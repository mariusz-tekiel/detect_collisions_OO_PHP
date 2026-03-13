<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Geometry\{Point,Circle,Rectangle,Triangle};
use App\Collision\CollisionService as C;

final class CollisionServiceTest extends TestCase
{
    // ── pointPoint ────────────────────────────────────────────────────────────

    public function testPointPointSame(): void
    {
        $this->assertTrue(C::pointPoint(new Point(1, 2), new Point(1, 2)));
    }

    public function testPointPointDifferent(): void
    {
        $this->assertFalse(C::pointPoint(new Point(1, 2), new Point(2, 1)));
    }

    public function testPointPointOrigin(): void
    {
        $this->assertTrue(C::pointPoint(new Point(0, 0), new Point(0, 0)));
    }

    // ── pointCircle ───────────────────────────────────────────────────────────

    public function testPointCircleInside(): void
    {
        $this->assertTrue(C::pointCircle(new Point(0, 0), new Circle(0, 0, 5)));
    }

    public function testPointCircleOutside(): void
    {
        $this->assertFalse(C::pointCircle(new Point(6, 0), new Circle(0, 0, 5)));
    }

    public function testPointCircleOnBoundary(): void
    {
        // punkt dokładnie na okręgu: dist == r
        $this->assertTrue(C::pointCircle(new Point(5, 0), new Circle(0, 0, 5)));
    }

    // ── circleCircle ──────────────────────────────────────────────────────────

    public function testCircleCircleOverlap(): void
    {
        $this->assertTrue(C::circleCircle(new Circle(0, 0, 5), new Circle(8, 0, 5)));
    }

    public function testCircleCircleNoOverlap(): void
    {
        $this->assertFalse(C::circleCircle(new Circle(0, 0, 5), new Circle(11, 0, 5)));
    }

    public function testCircleCircleTouching(): void
    {
        // okręgi stykają się w jednym punkcie: dist == r1 + r2
        $this->assertTrue(C::circleCircle(new Circle(0, 0, 5), new Circle(10, 0, 5)));
    }

    public function testCircleCircleOneInsideOther(): void
    {
        $this->assertTrue(C::circleCircle(new Circle(0, 0, 10), new Circle(0, 0, 3)));
    }

    // ── pointRect ─────────────────────────────────────────────────────────────

    public function testPointRectInside(): void
    {
        $this->assertTrue(C::pointRect(new Point(5, 5), new Rectangle(0, 0, 10, 10)));
    }

    public function testPointRectOutside(): void
    {
        $this->assertFalse(C::pointRect(new Point(-1, 5), new Rectangle(0, 0, 10, 10)));
    }

    public function testPointRectOnEdge(): void
    {
        // punkt dokładnie na krawędzi prostokąta
        $this->assertTrue(C::pointRect(new Point(0, 5), new Rectangle(0, 0, 10, 10)));
        $this->assertTrue(C::pointRect(new Point(10, 5), new Rectangle(0, 0, 10, 10)));
        $this->assertTrue(C::pointRect(new Point(5, 0), new Rectangle(0, 0, 10, 10)));
        $this->assertTrue(C::pointRect(new Point(5, 10), new Rectangle(0, 0, 10, 10)));
    }

    public function testPointRectOnCorner(): void
    {
        $this->assertTrue(C::pointRect(new Point(0, 0), new Rectangle(0, 0, 10, 10)));
        $this->assertTrue(C::pointRect(new Point(10, 10), new Rectangle(0, 0, 10, 10)));
    }

    // ── rectRect ──────────────────────────────────────────────────────────────

    public function testRectRectOverlap(): void
    {
        $this->assertTrue(C::rectRect(new Rectangle(0, 0, 10, 10), new Rectangle(5, 5, 4, 4)));
    }

    public function testRectRectNoOverlap(): void
    {
        $this->assertFalse(C::rectRect(new Rectangle(0, 0, 10, 10), new Rectangle(11, 0, 2, 2)));
    }

    public function testRectRectSharedEdge(): void
    {
        // prostokąty stykają się krawędzią
        $this->assertTrue(C::rectRect(new Rectangle(0, 0, 10, 10), new Rectangle(10, 0, 5, 5)));
    }

    public function testRectRectOneInsideOther(): void
    {
        $this->assertTrue(C::rectRect(new Rectangle(0, 0, 10, 10), new Rectangle(2, 2, 3, 3)));
    }

    // ── circleRect ────────────────────────────────────────────────────────────

    public function testCircleRectCenterInside(): void
    {
        $this->assertTrue(C::circleRect(new Circle(5, 5, 3), new Rectangle(0, 0, 10, 10)));
    }

    public function testCircleRectNoOverlap(): void
    {
        $this->assertFalse(C::circleRect(new Circle(-5, -5, 2), new Rectangle(0, 0, 3, 3)));
    }

    public function testCircleRectTouchingEdge(): void
    {
        // okrąg dotyka krawędzi prostokąta od zewnątrz
        $this->assertTrue(C::circleRect(new Circle(-3, 5, 3), new Rectangle(0, 0, 10, 10)));
    }

    public function testCircleRectJustOutside(): void
    {
        $this->assertFalse(C::circleRect(new Circle(-4, 5, 3), new Rectangle(0, 0, 10, 10)));
    }

    // ── trianglePoint ─────────────────────────────────────────────────────────

    public function testTrianglePointInside(): void
    {
        $tri = new Triangle(0, 0, 10, 0, 0, 10);
        $this->assertTrue(C::trianglePoint($tri, new Point(1, 1)));
    }

    public function testTrianglePointOutside(): void
    {
        $tri = new Triangle(0, 0, 10, 0, 0, 10);
        $this->assertFalse(C::trianglePoint($tri, new Point(6, 6)));
    }

    public function testTrianglePointOnVertex(): void
    {
        $tri = new Triangle(0, 0, 10, 0, 0, 10);
        $this->assertTrue(C::trianglePoint($tri, new Point(0, 0)));
        $this->assertTrue(C::trianglePoint($tri, new Point(10, 0)));
        $this->assertTrue(C::trianglePoint($tri, new Point(0, 10)));
    }

    // ── wyjątki ───────────────────────────────────────────────────────────────

    public function testCircleNegativeRadius(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Circle(0, 0, -1);
    }

    public function testRectangleNegativeWidth(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Rectangle(0, 0, -5, 10);
    }

    public function testRectangleNegativeHeight(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Rectangle(0, 0, 5, -10);
    }
}
