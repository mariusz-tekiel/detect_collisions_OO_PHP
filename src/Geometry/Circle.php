<?php
declare(strict_types=1);

namespace App\Geometry;

final class Circle extends Shape
{
    public function __construct(
        public readonly float $cx,
        public readonly float $cy,
        public readonly float $r,
        string $name = 'Circle'
    ) {
        parent::__construct($name);
        if ($r < 0) { throw new \InvalidArgumentException('Radius must be >= 0'); }
    }
}
