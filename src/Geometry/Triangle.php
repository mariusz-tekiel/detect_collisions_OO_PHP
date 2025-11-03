<?php
declare(strict_types=1);

namespace App\Geometry;

final class Triangle extends Shape
{
    public function __construct(
        public readonly float $x1, public readonly float $y1,
        public readonly float $x2, public readonly float $y2,
        public readonly float $x3, public readonly float $y3,
        string $name = 'Triangle'
    ) {
        parent::__construct($name);
    }
}
