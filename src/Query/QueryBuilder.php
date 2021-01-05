<?php

namespace AhmadWaleed\LaravelSOQLBuilder\Query;

use Illuminate\Support\Collection;
use AhmadWaleed\LaravelSOQLBuilder\Object\Relationship;
use AhmadWaleed\LaravelSOQLBuilder\Object\ChildRelation;
use AhmadWaleed\LaravelSOQLBuilder\Object\ParentRelation;
use AhmadWaleed\LaravelSOQLBuilder\Object\SalesForceObject;

class QueryBuilder
{
    protected array $fields = [];
    protected array $orders = [];
    protected string $limit = '';
    protected ?string $from = null;
    protected array $conditions = [];
    protected QueryableInterface $client;
    protected SalesForceObject $object;

    public function __construct(SalesForceObject $object, QueryableInterface $client)
    {
        $this->object = $object;
        $this->client = $client;
        $this->from = $object::object();
        $this->fields = $object::fields();
    }

    public function select(string ...$fields): self
    {
        $this->fields = $fields;

        return $this;
    }

    public function with(string ...$relations): self
    {
        foreach ($relations as $relation) {
            $this->addRelation($relation);
        }

        return $this;
    }

    private function addRelation(string $relation): SalesForceObject
    {
        if (! method_exists($this->object, $relation)) {
            throw new \Exception($relation.' does not exist on '.get_class($this->object));
        }

        $relationship = $this->object->{$relation}();

        if (! $relationship instanceof Relationship) {
            throw new \UnexpectedValueException('Relationship method must return instance of '.Relationship::class);
        }

        if ($relationship instanceof ParentRelation) {
            $this->addSelect($relationship->build()->fields);
        }

        if ($relationship instanceof ChildRelation) {
            $this->addSubSelect($relationship->build());
        }

        return $relationship->object();
    }

    public function addSubSelect(QueryBuilder $builder): self
    {
        $this->fields = array_merge($this->fields, ['('.$builder->toSOQL().')']);

        return $this;
    }

    public function addSelect(array $fields): self
    {
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

    public function orWhere(string $field, string $operator, $value): self
    {
        $this->where($field, $operator, $value, 'OR');

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

    public function from(string $from): self
    {
        $this->from = $from;

        return $this;
    }

    public function toSOQL(): string
    {
        $fields = implode(', ', $this->fields);
        $orderBy = implode(' ', $this->orders);
        $where = implode(' ', $this->conditions);

        $query = collect(['SELECT', $fields, 'FROM', $this->from, $orderBy, $where, $this->limit])
            ->filter()
            ->implode(' ');

        return trim($query);
    }

    /** @return Collection|SalesForceObject[] */
    public function get(): Collection
    {
        return collect($this->client->query($this->toSOQL()))
            ->map(fn (array $object) => $this->object::create($object));
    }

    public function first(): SalesForceObject
    {
        $this->limit(1);

        return $this->object::create($this->client->query($this->toSOQL())[0]);
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

    private function wrapQuotes($value): string
    {
        return is_string($value) ? "'{$value}'" : $value;
    }
}
