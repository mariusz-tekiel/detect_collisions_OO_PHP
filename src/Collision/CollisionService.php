<?php
declare(strict_types=1);

namespace App\Collision;

use App\Geometry\{Point, Circle, Rectangle, Triangle};

final class CollisionService
{
    public static function pointPoint(Point $a, Point $b): bool
    {
        return $a->x === $b->x && $a->y === $b->y;
    }

    public static function pointCircle(Point $p, Circle $c): bool
    {
        $dx = $p->x - $c->cx; $dy = $p->y - $c->cy;
        $dist = \sqrt($dx*$dx + $dy*$dy);
        return $dist <= $c->r;
    }

    public static function circleCircle(Circle $a, Circle $b): bool
    {
        $dx = $a->cx - $b->cx; $dy = $a->cy - $b->cy;
        $dist = \sqrt($dx*$dx + $dy*$dy);
        return $dist <= ($a->r + $b->r);
    }

    public static function pointRect(Point $p, Rectangle $r): bool
    {
        return (
            $p->x >= $r->x &&
            $p->x <= $r->x + $r->w &&
            $p->y >= $r->y &&
            $p->y <= $r->y + $r->h
        );
    }

    public static function rectRect(Rectangle $a, Rectangle $b): bool
    {
        return (
            $a->x + $a->w >= $b->x &&
            $a->x <= $b->x + $b->w &&
            $a->y + $a->h >= $b->y &&
            $a->y <= $b->y + $b->h
        );
    }

    public static function circleRect(Circle $c, Rectangle $r): bool
    {
        $testX = $c->cx; $testY = $c->cy;
        if ($c->cx < $r->x) $testX = $r->x;
        elseif ($c->cx > $r->x + $r->w) $testX = $r->x + $r->w;
        if ($c->cy < $r->y) $testY = $r->y;
        elseif ($c->cy > $r->y + $r->h) $testY = $r->y + $r->h;
        $dx = $c->cx - $testX; $dy = $c->cy - $testY;
        $dist = \sqrt($dx*$dx + $dy*$dy);
        return $dist <= $c->r;
    }

    public static function trianglePoint(Triangle $t, Point $p): bool
    {
        $areaOrig = \abs(($t->x2-$t->x1)*($t->y3-$t->y1) - ($t->x3-$t->x1)*($t->y2-$t->y1));
        $a1 = \abs(($t->x1-$p->x)*($t->y2-$p->y) - ($t->x2-$p->x)*($t->y1-$p->y));
        $a2 = \abs(($t->x2-$p->x)*($t->y3-$p->y) - ($t->x3-$p->x)*($t->y2-$p->y));
        $a3 = \abs(($t->x3-$p->x)*($t->y1-$p->y) - ($t->x1-$p->x)*($t->y3-$p->y));
        return ($a1 + $a2 + $a3) === $areaOrig;
    }
}
