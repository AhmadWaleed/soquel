<?php

namespace AhmadWaleed\Soquel\Query;

class Builder
{
    protected array $fields = [];
    protected array $orders = [];
    protected string $limit = '';
    protected ?string $from = null;
    protected array $conditions = [];

    protected array $keywords = [
        'from' => 'FROM',
        'select' => 'SELECT',
    ];

    public function object(string $object): self
    {
        return $this->from($object);
    }

    public function select(string ...$fields): self
    {
        $this->fields = $fields;

        return $this;
    }

    public function selectSub(Builder $builder): self
    {
        $this->fields = array_merge($this->fields, ['('.$builder->toSOQL().')']);

        return $this;
    }

    public function addSelect(string ...$fields): self
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

        if ($value instanceof Builder) {
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

        $query = collect([$this->keywords['select'], $fields, $this->keywords['from'], $this->from, $where, $orderBy, $this->limit])
            ->filter()
            ->implode(' ');

        return trim($query);
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
