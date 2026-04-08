<?php

declare(strict_types = 1);

namespace Centrex\TallUi\Http\Controllers;

use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;

class SelectSearchController extends Controller
{
    private const CACHE_TTL = 300;

    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'q'    => ['nullable', 'string', 'max:255'],
        ]);

        $name = $validated['name'];
        $query = mb_strtolower(trim($validated['q'] ?? ''));

        /** @var array<string, array<string, string>> $allowList */
        $allowList = config('tallui.forms.searchable_models', []);

        if (!array_key_exists($name, $allowList)) {
            return response()->json([], 403);
        }

        $config = $allowList[$name];
        $modelClass = $config['model'];
        $labelColumn = $config['label'] ?? 'name';
        $valueColumn = $config['value'] ?? 'id';
        $scope = $config['scope'] ?? null;

        if (!class_exists($modelClass)) {
            return response()->json([], 422);
        }

        $cacheKey = $this->buildCacheKey($name, $modelClass, $labelColumn, $valueColumn, $scope, $query);
        $cacheStore = config('tallui.cache.store');

        if ($cacheStore !== null && $query !== '' && strlen($query) >= 2) {
            return response()->json(
                Cache::store($cacheStore)->remember($cacheKey, self::CACHE_TTL, fn (): array => $this->fetchResults(
                    $modelClass,
                    $labelColumn,
                    $valueColumn,
                    $scope,
                    $query,
                )),
            );
        }

        return response()->json($this->fetchResults($modelClass, $labelColumn, $valueColumn, $scope, $query));
    }

    private function buildCacheKey(
        string $name,
        string $modelClass,
        string $labelColumn,
        string $valueColumn,
        ?string $scope,
        string $query,
    ): string {
        return 'tallui:select:' . md5($name . ':' . $modelClass . ':' . $labelColumn . ':' . $valueColumn . ':' . ($scope ?? '') . ':' . $query);
    }

    private function fetchResults(
        string $modelClass,
        string $labelColumn,
        string $valueColumn,
        ?string $scope,
        string $query,
    ): array {
        /** @var \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model> $builder */
        $builder = $modelClass::query();

        if ($scope !== null) {
            $builder = $builder->{$scope}();
        }

        if ($query !== '') {
            $builder->whereRaw('LOWER(' . $labelColumn . ') LIKE ?', ['%' . $query . '%']);
        }

        $results = $builder->limit(50)->get([$valueColumn, $labelColumn]);

        return $results->map(fn ($row): array => [
            'value' => $row->{$valueColumn},
            'label' => $row->{$labelColumn},
        ])->values()->all();
    }
}
