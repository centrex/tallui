<?php

declare(strict_types = 1);

namespace Centrex\TallUi\Http\Controllers;

use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\{Cache, Schema};
use InvalidArgumentException;

class SelectSearchController extends Controller
{
    private const CACHE_TTL = 300;

    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'   => ['nullable', 'required_without:source', 'string'],
            'source' => ['nullable', 'required_without:name', 'string'],
            'q'      => ['nullable', 'string', 'max:255'],
        ]);

        $sourceKey = (string) ($validated['name'] ?? $validated['source']);
        $term = trim((string) ($validated['q'] ?? ''));
        $normalizedTerm = mb_strtolower($term);

        $config = $this->resolveSourceConfig($validated);

        if ($config === []) {
            return response()->json([], 403);
        }

        $modelClass = (string) ($config['model'] ?? '');
        $labelColumn = (string) ($config['label'] ?? 'name');
        $sublabelColumn = isset($config['sublabel']) ? (string) $config['sublabel'] : null;
        $valueColumn = (string) ($config['value'] ?? 'id');
        $scope = isset($config['scope']) ? (string) $config['scope'] : null;
        $searchColumns = array_values($config['search_columns'] ?? [$labelColumn]);
        $orderBy = (string) ($config['order_by'] ?? $labelColumn);
        $direction = strtolower((string) ($config['order_direction'] ?? 'asc')) === 'desc' ? 'desc' : 'asc';
        $limit = max(1, min(100, (int) ($config['limit'] ?? 50)));
        $minSearchLength = max(0, (int) ($config['min_search_length'] ?? 0));

        if (!class_exists($modelClass) || !is_subclass_of($modelClass, Model::class)) {
            return response()->json([], 422);
        }

        /** @var Model $model */
        $model = new $modelClass();
        $table = $model->getTable();

        try {
            $labelColumn = $this->assertSafeColumn($table, $labelColumn);
            $sublabelColumn = $sublabelColumn !== null && $sublabelColumn !== ''
                ? $this->assertSafeColumn($table, $sublabelColumn)
                : null;
            $valueColumn = $this->assertSafeColumn($table, $valueColumn);
            $orderBy = $this->assertSafeColumn($table, $orderBy);

            $searchColumns = collect($searchColumns)
                ->filter(fn ($column) => is_string($column) && $column !== '')
                ->map(fn (string $column) => $this->assertSafeColumn($table, $column))
                ->values()
                ->all();

            if ($searchColumns === []) {
                $searchColumns = [$labelColumn];
            }
        } catch (InvalidArgumentException) {
            return response()->json([], 422);
        }

        if ($normalizedTerm !== '' && mb_strlen($normalizedTerm) < $minSearchLength) {
            return response()->json([]);
        }

        $cacheStore = config('tallui.cache.store');
        $shouldCache = $cacheStore !== null && $normalizedTerm !== '' && mb_strlen($normalizedTerm) >= 2;

        if ($shouldCache) {
            $cacheKey = $this->buildCacheKey(
                $sourceKey,
                $modelClass,
                $labelColumn,
                $sublabelColumn,
                $valueColumn,
                $scope,
                $normalizedTerm,
                $searchColumns,
                $orderBy,
                $direction,
                $limit,
                $minSearchLength,
            );

            return response()->json(
                Cache::store($cacheStore)->remember(
                    $cacheKey,
                    self::CACHE_TTL,
                    fn (): array => $this->fetchResults(
                        $modelClass,
                        $labelColumn,
                        $sublabelColumn,
                        $valueColumn,
                        $scope,
                        $normalizedTerm,
                        $searchColumns,
                        $orderBy,
                        $direction,
                        $limit,
                    ),
                ),
            );
        }

        return response()->json(
            $this->fetchResults(
                $modelClass,
                $labelColumn,
                $sublabelColumn,
                $valueColumn,
                $scope,
                $normalizedTerm,
                $searchColumns,
                $orderBy,
                $direction,
                $limit,
            ),
        );
    }

    /**
     * @param  array{name?:string,source?:string}  $validated
     * @return array<string, mixed>
     */
    private function resolveSourceConfig(array $validated): array
    {
        if (isset($validated['source'])) {
            $config = Cache::get('tallui:select-source:' . $validated['source']);

            return is_array($config) ? $config : [];
        }

        /** @var array<string, array<string, mixed>> $allowList */
        $allowList = config('tallui.forms.searchable_models', []);
        $name = (string) ($validated['name'] ?? '');

        return $allowList[$name] ?? [];
    }

    private function buildCacheKey(
        string $name,
        string $modelClass,
        string $labelColumn,
        ?string $sublabelColumn,
        string $valueColumn,
        ?string $scope,
        string $query,
        array $searchColumns,
        string $orderBy,
        string $direction,
        int $limit,
        int $minSearchLength,
    ): string {
        return 'tallui:select:' . md5(json_encode([
            'name'              => $name,
            'model'             => $modelClass,
            'label'             => $labelColumn,
            'sublabel'          => $sublabelColumn,
            'value'             => $valueColumn,
            'scope'             => $scope,
            'query'             => $query,
            'search'            => $searchColumns,
            'order'             => $orderBy,
            'dir'               => $direction,
            'limit'             => $limit,
            'min_search_length' => $minSearchLength,
        ], JSON_THROW_ON_ERROR));
    }

    /**
     * @param  class-string<Model>  $modelClass
     * @param  array<int, string>  $searchColumns
     * @return array<int, array{value:mixed,label:string,sublabel:?string}>
     */
    private function fetchResults(
        string $modelClass,
        string $labelColumn,
        ?string $sublabelColumn,
        string $valueColumn,
        ?string $scope,
        string $query,
        array $searchColumns,
        string $orderBy,
        string $direction,
        int $limit,
    ): array {
        /** @var Builder<Model> $builder */
        $builder = $modelClass::query();

        if ($scope !== null && $scope !== '') {
            if (!method_exists($builder->getModel(), 'scope' . ucfirst($scope))) {
                return [];
            }

            $builder->{$scope}();
        }

        if ($query !== '') {
            $builder->where(function (Builder $queryBuilder) use ($searchColumns, $query): void {
                foreach ($searchColumns as $index => $column) {
                    $method = $index === 0 ? 'whereRaw' : 'orWhereRaw';
                    $wrappedColumn = $queryBuilder->getQuery()->grammar->wrap($column);

                    $queryBuilder->{$method}(
                        'LOWER(' . $wrappedColumn . ') LIKE ?',
                        ['%' . $query . '%']
                    );
                }
            });
        }

        $columns = array_values(array_filter([$valueColumn, $labelColumn, $sublabelColumn]));

        $results = $builder
            ->orderBy($orderBy, $direction)
            ->limit($limit)
            ->get($columns);

        return $results
            ->map(fn (Model $row): array => [
                'value'    => $row->getAttribute($valueColumn),
                'label'    => (string) $row->getAttribute($labelColumn),
                'sublabel' => $sublabelColumn !== null
                    ? (string) $row->getAttribute($sublabelColumn)
                    : null,
            ])
            ->values()
            ->all();
    }

    private function assertSafeColumn(string $table, string $column): string
    {
        if ($column === '' || str_contains($column, '.') || str_contains($column, '(') || str_contains($column, ')')) {
            throw new InvalidArgumentException('Invalid column.');
        }

        if (!Schema::hasColumn($table, $column)) {
            throw new InvalidArgumentException('Unknown column.');
        }

        return $column;
    }
}
