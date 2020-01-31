<?php

use MathParser\Exceptions\DivisionByZeroException;
use MathParser\Exceptions\UnknownFunctionException;
use MathParser\Exceptions\UnknownOperatorException;
use MathParser\Interpreting\Differentiator;
use MathParser\Interpreting\TreePrinter;
use MathParser\Parsing\Nodes\ExpressionNode;
use MathParser\Parsing\Nodes\FunctionNode;
use MathParser\Parsing\Nodes\NumberNode;
use MathParser\Parsing\Nodes\VariableNode;
use MathParser\RationalMathParser;
use PHPUnit\Framework\TestCase;

class DifferentiatorTest extends TestCase
{
    private $parser;
    private $differentiator;

    public function setUp()
    {
        $this->parser = new RationalMathParser();
        $this->differentiator = new Differentiator('x');
    }

    public function diff($node)
    {
        $derivative = $node->accept($this->differentiator);

        return $derivative;
    }

    private function assertNodesEqual($node1, $node2)
    {
        $printer = new TreePrinter();
        $message = "Node1: " . $node1->accept($printer) . "\nNode 2: " . $node2->accept($printer) . "\n";

        $this->assertTrue($node1->compareTo($node2), $message);
    }

    private function assertResult($f, $df)
    {
        $fnc = $this->parser->parse($f);
        $derivative = $this->parser->parse($df);

        $this->assertNodesEqual($this->diff($fnc), $derivative);

        // Check that Differentior leaves the original node unchanged.
        $newAST = $this->parser->parse($f);
        $this->assertNodesEqual($fnc, $newAST);
    }

    public function testCanDifferentiateVariable()
    {
        $this->assertResult('x', '1');
        $this->assertResult('y', '0');
    }

    public function testCanDifferentiateConstant()
    {
        $this->assertResult('pi', '0');
        $this->assertResult('pi*e', '0');
        $this->assertResult('7', '0');
        $this->assertResult('1+3', '0');
        $this->assertResult('5*2', '0');
        $this->assertResult('1/2', '0');
        $this->assertResult('2^2', '0');
        $this->assertResult('-2', '0');
    }

    public function testCanDifferentiateExp()
    {
        $this->assertResult('exp(x)', 'exp(x)');
        $this->assertResult('exp(x^2)', '2*x*exp(x^2)');
    }

    public function testCanDifferentiateLog()
    {
        $this->assertResult('log(x)', '1/x');
    }

    public function testCanDifferentiateLn()
    {
        $this->assertResult('ln(x)', '1/x');
    }

    public function testCanDifferentiateLog10()
    {
        $this->assertResult('log10(x)', '1/(ln(10)x)');
    }

    public function testCanDifferentiateSin()
    {
        $this->assertResult('sin(x)', 'cos(x)');
    }

    public function testCanDifferentiateCos()
    {
        $this->assertResult('cos(x)', '-sin(x)');
    }

    public function testCanDifferentiateTan()
    {
        $this->assertResult('tan(x)', '1+tan(x)^2');
    }

    public function testCanDifferentiateCot()
    {
        $this->assertResult('cot(x)', '-1-cot(x)^2');
    }

    public function testCanDifferentiateArcsin()
    {
        $this->assertResult('arcsin(x)', '1/sqrt(1-x^2)');
    }

    public function testCanDifferentiateArccos()
    {
        $this->assertResult('arccos(x)', '(-1)/sqrt(1-x^2)');
    }

    public function testCanDifferentiateArctan()
    {
        $this->assertResult('arctan(x)', '1/(1+x^2)');
        $this->assertResult('arctan(x^3)', '(3x^2)/(1+x^6)');
    }

    public function testCanDifferentiateArccot()
    {
        $this->assertResult('-arccot(x)', '1/(1+x^2)');
    }

    public function testCanDifferentiateSqrt()
    {
        $this->assertResult('sqrt(x)', '1/(2sqrt(x))');
    }

    public function testCanDifferentiateSum()
    {
        $this->assertResult('x+sin(x)', '1+cos(x)');
        $this->assertResult('sin(x)+y', 'cos(x)');
        $this->assertResult('y+sin(x)', 'cos(x)');
    }

    public function testCanDifferentiateDifference()
    {
        $this->assertResult('x-sin(x)', '1-cos(x)');
        $this->assertResult('sin(x)-y', 'cos(x)');
        $this->assertResult('sin(x)-sin(x)', '0');
    }

    public function testCanDifferentiateProduct()
    {
        $this->assertResult('x*sin(x)', 'x*cos(x)+sin(x)');
    }

    public function testCanDifferentiateExponent()
    {
        $this->assertResult('x^1', '1');
        $this->assertResult('x^2', '2x');
        $this->assertResult('x^3', '3x^2');
        $this->assertResult('x^x', 'x^x*(ln(x)+1)');
        $this->assertResult('x^(1/2)', '(1/2)*x^(-1/2)');
        $this->assertResult('e^x', 'e^x');
        $this->assertResult('e^(x^2)', '2*x*e^(x^2)');
        $this->assertResult('sin(x)^cos(x)', 'sin(x)^cos(x)*((-sin(x))*ln(sin(x))+cos(x)*cos(x)/sin(x))');
    }

    public function testCanDifferentiateQuotient()
    {
        $this->assertResult('x/sin(x)', '(sin(x)-x*cos(x))/sin(x)^2');
        $this->assertResult('x/1', '1');

        // The parser catches 'x/0', so create the test AST directly
        $f = new ExpressionNode(new VariableNode('x'), '/', 0);
        $this->expectException(DivisionByZeroException::class);
        $this->diff($f);
    }

    public function testCanDifferentiateComposite()
    {
        $this->assertResult('sin(sin(x))', 'cos(x)*cos(sin(x))');

    }

    public function testCanDifferentiateUnaryMinus()
    {
        $this->assertResult('-x', '-1');
    }

    public function testCannotDifferentiateUnknownFunction()
    {
        $node = new FunctionNode('erf', new VariableNode('x'));
        $this->expectException(UnknownFunctionException::class);

        $this->diff($node);

    }

    public function testCannotDifferentiateUnknownOperator()
    {
        $node = new ExpressionNode(new NumberNode(1), '+', new VariableNode('x'));
        // We need to cheat here, since the ExpressionNode contructor already
        // throws an UnknownOperatorException when called with, say '%'
        $node->setOperator('%');
        $this->expectException(UnknownOperatorException::class);

        $this->diff($node);

    }

    public function testCanDifferentiateHyperbolicFunctions()
    {
        $this->assertResult('sinh(x)', 'cosh(x)');
        $this->assertResult('cosh(x)', 'sinh(x)');
        $this->assertResult('tanh(x)', '1-tanh(x)^2');
        $this->assertResult('coth(x)', '1-coth(x)^2');

        $this->assertResult('arsinh(x)', '1/sqrt(x^2+1)');
        $this->assertResult('arcosh(x)', '1/sqrt(x^2-1)');
        $this->assertResult('artanh(x)', '1/(1-x^2)');
        $this->assertResult('arcoth(x)', '1/(1-x^2)');
    }

    public function testCantDifferentiateCeil()
    {
        $f = $this->parser->parse('ceil(x)');

        $this->expectException(UnknownFunctionException::class);
        $this->diff($f);
    }

    public function testCantDifferentiateFloor()
    {
        $f = $this->parser->parse('floor(x)');

        $this->expectException(UnknownFunctionException::class);
        $this->diff($f);
    }

    public function testCantDifferentiateRound()
    {
        $f = $this->parser->parse('round(x)');

        $this->expectException(UnknownFunctionException::class);
        $this->diff($f);
    }

    public function testCantDifferentiateSgn()
    {
        $f = $this->parser->parse('sgn(x)');

        $this->expectException(UnknownFunctionException::class);
        $this->diff($f);
    }

}
