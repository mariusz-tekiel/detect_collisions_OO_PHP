<?php
declare(strict_types=1);

namespace App\Geometry;

final class Point extends Shape
{
    public function __construct(
        public readonly float $x,
        public readonly float $y,
        string $name = 'Point'
    ) {
        parent::__construct($name);
    }
}
