<?php

namespace AhmadWaleed\Soquel\Object;

use Illuminate\Support\Arr;
use AhmadWaleed\Soquel\Soquel;
use Illuminate\Support\Collection;
use AhmadWaleed\Soquel\Query\Builder;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Traits\ForwardsCalls;
use Omniphx\Forrest\Providers\Laravel\Facades\Forrest;

abstract class BaseObject implements Arrayable
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
        Soquel::authenticate();

        $this->builder = new ObjectBuilder($this, new Builder, app('forrest'));

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
        return $this->sobject ?? class_basename(get_class($this));
    }

    public function __set($field, $value): void
    {
        $accessor = 'set'.ucfirst($field).'Attribute';
        if (method_exists($this, $accessor)) {
            $value = $this->{$accessor}($value);
        }

        $this->setAttribute($field, $value);
    }

    public function __get(string $field)
    {
        $value = isset($this->attributes[$field]) ? $this->attributes[$field] : null;

        if (is_null($value)) {
            return $value;
        }

        $accessor = 'get'.ucfirst($field).'Attribute';
        if (method_exists($this, $accessor)) {
            return $this->{$accessor}($value);
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

        $this->setRelationAttributes($attributes);

        return $this;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getWritableAttributes(array $excludes = []): array
    {
        if (property_exists($this, 'externalIdKey')) {
            array_push($excludes, $this->{'externalIdKey'});
        }
        
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
        Soquel::authenticate();

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
        Soquel::authenticate();

        $endpoint = property_exists($this, 'externalIdKey')
            ? $this->sobject().'/'.$this->externalIdKey.'/'.$this->attributes[$this->externalIdKey]
            : $this->sobject().'/'.$this->attributes['Id'];

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

    public function toArray(): array
    {
        return $this->getAttributes();
    }

    private function endpoint(): string
    {
        return Arr::has($this->attributes, 'Id') ? $this->sobject().'/'.$this->Id : $this->sobject();
    }

    private function method(): string
    {
        return Arr::has($this->attributes, 'Id') ? 'patch' : 'post';
    }

    private function setRelationAttributes(array $attributes): void
    {
        foreach ($this->builder->getRelations() as $name) {
            /** @var Relation $relation */
            $relation = $this->{$name}();

            if (! isset($attributes[$relation->getRelationship()])) {
                continue;
            }

            $model = $relation->getModel();

            if ($relation instanceof ParentRelation) {
                $model->fill($attributes[$relation->getRelationship()]);
                $this->setAttribute(str_replace($relation->getRelationship().'.', '', $name), $model);
            }

            if ($relation instanceof ChildRelation) {
                $models = collect($attributes[$relation->getRelationship()]['records'])
                    ->map(function (array $item) use ($model) {
                        $class = get_class($model);
                        $newModel = new $class;

                        return $newModel->fill($item);
                    });

                $this->setAttribute($name, $models);
            }
        }
    }
}
