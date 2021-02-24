<?php

namespace AhmadWaleed\Soquel\Object;

use Omniphx\Forrest\Client;
use Illuminate\Support\Collection;
use AhmadWaleed\Soquel\Query\Builder;
use Illuminate\Support\Traits\ForwardsCalls;
use AhmadWaleed\Soquel\Exceptions\NotFoundException;

/** @mixin \AhmadWaleed\Soquel\Query\Builder */
class ObjectBuilder
{
    use ForwardsCalls;

    protected Builder $query;
    protected BaseObject $object;
    protected array $relations = [];
    protected Client $client;

    public function __construct(BaseObject $object, Builder $query, Client $client)
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
        $this->where('Id', '=', $id);

        return $this->first();
    }

    public function first(): BaseObject
    {
        $this->query->limit(1);

        $records = $this->get();

        if ($records->isEmpty()) {
            throw new NotFoundException;
        }

        return $records->first();
    }

    /** @return Collection|BaseObject[] */
    public function get(): Collection
    {
        return collect($this->client->query($this->toSOQL())['records'])
            ->map(function (array $attributes) {
                $object = clone $this->object;

                return $object->fill($attributes);
            });
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

    public function getRelations(): array
    {
        return $this->relations;
    }
}
