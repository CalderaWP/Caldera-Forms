<?php

use MathParser\Exceptions\UnknownConstantException;
use MathParser\Interpreting\LaTeXPrinter;
use MathParser\Parsing\Nodes\ConstantNode;
use MathParser\Parsing\Nodes\IntegerNode;
use MathParser\Parsing\Nodes\VariableNode;
use MathParser\RationalMathParser;
use PHPUnit\Framework\TestCase;

class LaTeXPrinterTest extends TestCase
{
    private $parser;
    private $printer;

    public function setUp()
    {
        $this->parser = new RationalMathParser();
        $this->printer = new LaTeXPrinter();
    }

    private function assertResult($input, $output)
    {
        $node = $this->parser->parse($input);
        $result = $node->accept($this->printer);

        $this->assertEquals($result, $output);
    }

    public function testCanPrintVariable()
    {
        $this->assertResult('x', 'x');
    }

    public function testCanPrintNumber()
    {
        $this->assertResult('4', '4');
        $this->assertResult('-2', '-2');
        $this->assertResult('1.5', '1.5');
        $this->assertResult('3/4', '\frac{3}{4}');
    }

    public function testCanPrintUnaryMinus()
    {
        $this->assertResult('-x', '-x');
        $this->assertResult('sin(-x)', '\sin(-x)');
        $this->assertResult('(-1)^k', '(-1)^k');
        $this->assertResult('-(x-1)', '-(x-1)');
        $this->assertResult('-(2/3)', '\frac{-2}{3}');
    }

    public function testCanPrintSums()
    {
        $this->assertResult('x+y+z', 'x+y+z');
        $this->assertResult('x+y-z', 'x+y-z');
        $this->assertResult('x-y-z', 'x-y-z');
        $this->assertResult('x-y+z', 'x-y+z');
        $this->assertResult('-x-y-z', '-x-y-z');
        $this->assertResult('x+(-y)', 'x+(-y)');
        $this->assertResult('x+y+z', 'x+y+z');
        $this->assertResult('1+2x+3x^2', '1+2x+3x^2');

    }

    public function testCanPrintProducts()
    {
        $this->assertResult('xyz', 'xyz');
        $this->assertResult('xy/z', '\frac{xy}{z}');
        $this->assertResult('x/yz', '\frac{x}{y}z');
        $this->assertResult('x/y/z', '\frac{\frac{x}{y}}{z}');
    }

    public function testCanPrintExponentiation()
    {

        $this->assertResult('x^y^z', 'x^{y^z}');
        $this->assertResult('(x^y)^z', 'x^{yz}');

        $this->parser->setSimplifying(false);
        $this->assertResult('x^y^z', 'x^{y^z}');
        $this->assertResult('(x^y)^z', '{x^y}^z');
        $this->parser->setSimplifying(true);

    }

    public function testCanAddBraces()
    {
        $node = new IntegerNode(4);
        $output = $this->printer->bracesNeeded($node);

        $this->assertEquals($output, '4');

        $node = new IntegerNode(-2);
        $output = $this->printer->bracesNeeded($node);

        $this->assertEquals($output, '{-2}');

        $node = new IntegerNode(12);
        $output = $this->printer->bracesNeeded($node);

        $this->assertEquals($output, '{12}');

        $node = new VariableNode('x');
        $output = $this->printer->bracesNeeded($node);

        $this->assertEquals($output, 'x');

        $node = new ConstantNode('pi');
        $output = $this->printer->bracesNeeded($node);

        $this->assertEquals($output, '\pi{}');

        $node = $this->parser->parse('x+1');
        $output = $this->printer->bracesNeeded($node);

        $this->assertEquals($output, '{x+1}');
    }

    public function testCanPrintDivision()
    {
        $this->assertResult('1/2', '\frac{1}{2}');
        $this->assertResult('x/y', '\frac{x}{y}');
        $this->assertResult('4/2', '2');
        $this->assertResult('1/(sin(x)^2)', '\frac{1}{\sin(x)^2}');
    }

    public function testCanPrintMultiplication()
    {
        $this->assertResult('sin(x)*x', '\sin(x)\cdot x');
        $this->assertResult('2*(x+4)', '2(x+4)');
        $this->assertResult('(x+1)*(x+2)', '(x+1)(x+2)');

        $this->parser->setSimplifying(false);
        $this->assertResult('2*3', '2\cdot 3');
        $this->assertResult('2*x', '2x');
        $this->assertResult('2*3^2', '2\cdot 3^2');
        $this->assertResult('2*(1/2)^2', '2(\frac{1}{2})^2');
        $this->parser->setSimplifying(true);
    }

    public function testCanPrintFunctions()
    {
        $this->assertResult('sin(x)', '\sin(x)');
        $this->assertResult('cos(x)', '\cos(x)');
        $this->assertResult('tan(x)', '\tan(x)');

        $this->assertResult('log(x)', '\log(x)');
        $this->assertResult('log(2x)', '\log(2x)');
        $this->assertResult('log(2+x)', '\log(2+x)');

        $this->assertResult('ln(x)', '\ln(x)');
        $this->assertResult('ln(2x)', '\ln(2x)');
        $this->assertResult('ln(2+x)', '\ln(2+x)');

        $this->assertResult('sqrt(x)', '\sqrt{x}');
        $this->assertResult('sqrt(x^2)', '\sqrt{x^2}');

        $this->assertResult('asin(x)', '\arcsin(x)');
        $this->assertResult('arsinh(x)', '\operatorname{arsinh}(x)');
    }

    /**
     * @test
     */
    public function it_can_print_exponential_functions()
    {
        $this->assertResult('exp(x)', 'e^x');
        $this->assertResult('exp(2)', 'e^2');
        $this->assertResult('exp(2x)', 'e^{2x}');
        $this->assertResult('exp(x/2)', 'e^{x/2}');
        $this->assertResult('exp((x+1)/2)', 'e^{(x+1)/2}');
        $this->assertResult('exp(-2x)', 'e^{-2x}');
        $this->assertResult('exp(-2x+3)', 'e^{-2x+3}');
        $this->assertResult('exp(x+y+z)', 'e^{x+y+z}');
        $this->assertResult('exp(x^2)', '\exp(x^2)');
        $this->assertResult('exp(sin(x))', 'e^{\sin(x)}');
        $this->assertResult('exp(sin(x)cos(x))', '\exp(\sin(x)\cdot \cos(x))');
    }

    /**
     * @test
     */
    public function it_can_print_powers()
    {
        $this->assertResult('x^y', 'x^y');
        $this->assertResult('x^2', 'x^2');
        $this->assertResult('x^(2y)', 'x^{2y}');
        $this->assertResult('x^(1/2)', 'x^{1/2}');
        $this->assertResult('x^((x+1)/2)', 'x^{(x+1)/2}');
        $this->assertResult('x^((y+z)^2/(w+t))', 'x^{(y+z)^2/(w+t)}');
    }

    public function testCanPrintFactorials()
    {
        $this->assertResult('3!', '3!');
        $this->assertResult('x!', 'x!');
        $this->assertResult('e!', 'e!');
        $this->assertResult('(x+y)!', '(x+y)!');
        $this->assertResult('(x+2)!', '(x+2)!');
        $this->assertResult('sin(x)!', '(\sin(x))!');
        $this->assertResult('(3!)!', '(3!)!');
    }

    public function testCanPrintSemiFactorials()
    {
        $this->assertResult('3!!', '3!!');
        $this->assertResult('x!!', 'x!!');
        $this->assertResult('e!!', 'e!!');
        $this->assertResult('(x+y)!!', '(x+y)!!');
        $this->assertResult('(x+2)!!', '(x+2)!!');
        $this->assertResult('sin(x)!!', '(\sin(x))!!');
    }

    public function testCanPrintConstant()
    {
        $this->assertResult('pi', '\pi{}');
        $this->assertResult('e', 'e');

        $node = new ConstantNode('xcv');
        $this->expectException(UnknownConstantException::class);
        $node->accept($this->printer);
    }
}
