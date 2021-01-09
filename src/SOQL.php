<?php

namespace AhmadWaleed\Soquel;

use AhmadWaleed\Soquel\Query\Builder;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \AhmadWaleed\Soquel\Query\Builder object(string $object)
 * @see \AhmadWaleed\Soquel\Query\Builder
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
