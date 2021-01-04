<?php

namespace AhmadWaleed\LaravelSOQLBuilder\Object;

use Illuminate\Contracts\Support\Arrayable;

interface ObjectInterface extends Arrayable
{
    public static function sobject(): string;

    public static function robject(): string;

    public static function sofields(): array;

    public static function rofields(): array;
}
