<?php

namespace AhmadWaleed\LaravelSOQLBuilder\Object;

use Illuminate\Support\Collection;
use Illuminate\Support\Traits\ForwardsCalls;
use AhmadWaleed\LaravelSOQLBuilder\Query\Builder;
use AhmadWaleed\LaravelSOQLBuilder\Query\QueryableInterface;

/** @mixin \AhmadWaleed\LaravelSOQLBuilder\Query\Builder */
class ObjectBuilder
{
    use ForwardsCalls;

    protected Builder $query;
    protected BaseObject $object;
    protected array $relations = [];
    protected QueryableInterface $client;

    public function __construct(BaseObject $object, Builder $query, QueryableInterface $client)
    {
        $this->object = $object;
        $this->query = $query;
        $this->client = $client;
    }

    public function with(string ...$relations): self
    {
        $this->relations = array_merge($this->relations, $relations);

        return $this;
    }

    public function toSOQL(): string
    {
        foreach ($this->relations as $relation) {
            if (! method_exists($this->object, $relation)) {
                throw new \Exception($relation.' does not exist on '.get_class($this));
            }

            $relationship = $this->object->{$relation}();

            if ($relationship instanceof ChildRelation) {
                $this->query->selectSub($relationship->getBuilder()->getQuery());
            }
        }

        return $this->query->toSOQL();
    }

    public function find(string $id): BaseObject
    {
        $this->query()->where('Id', '=', $id);

        return $this->first();
    }

    public function first(): BaseObject
    {
        $this->query->limit(1);

        return $this->object::create($this->get()->first()->toArray());
    }

    /** @return Collection|BaseObject[] */
    public function get(): Collection
    {
        return collect($this->client->query($this->query->toSOQL()))
            ->map(fn (array $object) => $this->object::create($object));
    }

    public function getQuery(): Builder
    {
        return $this->query;
    }

    /** @return mixed */
    public function __call(string $method, array $parameters)
    {
        $result = $this->forwardCallTo($this->query, $method, $parameters);

        if (! $result instanceof Builder) {
            return $result;
        }

        return $this;
    }

    public function getObject(): BaseObject
    {
        return $this->object;
    }
}
