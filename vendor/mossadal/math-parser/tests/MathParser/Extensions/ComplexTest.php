<?php

use MathParser\Exceptions\SyntaxErrorException;
use MathParser\Extensions\Complex;
use PHPUnit\Framework\TestCase;

class ComplexTest extends TestCase
{
    public function testComplexFromString()
    {
        $z = Complex::parse('2+5i');
        $this->assertEquals(2, $z->r());
        $this->assertEquals(5, $z->i());
        $this->assertEquals('2+5i', "$z");

        // Imaginary part missing coefficient

        $z = Complex::parse('2+i');
        $this->assertEquals(2, $z->r());
        $this->assertEquals(1, $z->i());
        $this->assertEquals('2+i', "$z");

        $z = Complex::parse('2-i');
        $this->assertEquals(2, $z->r());
        $this->assertEquals(-1, $z->i());
        $this->assertEquals('2-i', "$z");

        // Real part missing

        $z = Complex::parse('i');
        $this->assertEquals(0, $z->r());
        $this->assertEquals(1, $z->i());
        $this->assertEquals('i', "$z");

        $z = Complex::parse('-i');
        $this->assertEquals(0, $z->r());
        $this->assertEquals(-1, $z->i());
        $this->assertEquals('-i', "$z");

        //Purely imaginary
        $z = Complex::parse('2i');
        $this->assertEquals(0, $z->r());
        $this->assertEquals(2, $z->i());
        $this->assertEquals('2i', "$z");

        $z = Complex::parse('-3i');
        $this->assertEquals(0, $z->r());
        $this->assertEquals(-3, $z->i());
        $this->assertEquals('-3i', "$z");

        // Imaginary part missing

        $z = Complex::parse('2');
        $this->assertEquals(2, $z->r());
        $this->assertEquals(0, $z->i());
        $this->assertEquals('2', "$z");

        // Rational coefficients

        $z = Complex::parse('2/3+1/2i');
        $this->assertEquals(2 / 3, $z->r());
        $this->assertEquals(1 / 2, $z->i());
        $this->assertEquals('2/3+1/2i', "$z");

        // Real coefficients, (note that numbers that can be identified with small fractions are printed as such)
        $z = Complex::parse('0.7-0.2i');
        $this->assertEquals(0.7, $z->r());
        $this->assertEquals(-0.2, $z->i());
        $this->assertEquals('7/10-1/5i', "$z");

        // Imaginary part 1 or -1
        $z = Complex::parse('4+i');
        $this->assertEquals(4, $z->r());
        $this->assertEquals(1, $z->i());
        $this->assertEquals('4+i', "$z");

        $z = Complex::parse('4-i');
        $this->assertEquals(4, $z->r());
        $this->assertEquals(-1, $z->i());
        $this->assertEquals('4-i', "$z");

        $z = Complex::parse('0.2353578');
        $this->assertEquals(0.2353578, $z->r());
        $this->assertEquals(0, $z->i());
    }

    public function testCreateComplex()
    {
        $z = Complex::create(1, 2);
        $this->assertEquals(1, $z->r());
        $this->assertEquals(2, $z->i());

        $z = Complex::create("1", "1/2");
        $this->assertEquals(1, $z->r());
        $this->assertEquals(0.5, $z->i());
    }

    public function testParseFailure()
    {
        $this->expectException(SyntaxErrorException::class);
        $z = Complex::parse('sdf');
    }

    public function testCreateFailure()
    {
        $this->expectException(SyntaxErrorException::class);
        $z = Complex::create([1, 2], '23');
    }

    public function testCanDoAritmethic()
    {
        $z = new Complex(1, 2);
        $w = new Complex(2, -1);

        $this->assertEquals(new Complex(3, 1), Complex::add($z, $w));
        $this->assertEquals(new Complex(-1, 3), Complex::sub($z, $w));
        $this->assertEquals(new Complex(4, 3), Complex::mul($z, $w));
        $this->assertEquals(new Complex(0, 1), Complex::div($z, $w));

    }

    public function testCanComputePowers()
    {
        $z = new Complex(1, 2);

        $this->assertEquals(new Complex(-3, 4), Complex::pow($z, 2));
        $this->assertEquals(new Complex(-11, -2), Complex::pow($z, 3));
        $this->assertEquals(new Complex(1 / 5, -2 / 5), Complex::pow($z, -1));
        $this->assertEquals(new Complex(0.2291401860, 0.2381701151), Complex::pow($z, new Complex(0, 1)));
    }

    public function testCanComputeTranscendentals()
    {
        $z = new Complex(1, 2);
        $accuracy = 1e-9;

        $this->assertEquals(new Complex(1.272019650, 0.7861513778), Complex::sqrt($z), 'sqrt', $accuracy);
        $this->assertEquals(new Complex(3.165778513, 1.959601041), Complex::sin($z), 'sin', $accuracy);
        $this->assertEquals(new Complex(2.032723007, -3.051897799), Complex::cos($z), 'cos', $accuracy);
        $this->assertEquals(new Complex(0.03381282608, 1.014793616), Complex::tan($z), 'tan', $accuracy);
        $this->assertEquals(new Complex(0.03279775553, -0.9843292265), Complex::cot($z), 'cot', $accuracy);
        $this->assertEquals(new Complex(0.4270785864, 1.528570919), Complex::arcsin($z), 'arcsin', $accuracy);
        $this->assertEquals(new Complex(1.143717740, -1.528570919), Complex::arccos($z), 'arccos', $accuracy);
        $this->assertEquals(new Complex(1.338972522, 0.4023594781), Complex::arctan($z), 'arctan', $accuracy);
        $this->assertEquals(new Complex(0.2318238045, -0.4023594781), Complex::arccot($z), 'arccot', $accuracy);
        $this->assertEquals(new Complex(-1.131204384, 2.471726672), Complex::exp($z), 'exp', $accuracy);
        $this->assertEquals(new Complex(0.8047189562, 1.107148718), Complex::log($z), 'log', $accuracy);
        $this->assertEquals(new Complex(-0.4890562590, 1.403119251), Complex::sinh($z), 'sinh', $accuracy);
        $this->assertEquals(new Complex(-0.6421481247, 1.068607421), Complex::cosh($z), 'cosh', $accuracy);
        $this->assertEquals(new Complex(1.166736257, -0.2434582012), Complex::tanh($z), 'tanh', $accuracy);
        $this->assertEquals(new Complex(1.469351744, 1.063440024), Complex::arsinh($z), 'arsinh', $accuracy);
        $this->assertEquals(new Complex(1.528570919, 1.143717740), Complex::arcosh($z), 'arcosh', $accuracy);
        $this->assertEquals(new Complex(0.1732867951, 1.178097245), Complex::artanh($z), 'artanh', $accuracy);

    }

    public function testCanComputeNonAnalytic()
    {
        $z = new Complex(1, 2);
        $accuracy = 1e-9;

        $this->assertEquals(sqrt(5), $z->abs(), 'abs', $accuracy);
        $this->assertEquals(1, $z->r(), 'r', $accuracy);
        $this->assertEquals(2, $z->i(), 'i', $accuracy);
        $this->assertEquals(1.107148718, $z->arg(), 'arg', $accuracy);

    }
}
