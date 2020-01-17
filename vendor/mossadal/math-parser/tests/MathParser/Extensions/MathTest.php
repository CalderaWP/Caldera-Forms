<?php

use MathParser\Extensions\Math;
use PHPUnit\Framework\TestCase;

class MathTest extends TestCase
{
    public function testGcd()
    {
        $this->assertEquals(4, Math::gcd(8, 12));
        $this->assertEquals(4, Math::gcd(12, 8));
        $this->assertEquals(1, Math::gcd(12, 7));

        // Edge cases

        $this->assertEquals(5, Math::gcd(0, 5));
        $this->assertEquals(0, Math::gcd(0, 0));
        $this->assertEquals(-2, Math::gcd(2, -2));
        $this->assertEquals(2, Math::gcd(-2, -2));
    }

    public function testLogGamma()
    {
        $this->assertEquals(857.9336698, Math::logGamma(200), '', 3e-7);
        $this->assertEquals(log(120), Math::logGamma(6), '', 3e-9);
        $this->assertEquals(3.9578139676187, Math::logGamma(5.5), '', 3e-9);

    }

    public function testFactorial()
    {
        $this->assertEquals(1, Math::Factorial(0));
        $this->assertEquals(6, Math::Factorial(3));
        $this->assertEquals(362880, Math::Factorial(9));
    }

    public function testSemiFactorial()
    {
        $this->assertEquals(1, Math::SemiFactorial(0));
        $this->assertEquals(1, Math::SemiFactorial(1));
        $this->assertEquals(2, Math::SemiFactorial(2));
        $this->assertEquals(3, Math::SemiFactorial(3));
        $this->assertEquals(8, Math::SemiFactorial(4));
        $this->assertEquals(15, Math::SemiFactorial(5));
        $this->assertEquals(48, Math::SemiFactorial(6));
        $this->assertEquals(105, Math::SemiFactorial(7));
    }

}
