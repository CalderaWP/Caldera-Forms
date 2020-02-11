<?php

use MathParser\ComplexMathParser;
use MathParser\Exceptions\DivisionByZeroException;
use MathParser\Exceptions\UnknownConstantException;
use MathParser\Exceptions\UnknownFunctionException;
use MathParser\Exceptions\UnknownOperatorException;
use MathParser\Exceptions\UnknownVariableException;
use MathParser\Extensions\Complex;
use MathParser\Interpreting\ComplexEvaluator;
use MathParser\Parsing\Nodes\ConstantNode;
use MathParser\Parsing\Nodes\ExpressionNode;
use MathParser\Parsing\Nodes\FunctionNode;
use MathParser\Parsing\Nodes\NumberNode;
use MathParser\Parsing\Nodes\VariableNode;
use PHPUnit\Framework\TestCase;

class ComplexEvaluatorTest extends TestCase
{
    private $parser;
    private $rparser;
    private $evaluator;
    private $variables;

    public function setUp()
    {
        $this->parser = new ComplexMathParser();

        $this->variables = ['x' => Complex::parse('1+i'), 'y' => Complex::parse('3+2i')];
        $this->evaluator = new ComplexEvaluator($this->variables);
    }

    private function evaluate($f)
    {
        $this->evaluator->setVariables($this->variables);

        return $f->accept($this->evaluator);
    }

    private function assertResult($f, $x)
    {
        $value = $this->evaluate($this->parser->parse($f));
        if (!($x instanceof Complex)) {
            $x = Complex::parse($x);
        }

        $this->assertEquals($value->r(), $x->r());
        $this->assertEquals($value->i(), $x->i());
    }

    private function assert_NAN($f)
    {
        $value = $this->evaluate($this->parser->parse($f));
        $this->assertTrue($value->is_nan());
    }

    public function testCanEvaluateNumber()
    {
        $this->assertResult('3', new Complex(3, 0));
        $this->assertResult('-2', new Complex(-2, 0));
        $this->assertResult('1+i', new Complex(1, 1));
    }

    public function testCanEvaluateConstant()
    {
        $this->assertResult('pi', pi());
        $this->assertResult('i', new Complex(0, 1));

        $f = new ConstantNode('sdf');
        $this->expectException(UnknownConstantException::class);
        $value = $this->evaluate($f);
    }

    public function testCanEvaluateVariable()
    {
        $this->assertResult('x', $this->variables['x']);

        $this->expectException(UnknownVariableException::class);

        $f = $this->parser->parse("q");
        $value = $this->evaluate($f);
    }

    public function testCanEvaluateAdditiion()
    {
        $x = $this->variables['x'];
        $this->assertResult('3+x', Complex::add(3, $x));
        $this->assertResult('3+x+1', Complex::add(4, $x));
    }

    public function testCanEvaluateSubtraction()
    {
        $x = $this->variables['x'];
        $this->assertResult('3-x', Complex::sub(3, $x));
        $this->assertResult('3-x-1', Complex::sub(2, $x));
    }

    public function testCanEvaluateUnaryMinus()
    {
        $this->assertResult('-x', Complex::mul(-1, $this->variables['x']));
    }

    public function testCanEvaluateMultiplication()
    {
        $x = $this->variables['x'];
        $this->assertResult('3*x', Complex::mul(3, $x));
        $this->assertResult('3*x*2', Complex::mul(6, $x));
    }

    public function testCanEvaluateDivision()
    {
        $x = $this->variables['x'];
        $this->assertResult('3/x', Complex::div(3, $x));
        $this->assertResult('20/x/5', Complex::div(4, $x));
    }

    public function testCanEvaluateExponentiation()
    {
        $x = $this->variables['x'];
        $this->assertResult('x^3', Complex::pow($x, 3));
        $this->assertResult('x^x^x', Complex::pow($x, Complex::pow($x, $x)));
        $this->assertResult('(-1)^(-1)', Complex::parse(-1));
    }

    public function testCantRaise0To0()
    {
        $this->expectException(DivisionByZeroException::class);
        $this->assertResult('0^0', 1);
    }

    public function testCanEvaluateSine()
    {
        $this->assertResult('sin(0)', 0);
        $this->assertResult('sin(pi/2)', 1);
        $this->assertResult('sin(pi/6)', 0.5);
        $this->assertResult('sin(x)', Complex::sin($this->variables['x']));
    }

    public function testCanEvaluateCosine()
    {
        $this->assertResult('cos(pi)', -1);
        $this->assertResult('cos(pi/2)', 0);
        $this->assertResult('cos(pi/3)', 0.5);
        $this->assertResult('cos(x)', Complex::cos($this->variables['x']));
    }

    public function testCanEvaluateTangent()
    {
        $this->assertResult('tan(pi)', 0);
        $this->assertResult('tan(pi/4)', 1);
        $this->assertResult('tan(x)', Complex::tan($this->variables['x']));
    }

    public function testCanEvaluateCotangent()
    {
        $this->assertResult('cot(pi/2)', 0);
        $this->assertResult('cot(pi/4)', 1);
        $this->assertResult('cot(x)', Complex::div(1, Complex::tan($this->variables['x'])));
    }

    public function testCanEvaluateArcsin()
    {
        $this->assertResult('arcsin(1)', pi() / 2);
        $this->assertResult('arcsin(1/2)', pi() / 6);
        $this->assertResult('arcsin(x)', Complex::arcsin($this->variables['x']));
    }

    public function testCanEvaluateArccos()
    {
        $this->assertResult('arccos(0)', pi() / 2);
        $this->assertResult('arccos(1/2)', pi() / 3);
        $this->assertResult('arccos(x)', Complex::arccos($this->variables['x']));
    }

    public function testCanEvaluateArctan()
    {
        $this->assertResult('arctan(1)', pi() / 4);
        $this->assertResult('arctan(x)', Complex::arctan($this->variables['x']));
    }

    public function testCanEvaluateArccot()
    {
        $this->assertResult('arccot(1)', pi() / 4);
        $this->assertResult('arccot(x)', Complex::arccot($this->variables['x']));
    }

    public function testCanEvaluateExp()
    {
        $this->assertResult('exp(x)', Complex::exp($this->variables['x']));
        $this->assertResult('e^x', Complex::exp($this->variables['x']));
    }

    public function testCanEvaluateLog()
    {
        $this->assertResult('log(-1)', new Complex(0, pi()));
        $this->assertResult('log(x)', Complex::log($this->variables['x']));
    }

    public function testCanEvaluateLn()
    {
        $this->assertResult('ln(3)', new Complex(log(3), 0.0));

        $this->expectException(\UnexpectedValueException::class);
        $this->assertResult('ln(x)', Complex::log($this->variables['x']));
    }

    public function testCanEvaluateLog10()
    {
        $this->assertResult('lg(-1)', new Complex(0, pi() / log(10)));
    }

    public function testCanEvaluateSqrt()
    {
        $this->assertResult('sqrt(-1)', new Complex(0, 1));
        $this->assertResult('sqrt(x)', Complex::sqrt($this->variables['x']));
    }

    public function testCanEvaluateAbs()
    {
        $x = $this->variables['x'];
        $this->assertResult('abs(x)', $x->abs());
        $this->assertResult('abs(i)', 1);
    }

    public function testCanEvaluateArg()
    {
        $x = $this->variables['x'];
        $this->assertResult('arg(x)', $x->arg());
        $this->assertResult('arg(1+i)', pi() / 4);
        $this->assertResult('arg(-i)', -pi() / 2);
    }

    public function testCanEvaluateConj()
    {
        $x = $this->variables['x'];
        $this->assertResult('conj(x)', new Complex($x->r(), -$x->i()));
    }

    public function testCanEvaluateRe()
    {
        $y = $this->variables['y'];
        $this->assertResult('re(y)', $y->r());
    }

    public function testCanEvaluateIm()
    {
        $y = $this->variables['y'];
        $this->assertResult('im(y)', $y->i());
    }

    public function testCanEvaluateHyperbolicFunctions()
    {
        $x = $this->variables['x'];

        $this->assertResult('sinh(0)', 0);
        $this->assertResult('sinh(x)', Complex::sinh($x));

        $this->assertResult('cosh(0)', 1);
        $this->assertResult('cosh(x)', Complex::cosh($x));

        $this->assertResult('tanh(0)', 0);
        $this->assertResult('tanh(x)', Complex::tanh($x));

        $this->assertResult('coth(x)', Complex::div(1, Complex::tanh($x)));

        $this->assertResult('arsinh(0)', 0);
        $this->assertResult('arsinh(x)', Complex::arsinh($x));

        $this->assertResult('arcosh(1)', 0);
        $this->assertResult('arcosh(3)', Complex::arcosh(3));
        $this->assertResult('arcosh(x)', Complex::arcosh($x));

        $this->assertResult('artanh(0)', 0);
        $this->assertResult('artanh(x)', Complex::artanh($x));
    }

    public function testCannotEvalauateUnknownFunction()
    {
        $f = new FunctionNode('sdf', new NumberNode(1));

        $this->expectException(UnknownFunctionException::class);
        $value = $this->evaluate($f);
    }

    public function testCannotEvaluateUnknownOperator()
    {
        $node = new ExpressionNode(new NumberNode(1), '+', new VariableNode('x'));
        // We need to cheat here, since the ExpressionNode contructor already
        // throws an UnknownOperatorException when called with, say '%'
        $node->setOperator('%');
        $this->expectException(UnknownOperatorException::class);

        $this->evaluate($node);
    }

    public function testCanCreateTemporaryUnaryMinusNode()
    {
        $node = new ExpressionNode(null, '~', null);
        $this->assertEquals($node->getOperator(), '~');
        $this->assertNull($node->getRight());
        $this->assertNull($node->getLeft());
        $this->assertEquals($node->getPrecedence(), 25);
    }

    public function testUnknownException()
    {
        $this->expectException(UnknownOperatorException::class);
        $node = new ExpressionNode(null, '%', null);
    }

    public function testEdgeCases()
    {
        $this->assert_NAN('log(0)');
        $this->assert_NAN('arctan(i)');
    }
}
