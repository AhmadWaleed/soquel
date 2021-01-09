<?php

namespace AhmadWaleed\Soquel\Object;

use Illuminate\Contracts\Support\Arrayable;

interface ObjectInterface extends Arrayable
{
    public static function object(): string;

    public static function fields(): array;
}
