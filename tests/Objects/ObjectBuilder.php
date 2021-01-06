<?php

namespace AhmadWaleed\LaravelSOQLBuilder\Tests\Objects;

use Illuminate\Support\Collection;
use AhmadWaleed\LaravelSOQLBuilder\SOQL;
use Illuminate\Support\Traits\ForwardsCalls;
use AhmadWaleed\LaravelSOQLBuilder\Query\Builder;
use AhmadWaleed\LaravelSOQLBuilder\Object\BaseObject;
use AhmadWaleed\LaravelSOQLBuilder\Object\Relationship;
use AhmadWaleed\LaravelSOQLBuilder\Object\ChildRelation;
use AhmadWaleed\LaravelSOQLBuilder\Object\ParentRelation;
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

        foreach ($relations as $relation) {
            $this->selectRelation($relation);
        }

        return $this;
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

    private function selectRelation(string $relation): self
    {
        if (! method_exists($this->object, $relation)) {
            throw new \Exception($relation.' does not exist on '.get_class($this));
        }

        $relationship = $this->object->{$relation}();

        if (! $relationship instanceof Relationship) {
            throw new \UnexpectedValueException('Relationship method must return instance of '.Relationship::class);
        }

        if ($relationship instanceof ParentRelation) {
            $this->query->addSelect(...$relationship->rfields());
        }

        if ($relationship instanceof ChildRelation) {
            $this->query->selectSub(
                SOQL::object($relationship->relation())->select(...$relationship->rfields())
            );
        }

        return $this;
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
}
