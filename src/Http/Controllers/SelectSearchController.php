<?php

declare(strict_types = 1);

namespace Centrex\TallUi\Http\Controllers;

use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Routing\Controller;

class SelectSearchController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'q'    => ['nullable', 'string', 'max:255'],
        ]);

        $name = $validated['name'];
        $query = $validated['q'] ?? '';

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

        /** @var \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model> $builder */
        $builder = $modelClass::query();

        if ($scope !== null) {
            $builder = $builder->{$scope}();
        }

        if ($query !== '') {
            $builder->where($labelColumn, 'like', '%' . $query . '%');
        }

        $results = $builder->limit(50)->get([$valueColumn, $labelColumn]);

        $items = $results->map(fn ($row): array => [
            'value' => $row->{$valueColumn},
            'label' => $row->{$labelColumn},
        ])->values();

        return response()->json($items);
    }
}
