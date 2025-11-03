<?php
declare(strict_types=1);

namespace App\Geometry;

abstract class Shape
{
    public function __construct(public readonly string $name) {}
}
