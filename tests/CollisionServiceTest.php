<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Geometry\{Point,Circle,Rectangle,Triangle};
use App\Collision\CollisionService as C;

final class CollisionServiceTest extends TestCase
{
    public function testPointPoint(): void
    {
        $this->assertTrue(C::pointPoint(new Point(1,2), new Point(1,2)));
        $this->assertFalse(C::pointPoint(new Point(1,2), new Point(2,1)));
    }

    public function testPointCircle(): void
    {
        $this->assertTrue(C::pointCircle(new Point(0,0), new Circle(0,0,5)));
        $this->assertFalse(C::pointCircle(new Point(6,0), new Circle(0,0,5)));
    }

    public function testCircleCircle(): void
    {
        $this->assertTrue(C::circleCircle(new Circle(0,0,5), new Circle(8,0,5)));
        $this->assertFalse(C::circleCircle(new Circle(0,0,5), new Circle(11,0,5)));
    }

    public function testPointRect(): void
    {
        $rect = new Rectangle(0,0,10,10);
        $this->assertTrue(C::pointRect(new Point(5,5), $rect));
        $this->assertFalse(C::pointRect(new Point(-1,5), $rect));
    }

    public function testRectRect(): void
    {
        $this->assertTrue(C::rectRect(new Rectangle(0,0,10,10), new Rectangle(5,5,4,4)));
        $this->assertFalse(C::rectRect(new Rectangle(0,0,10,10), new Rectangle(11,0,2,2)));
    }

    public function testCircleRect(): void
    {
        $this->assertTrue(C::circleRect(new Circle(5,5,3), new Rectangle(0,0,10,10)));
        $this->assertFalse(C::circleRect(new Circle(-5,-5,2), new Rectangle(0,0,3,3)));
    }

    public function testTrianglePoint(): void
    {
        $tri = new Triangle(0,0, 10,0, 0,10);
        $this->assertTrue(C::trianglePoint($tri, new Point(1,1)));
        $this->assertFalse(C::trianglePoint($tri, new Point(6,6)));
    }
}
