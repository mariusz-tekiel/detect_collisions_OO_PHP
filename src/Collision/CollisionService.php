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

    public static function triangleCircle(Triangle $t, Circle $c): bool
    {
        // 1. Środek okręgu wewnątrz trójkąta
        if (self::trianglePoint($t, new Point($c->cx, $c->cy))) {
            return true;
        }

        // 2. Okrąg przecina któryś z boków trójkąta
        $rSq = $c->r * $c->r;
        return
            self::segmentDistSq($c->cx, $c->cy, $t->x1, $t->y1, $t->x2, $t->y2) <= $rSq ||
            self::segmentDistSq($c->cx, $c->cy, $t->x2, $t->y2, $t->x3, $t->y3) <= $rSq ||
            self::segmentDistSq($c->cx, $c->cy, $t->x3, $t->y3, $t->x1, $t->y1) <= $rSq;
    }

    // Kwadrat odległości punktu (px,py) od odcinka (ax,ay)–(bx,by)
    private static function segmentDistSq(
        float $px, float $py,
        float $ax, float $ay,
        float $bx, float $by
    ): float {
        $dx = $bx - $ax; $dy = $by - $ay;
        $lenSq = $dx*$dx + $dy*$dy;
        if ($lenSq == 0.0) {
            return ($px-$ax)*($px-$ax) + ($py-$ay)*($py-$ay);
        }
        $t = \max(0.0, \min(1.0, (($px-$ax)*$dx + ($py-$ay)*$dy) / $lenSq));
        $cx = $px - ($ax + $t*$dx);
        $cy = $py - ($ay + $t*$dy);
        return $cx*$cx + $cy*$cy;
    }
}
