<?php

namespace AhmadWaleed\Soquel\Object;

use Illuminate\Support\Arr;
use AhmadWaleed\Soquel\SOQLClient;
use Illuminate\Support\Collection;
use AhmadWaleed\Soquel\Query\Builder;
use Illuminate\Support\Traits\ForwardsCalls;
use Omniphx\Forrest\Providers\Laravel\Facades\Forrest;

abstract class BaseObject
{
    use HasRelationship, ForwardsCalls;

    protected ObjectBuilder $builder;
    protected string $sobject;
    protected array $with = [];
    protected array $fields = [];
    protected array $attributes = [];
    protected array $readOnly = ['Id'];

    public function __construct(array $attributes = [])
    {
        $this->sobject = class_basename(get_class($this));
        $this->attributes = $attributes;
        $this->builder = $this->newQuery();
    }

    public static function new(): self
    {
        return new static();
    }

    public function query(): ObjectBuilder
    {
        return $this->builder;
    }

    public function newQuery(): ObjectBuilder
    {
        SOQLClient::authenticate();

        $this->builder = new ObjectBuilder($this, new Builder, config('soquel.client'));

        $this->builder
            ->object($this->sobject())
            ->select(...$this->fields());

        return $this->builder;
    }

    /** @return mixed */
    public function __call(string $method, array $parameters)
    {
        return $this->forwardCallTo($this->builder, $method, $parameters);
    }

    public function setAttribute(string $key, $value): BaseObject
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    public function sobject(): string
    {
        return $this->sobject;
    }

    public function __set($field, $value): void
    {
        $this->setAttribute($field, $value);
    }

    public function __get(string $field)
    {
        if (! isset($this->attributes[$field])) {
            throw new \InvalidArgumentException("No such field {$field} exists.");
        }

        $accessor = 'get'.ucfirst($field).'Attribute';
        if (method_exists($this, $accessor)) {
            return $this->{$accessor}($this->attributes[$field]);
        }

        return $this->attributes[$field];
    }

    public function getOriginal(string $field)
    {
        if (! isset($this->attributes[$field])) {
            throw new \InvalidArgumentException("No such field {$field} exists.");
        }

        return $this->attributes[$field];
    }

    public function fields(): array
    {
        return array_merge($this->readOnly, $this->fields);
    }

    public function fill(array $attributes): BaseObject
    {
        foreach ($attributes as $field => $value) {
            if (in_array($field, $this->fields())) {
                $this->setAttribute($field, $value);
            }
        }

        return $this;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getWritableAttributes(array $excludes = []): array
    {
        return Arr::except($this->attributes, array_merge($this->readOnly, $excludes));
    }

    public static function create(array $attributes): BaseObject
    {
        return (new static)->fill($attributes)->save();
    }

    public function update(array $body): BaseObject
    {
        return $this->fill($body)->save();
    }

    public function save(): BaseObject
    {
        SOQLClient::authenticate();

        try {
            $response = Forrest::sobjects($this->endpoint(), [
                'method' => $this->method(),
                'body' => $this->getWritableAttributes(),
            ]);

            if (isset($response['success'])) {
                try {
                    return $this->newQuery()->find($response['id']);
                } catch (\Exception $exception) {
                    if (isset($response['id'])) {
                        $this->Id = $response['id'];
                    }
                }
            }

            return $this;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public static function saveMany(Collection $objects): void
    {
        $objects->each(function ($object) {
            if ($object instanceof BaseObject) {
                $object->save();
            }

            if (is_array($object)) {
                static::create($object);
            }
        });
    }

    public function upsert(): BaseObject
    {
        SOQLClient::authenticate();

        if (! property_exists($this, 'externalIdKey')) {
            throw new \LogicException("No externalIdKey exists on object.");
        }

        $endpoint = $this->sobject.'/'.$this->externalIdKey.'/'.$this->attributes[$this->externalIdKey];

        try {
            $response = Forrest::sobjects($endpoint, [
                'method' => 'patch',
                'body' => $this->getWritableAttributes(),
            ]);

            if (isset($response['success'])) {
                try {
                    return $this->newQuery()->find($response['id']);
                } catch (\Exception $exception) {
                    if (isset($response['id'])) {
                        $this->Id = $response['id'];
                    }
                }
            }

            return $this;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    private function endpoint(): string
    {
        return Arr::has($this->attributes, 'Id') ? $this->sobject.'/'.$this->Id : $this->sobject;
    }

    private function method(): string
    {
        return Arr::has($this->attributes, 'Id') ? 'patch' : 'post';
    }
}
