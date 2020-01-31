<?php

use MathParser\Exceptions\DivisionByZeroException;
use MathParser\Exceptions\UnknownConstantException;
use MathParser\Exceptions\UnknownFunctionException;
use MathParser\Exceptions\UnknownOperatorException;
use MathParser\Exceptions\UnknownVariableException;
use MathParser\Interpreting\RationalEvaluator;
use MathParser\Parsing\Nodes\ConstantNode;
use MathParser\Parsing\Nodes\ExpressionNode;
use MathParser\Parsing\Nodes\FunctionNode;
use MathParser\Parsing\Nodes\IntegerNode;
use MathParser\Parsing\Nodes\RationalNode;
use MathParser\Parsing\Nodes\VariableNode;
use MathParser\RationalMathParser;
use PHPUnit\Framework\TestCase;

class RationalEvaluatorTest extends TestCase
{
    private $parser;
    private $evaluator;
    private $variables;

    public function setUp()
    {
        $this->parser = new RationalMathParser();

        $this->variables = ['x' => '1/2', 'y' => '2/3'];
        $this->evaluator = new RationalEvaluator($this->variables);
    }

    private function evaluate($f)
    {
        return $f->accept($this->evaluator);
    }

    private function assertResult($f, $x)
    {
        $this->evaluator->setVariables($this->variables);
        $value = $this->evaluate($this->parser->parse($f));
        $this->assertEquals($value, $x);
    }

    public function testCanEvaluateNumber()
    {
        $this->assertResult('3', new RationalNode(3, 1));
        $this->assertResult('-2', new RationalNode(-2, 1));
        $this->assertResult('1/2', new RationalNode(1, 2));
    }

    public function testCantEvaluateFloat()
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->assertResult('2.5', 2.5);
    }

    public function testCanEvaluateConstant()
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->assertResult('pi', pi());
    }

    public function testUnknownConstant()
    {
        $f = new ConstantNode('sdf');
        $this->expectException(UnknownConstantException::class);
        $value = $this->evaluate($f);
    }

    public function testCanEvaluateVariable()
    {
        $this->assertResult('x', $this->evaluator->parseRational($this->variables['x']));

        $this->expectException(UnknownVariableException::class);

        $f = $this->parser->parse("q");
        $value = $this->evaluate($f);
    }

    public function testCanEvaluateAdditiion()
    {
        $this->assertResult('3+x', new RationalNode(7, 2));
        $this->assertResult('3+x+1', new RationalNode(9, 2));
    }

    public function testCanEvaluateSubtraction()
    {
        $this->assertResult('3-x', new RationalNode(5, 2));
        $this->assertResult('3-x-1', new RationalNode(3, 2));
    }

    public function testCanEvaluateUnaryMinus()
    {
        $this->assertResult('-x', new RationalNode(-1, 2));
    }

    public function testCanEvaluateMultiplication()
    {
        $this->assertResult('3*x', new RationalNode(3, 2));
        $this->assertResult('3*x*2', new RationalNode(3, 1));
    }

    public function testCanEvaluateDivision()
    {
        $this->assertResult('3/x', new RationalNode(6, 1));
        $this->assertResult('20/x/5', new RationalNode(8, 1));
    }

    public function testCannotDivideByZero()
    {
        $f = new ExpressionNode(new IntegerNode(3), '/', new IntegerNode(0));

        $this->expectException(DivisionByZeroException::class);
        $value = $this->evaluate($f);
    }

    public function testCanEvaluateExponentiation()
    {
        $this->assertResult('x^3', new RationalNode(1, 8));
        $this->assertResult('x^(-3)', new RationalNode(8, 1));
        $this->assertResult('(-x)^3', new RationalNode(-1, 8));
        $this->assertResult('(-x)^(-3)', new RationalNode(-8, 1));
        $this->assertResult('(-1)^(-1)', new RationalNode(-1, 1));
        $this->assertResult('4^(-3/2)', new RationalNode(1, 8));
    }

    public function testCantRaise0To0()
    {
        $this->expectException(DivisionByZeroException::class);
        $this->assertResult('0^0', 1);
    }

    public function testExponentiationExceptions()
    {
        $this->expectException(DivisionByZeroException::class);
        $f = $this->parser->parse('0^(-1)');
        $value = $this->evaluate($f);
    }

    public function testCanEvaluateSine()
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->assertResult('sin(x)', 0);
    }

    public function testCanEvaluateCosine()
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->assertResult('cos(x)', 0);
    }

    public function testCanEvaluateTangent()
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->assertResult('tan(x)', 0);
    }

    public function testCanEvaluateCotangent()
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->assertResult('cot(x)', 0);
    }

    public function testCanEvaluateArcsin()
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->assertResult('arcsin(x)', 0);
    }

    public function testCanEvaluateArccos()
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->assertResult('arccos(x)', 0);
    }

    public function testCanEvaluateArctan()
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->assertResult('arctan(x)', 0);
    }

    public function testCanEvaluateArccot()
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->assertResult('arccot(x)', 0);
    }

    public function testCanEvaluateExp()
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->assertResult('exp(x)', 0);
    }

    public function testCanEvaluateLog()
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->assertResult('log(x)', 0);
    }

    public function testCanEvaluateLog10()
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->assertResult('log10(x)', 0);
    }

    public function testCanEvaluateSqrt()
    {
        $this->assertResult('sqrt(1/4)', new RationalNode(1, 2));
        $this->assertResult('sqrt(4)', new RationalNode(2, 1));

        $this->expectException(\UnexpectedValueException::class);
        $this->assertResult('sqrt(1/2)', 0);

        $this->assertResult('sqrt(225)', new RationalNode(15, 1));

        $this->assertResult('sqrt(7^6)', new RationalNode(7 * 7 * 7, 1));
    }

    public function testCanEvaluateHyperbolicFunctions()
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->assertResult('sinh(x)', 0);

    }

    public function testCannotEvalauateUnknownFunction()
    {
        $f = new FunctionNode('sdf', new RationalNode(1, 1));

        $this->expectException(UnknownFunctionException::class);
        $value = $this->evaluate($f);

    }

    public function testCannotEvaluateUnknownOperator()
    {
        $node = new ExpressionNode(new RationalNode(1, 1), '+', new VariableNode('x'));
        // We need to cheat here, since the ExpressionNode contructor already
        // throws an UnknownOperatorException when called with, say '%'
        $node->setOperator('%');
        $this->expectException(UnknownOperatorException::class);

        $this->evaluate($node);

    }

    public function testUnknownException()
    {
        $this->expectException(UnknownOperatorException::class);
        $node = new ExpressionNode(null, '%', null);
    }

    public function testParseRational()
    {
        $node = $this->evaluator->parseRational('1');
        $this->assertEquals($node, new RationalNode(1, 1));

        $this->expectException(\UnexpectedValueException::class);
        $this->evaluator->parseRational('1/2/3');
    }

    public function testParseRational2()
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->evaluator->setVariables(['x' => 'u/q']);
    }

    public function testCanSetVariables()
    {
        $this->evaluator->setVariables(['x' => '1', 'y' => new RationalNode(2, 3)]);

        $value = $this->evaluate($this->parser->parse('x'));
        $this->assertEquals($value, new RationalNode(1, 1));

        $value = $this->evaluate($this->parser->parse('y'));
        $this->assertEquals($value, new RationalNode(2, 3));
    }

    public function testCanFactor()
    {
        $factors = $this->evaluator->ifactor(51);
        $this->assertEquals($factors, [3 => 1, 17 => 1]);

        $factors = $this->evaluator->ifactor(25 * 13);
        $this->assertEquals($factors, [5 => 2, 13 => 1]);
    }
}
