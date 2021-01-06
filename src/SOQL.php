<?php

namespace AhmadWaleed\LaravelSOQLBuilder;

use Illuminate\Support\Facades\Facade;
use AhmadWaleed\LaravelSOQLBuilder\Query\Builder;

/**
 * @method static \AhmadWaleed\LaravelSOQLBuilder\Query\Builder object(string $object)
 * @see \AhmadWaleed\LaravelSOQLBuilder\Query\Builder
 */
class SOQL extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return new Builder;
    }
}
