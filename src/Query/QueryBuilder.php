<?php

namespace AhmadWaleed\LaravelSOQLBuilder\Query;

use AhmadWaleed\LaravelSOQLBuilder\Object\SalesForceObject;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Omniphx\Forrest\Providers\Laravel\Facades\Forrest;

class QueryBuilder
{
    protected SalesForceObject $object;
    protected array $fields = [];
    protected array $orders = [];
    protected array $conditions = [];
    protected string $limit = '';

    public function __construct(SalesForceObject $object)
    {
        $this->object = $object;

        $this->fields = $object::rofields();
    }

    public function select(string ...$fields): self
    {
        $this->fields = $fields;

        return $this;
    }

    public function with(string ...$relations): self
    {
        foreach ($relations as $relation) {
            $class = app()->basePath('objects').Str::studly($relation);
            $this->addSelect($class::rofields(), $class::robject());
        }

        return $this;
    }

    public function addSubSelect(QueryBuilder $builder, string $object): self
    {
        $this->fields = array_merge($this->fields, ['('.$builder->toSOQL($object).')']);

        return $this;
    }

    public function addSelect(array $fields, string $relation = ''): self
    {
        $fields = !empty($relation) ? $this->prefixWithRelation($fields, $relation) : $fields;

        $this->fields = array_merge($this->fields, $fields);

        return $this;
    }

    public function orderBy(string $field, string $order = 'DESC'): self
    {
        if (empty($this->orders)) {
            $this->orders[] = 'ORDER BY';
        }

        $this->orders[] = "{$field} {$order}";

        return $this;
    }

    public function find(string $id): SalesForceObject
    {
        return $this->where('Id', '=', $id)->first();
    }

    public function whereNull(string $field, string $boolean = 'AND'): self
    {
        $this->normalizeWhereClause($boolean);

        $this->conditions[] = "{$field} = null";

        return $this;
    }

    public function whereNotNull(string $field, string $boolean = 'AND'): self
    {
        $this->normalizeWhereClause($boolean);

        $this->conditions[] = "{$field} != null";

        return $this;
    }

    public function where(string $field, string $operator, $value, string $boolean = 'AND'): self
    {
        $this->normalizeWhereClause($boolean);

        $this->conditions[] = "{$field} {$operator} {$this->wrapQuotes($value)}";

        return $this;
    }

    public function whereIn(string $field, $value, string $boolean = 'AND'): self
    {
        if (empty($value)) {
            return $this;
        }

        $this->normalizeWhereClause($boolean);

        if ($value instanceof QueryBuilder) {
            $condition = $value->toSOQL();
        } else {
            $condition = collect($value)
                ->map(fn ($value) => $this->wrapQuotes($value))
                ->implode(', ');
        }

        $this->conditions[] = "{$field} IN ({$condition})";

        return $this;
    }

    public function when(bool $value, callable $callback): self
    {
        if ($value) {
            return $callback($this);
        }

        return $this;
    }

    public function whereRaw(string $raw, $boolean = 'AND'): self
    {
        $this->normalizeWhereClause($boolean);

        $this->conditions[] = $raw;

        return $this;
    }

    public function limit(int $limit = 2000): self
    {
        $this->limit = "LIMIT {$limit}";

        return $this;
    }

    public function toSOQL(?string $object = null): string
    {
        $fields = implode(', ', $this->fields);
        $orderBy = implode(' ', $this->orders);
        $where = implode(' ', $this->conditions);
        $object = $object ?: $this->object::sobject();

        return trim(
            collect(['SELECT', $fields, 'FROM', $object, $orderBy, $where, $this->limit])->implode(' ')
        );
    }

    /** @return Collection|SalesForceObject[] */
    public function get(): Collection
    {
        return collect(Forrest::query($this->toSOQL())['records'])
            ->map(fn (array $object) => $this->object::createFromArray($object));
    }

    public function first(): SalesForceObject
    {
        $this->limit(1);

        return $this->object::createFromArray(
            Forrest::query($this->toSOQL())['records'][0]
        );
    }

    private function normalizeWhereClause(string $boolean): void
    {
        if (empty($this->conditions)) {
            $this->conditions[] = 'WHERE';
        }

        if (count($this->conditions) >= 2) {
            $this->conditions[] = $boolean;
        }
    }

    private function prefixWithRelation(array $fields, string $relation): array
    {
        return collect($fields)
            ->map(fn (string $field) => "{$relation}.{$field}")
            ->toArray();
    }

    private function wrapQuotes($value): string
    {
        return is_string($value) ? "'{$value}'" : $value;
    }
}
