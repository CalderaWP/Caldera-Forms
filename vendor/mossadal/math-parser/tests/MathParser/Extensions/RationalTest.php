<?php

use MathParser\Exceptions\DivisionByZeroException;
use MathParser\Exceptions\SyntaxErrorException;
use MathParser\Extensions\Rational;
use PHPUnit\Framework\TestCase;

class RationalTest extends TestCase
{
    public function testRationalFromString()
    {
        $x = Rational::parse('3');
        $this->assertEquals(3, $x->p);
        $this->assertEquals(1, $x->q);
        $this->assertEquals('3', "$x");

        $x = Rational::parse('1/2');
        $this->assertEquals(1, $x->p);
        $this->assertEquals(2, $x->q);
        $this->assertEquals('1/2', "$x");

        $x = Rational::parse('-1/2');
        $this->assertEquals(-1, $x->p);
        $this->assertEquals(2, $x->q);
        $this->assertEquals('-1/2', "$x");

        $x = Rational::parse('2/4');
        $this->assertEquals(1, $x->p);
        $this->assertEquals(2, $x->q);
        $this->assertEquals('1/2', "$x");

        $x = Rational::parse('2/4', false);
        $this->assertEquals(2, $x->p);
        $this->assertEquals(4, $x->q);
        $this->assertEquals('2/4', "$x");
    }

    public function testRationalFromStingDivisionByZero()
    {
        $this->expectException(DivisionByZeroException::class);
        $x = Rational::parse('1/0');
    }

    public function testFromFloat()
    {
        $x = Rational::fromFloat(0.33333333333);
        $this->assertEquals('1/3', "$x");

        $x = Rational::fromFloat("0.33333333333");
        $this->assertEquals('1/3', "$x");

        $x = Rational::fromFloat("0,33333333333");
        $this->assertEquals('1/3', "$x");
    }

    public function testParseFailure()
    {
        $this->expectException(SyntaxErrorException::class);
        $x = Rational::parse('sdf');
    }

    public function testNan()
    {
        $this->expectException(SyntaxErrorException::class);
        $x = Rational::parse('1/2/3');
    }

    public function testNan2()
    {
        $x = new Rational(NAN, 1);
        $this->assertTrue($x->is_nan());

        $x = new Rational(1, 1);
        $x->q = 0;
        $this->assertTrue($x->is_nan());
    }

    public function testCanDoAritmethic()
    {
        $x = new Rational(1, 2);
        $y = new Rational(2, 3);

        $this->assertEquals(new Rational(7, 6), Rational::add($x, $y));
        $this->assertEquals(new Rational(-1, 6), Rational::sub($x, $y));
        $this->assertEquals(new Rational(1, 3), Rational::mul($x, $y));
        $this->assertEquals(new Rational(3, 4), Rational::div($x, $y));
    }

    public function testDivisionByZero()
    {
        $x = Rational::parse('1/2');
        $y = Rational::parse('0');

        $this->expectException(DivisionByZeroException::class);
        $z = Rational::div($x, $y);
    }
}
