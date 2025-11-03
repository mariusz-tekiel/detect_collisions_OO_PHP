<?php
declare(strict_types=1);

namespace App\Geometry;

final class Rectangle extends Shape
{
    public function __construct(
        public readonly float $x,
        public readonly float $y,
        public readonly float $w,
        public readonly float $h,
        string $name = 'Rectangle'
    ) {
        parent::__construct($name);
        if ($w < 0 || $h < 0) { throw new \InvalidArgumentException('Width/height must be >= 0'); }
    }

    public function right(): float { return $this->x + $this->w; }
    public function top(): float { return $this->y + $this->h; }
}
