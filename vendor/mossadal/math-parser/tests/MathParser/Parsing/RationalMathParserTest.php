<?php

use MathParser\Exceptions\DivisionByZeroException;
use MathParser\Exceptions\ParenthesisMismatchException;
use MathParser\Exceptions\SyntaxErrorException;
use MathParser\Interpreting\TreePrinter;
use MathParser\Lexing\Token;
use MathParser\Lexing\TokenType;
use MathParser\Parsing\Nodes\ConstantNode;
use MathParser\Parsing\Nodes\ExpressionNode;
use MathParser\Parsing\Nodes\Factories\NodeFactory;
use MathParser\Parsing\Nodes\IntegerNode;
use MathParser\Parsing\Nodes\NumberNode;
use MathParser\Parsing\Nodes\RationalNode;
use MathParser\Parsing\Nodes\VariableNode;
use MathParser\Parsing\Parser;
use MathParser\RationalMathParser;
use PHPUnit\Framework\TestCase;

class RationalMathParserTest extends TestCase
{
    private $parser;

    public function setUp()
    {
        $this->parser = new RationalMathParser();
        $this->factory = new NodeFactory();

    }

    private function assertNodesEqual($node1, $node2)
    {
        $printer = new TreePrinter();
        $message = "Node1: " . $node1->accept($printer) . "\nNode 2:" . $node2->accept($printer) . "\n";

        $this->assertTrue($node1->compareTo($node2), $message);
    }

    private function assertNumberNode($node, $value)
    {
        $this->assertInstanceOf('MathParser\Parsing\Nodes\NumberNode', $node);
        $this->assertEquals($value, $node->getValue());
    }

    private function assertIntegerNode($node, $value)
    {
        $this->assertInstanceOf('MathParser\Parsing\Nodes\IntegerNode', $node);
        $this->assertEquals($value, $node->getValue());
    }

    private function assertRationalNode($node, $p, $q)
    {
        $this->assertInstanceOf('MathParser\Parsing\Nodes\RationalNode', $node);
        $this->assertEquals($p, $node->getNumerator());
        $this->assertEquals($q, $node->getDenominator());
    }

    private function assertVariableNode($node, $value)
    {
        $this->assertInstanceOf('MathParser\Parsing\Nodes\VariableNode', $node);
        $this->assertEquals($value, $node->getName());
    }

    private function assertCompareNodes($text)
    {
        $node1 = $this->parser->parse($text);
        $node2 = $this->parser->parse($text);

        $this->assertNodesEqual($node1, $node2);
    }

    public function testCanCompareNodes()
    {
        $this->assertCompareNodes("3");
        $this->assertCompareNodes("x");
        $this->assertCompareNodes("x+y");
        $this->assertCompareNodes("sin(x)");
        $this->assertCompareNodes("(x)");
        $this->assertCompareNodes("1+x+y");
    }

    private function assertTokenEquals($value, $type, Token $token)
    {
        $this->assertEquals($value, $token->getValue());
        $this->assertEquals($type, $token->getType());
    }

    public function testCanGetTokenList()
    {
        $node = $this->parser->parse("x+y");
        $tokens = $this->parser->getTokenList();

        $this->assertTokenEquals("x", TokenType::Identifier, $tokens[0]);
        $this->assertTokenEquals("+", TokenType::AdditionOperator, $tokens[1]);
        $this->assertTokenEquals("y", TokenType::Identifier, $tokens[2]);

    }

    public function testCanGetTree()
    {
        $node = $this->parser->parse("1+x");
        $tree = $this->parser->getTree();
        $this->assertNodesEqual($node, $tree);
    }

    public function testCanParseSingleNumberExpression()
    {
        $node = $this->parser->parse("3");
        $shouldBe = new IntegerNode(3);
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("2/3");
        $shouldBe = new RationalNode(2, 3);
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("3.5");
        $shouldBe = new NumberNode(3.5);
        $this->assertNodesEqual($node, $shouldBe);

    }

    public function testCanParseSingleVariable()
    {
        $node = $this->parser->parse('x');
        $shouldBe = new VariableNode('x');

        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse('(x)');
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse('((x))');
        $this->assertNodesEqual($node, $shouldBe);
    }

    public function testCanParseSingleConstant()
    {
        $node = $this->parser->parse('pi');
        $shouldBe = new ConstantNode('pi');

        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse('(pi)');
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse('((pi))');
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse('e');
        $shouldBe = new ConstantNode('e');

        $this->assertNodesEqual($node, $shouldBe);
    }

    public function testCanParseBinaryExpression()
    {
        $node = $this->parser->parse("x+y");
        $shouldBe = new ExpressionNode(new VariableNode('x'), '+', new VariableNode('y'));

        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("x-y");
        $shouldBe = new ExpressionNode(new VariableNode('x'), '-', new VariableNode('y'));

        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("x*y");
        $shouldBe = new ExpressionNode(new VariableNode('x'), '*', new VariableNode('y'));

        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("x/y");
        $shouldBe = new ExpressionNode(new VariableNode('x'), '/', new VariableNode('y'));

        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("x^y");
        $shouldBe = new ExpressionNode(new VariableNode('x'), '^', new VariableNode('y'));

        $this->assertNodesEqual($node, $shouldBe);
    }

    public function testCanParseWithCorrectAssociativity()
    {
        $node = $this->parser->parse("x+y+z");
        $shouldBe = new ExpressionNode(
            new ExpressionNode(new VariableNode('x'), '+', new VariableNode('y')),
            '+',
            new VariableNode('z')
        );
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("x-y-z");
        $shouldBe = new ExpressionNode(
            new ExpressionNode(new VariableNode('x'), '-', new VariableNode('y')),
            '-',
            new VariableNode('z')
        );
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("x*y*z");
        $shouldBe = new ExpressionNode(
            new ExpressionNode(new VariableNode('x'), '*', new VariableNode('y')),
            '*',
            new VariableNode('z')
        );
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("x/y/z");
        $shouldBe = new ExpressionNode(
            new ExpressionNode(new VariableNode('x'), '/', new VariableNode('y')),
            '/',
            new VariableNode('z')
        );
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("x^y^z");
        $shouldBe = new ExpressionNode(
            new VariableNode('x'),
            '^',
            new ExpressionNode(new VariableNode('y'), '^', new VariableNode('z'))
        );
        $this->assertNodesEqual($node, $shouldBe);

    }

    public function testCanParseWithCorrectPrecedence()
    {
        $x = new VariableNode('x');
        $y = new VariableNode('y');
        $z = new VariableNode('z');

        $node = $this->parser->parse("x+y*z");

        $factors = new ExpressionNode($y, '*', $z);
        $shouldBe = new ExpressionNode($x, '+', $factors);

        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("x*y+z");
        $factors = new ExpressionNode($x, '*', $y);
        $shouldBe = new ExpressionNode($factors, '+', $z);

        $this->assertNodesEqual($node, $shouldBe);
    }

    public function testCanParseParentheses()
    {
        $node = $this->parser->parse("(x)");
        $shouldBe = new VariableNode('x');
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("((x))");
        $shouldBe = new VariableNode('x');
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("(x+1)");
        $shouldBe = $this->parser->parse("x+1");
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("(x*y)");
        $shouldBe = $this->parser->parse("x*y");
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("(x^y)");
        $shouldBe = $this->parser->parse("x^y");
        $this->assertNodesEqual($node, $shouldBe);
    }

    public function testImplicitMultiplication()
    {
        $node = $this->parser->parse("2x");
        $shouldBe = $this->parser->parse("2*x");
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("2xy");
        $shouldBe = $this->parser->parse("2*x*y");
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("2x^2");
        $shouldBe = $this->parser->parse("2*x^2");
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("2x^2y");
        $shouldBe = $this->parser->parse("2*x^2*y");
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("(-x)2");
        $shouldBe = $this->parser->parse("(-x)*2");
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("x^2y^2");
        $shouldBe = $this->parser->parse("x^2*y^2");
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("(x+1)(x-1)");
        $shouldBe = $this->parser->parse("(x+1)*(x-1)");
        $this->assertNodesEqual($node, $shouldBe);

    }

    public function testCanParseUnaryOperators()
    {
        $node = $this->parser->parse("-x");
        $shouldBe = new ExpressionNode(new VariableNode('x'), '-', null);
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("+x");
        $shouldBe = new VariableNode('x');
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("-x+y");
        $shouldBe = new ExpressionNode(
            new ExpressionNode(new VariableNode('x'), '-', null),
            '+',
            new VariableNode('y')
        );
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("-x*y");
        $shouldBe = $this->parser->parse("-(x*y)");
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("-x^y");
        $shouldBe = $this->parser->parse("-(x^y)");
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("(-x)^y");
        $shouldBe = new ExpressionNode(
            new ExpressionNode(new VariableNode('x'), '-', null),
            '^',
            new VariableNode('y')
        );
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->parser->parse("-(1/2)");
        $shouldBe = new RationalNode(-1, 2);
        $this->assertNodesEqual($node, $shouldBe);

    }

    public function testSyntaxErrorException()
    {
        $this->expectException(SyntaxErrorException::class);
        $this->parser->parse('1+');
    }

    public function testSyntaxErrorException2()
    {
        $this->expectException(SyntaxErrorException::class);
        $this->parser->parse('**3');
    }

    public function testSyntaxErrorException3()
    {
        $this->expectException(SyntaxErrorException::class);
        $this->parser->parse('-');
    }

    public function testParenthesisMismatchException()
    {
        $this->expectException(ParenthesisMismatchException::class);
        $this->parser->parse('1+1)');

        $this->expectException(ParenthesisMismatchException::class);
        $this->parser->parse('(1+1');
    }

    public function testCanEvaluateNode()
    {
        $f = $this->parser->parse('x+y');
        $this->assertEquals($f->evaluate(['x' => 1, 'y' => 2]), 3);
    }

    public function testCanParseRationals()
    {
        $f = $this->parser->parse('1/2');
        $this->assertTrue($f->compareTo(new RationalNode(1, 2)));

        $f = $this->parser->parse('1/2+1/3');
        $this->assertTrue($f->compareTo(new RationalNode(5, 6)));

        $f = $this->parser->parse('1/2*2/5');
        $this->assertTrue($f->compareTo(new RationalNode(1, 5)));

        $f = $this->parser->parse('(1/2)/(1/3)');
        $this->assertTrue($f->compareTo(new RationalNode(3, 2)));

    }

    public function testAdditionNodeFactory()
    {
        $node = $this->factory->addition(new VariableNode('x'), new IntegerNode(0));
        $shouldBe = new VariableNode('x');
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->addition(new VariableNode('x'), new RationalNode(0, 1));
        $shouldBe = new VariableNode('x');
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->addition(new VariableNode('x'), new NumberNode(0));
        $shouldBe = new VariableNode('x');
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->addition(new IntegerNode(0), new VariableNode('x'));
        $shouldBe = new VariableNode('x');
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->addition(new RationalNode(0, 1), new VariableNode('x'));
        $shouldBe = new VariableNode('x');
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->addition(new NumberNode(0), new VariableNode('x'));
        $shouldBe = new VariableNode('x');
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->addition(new VariableNode('x'), new VariableNode('y'));
        $shouldBe = $this->parser->parse('x+y');
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->addition(new NumberNode(1.5), new NumberNode(2.5));
        $shouldBe = new NumberNode(4.0);
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->addition(new NumberNode(1.5), new RationalNode(1, 2));
        $shouldBe = new NumberNode(2.0);
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->addition(new NumberNode(1.5), new IntegerNode(1));
        $shouldBe = new NumberNode(2.5);
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->addition(new RationalNode(1, 2), new RationalNode(1, 3));
        $shouldBe = new RationalNode(5, 6);
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->addition(new RationalNode(1, 2), new IntegerNode(1));
        $shouldBe = new RationalNode(3, 2);
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->addition(new IntegerNode(2), new IntegerNode(1));
        $shouldBe = new IntegerNode(3);
        $this->assertNodesEqual($node, $shouldBe);
    }

    public function testDivisionNodeFactory()
    {
        $node = $this->factory->division(new VariableNode('x'), new IntegerNode(1));
        $shouldBe = new VariableNode('x');
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->division(new IntegerNode(0), new VariableNode('x'));
        $shouldBe = new IntegerNode(0);
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->division(new RationalNode(0, 1), new VariableNode('x'));
        $shouldBe = new IntegerNode(0);
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->division(new NumberNode(0), new VariableNode('x'));
        $shouldBe = new IntegerNode(0);
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->division(new VariableNode('x'), new VariableNode('y'));
        $shouldBe = $this->parser->parse('x/y');
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->division(new NumberNode(3.0), new NumberNode(1.5));
        $shouldBe = new NumberNode(2.0);
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->division(new NumberNode(3.0), new RationalNode(3, 2));
        $shouldBe = new NumberNode(2.0);
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->division(new NumberNode(3.0), new IntegerNode(2));
        $shouldBe = new NumberNode(1.5);
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->division(new RationalNode(1, 2), new RationalNode(1, 3));
        $shouldBe = new RationalNode(3, 2);
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->division(new RationalNode(1, 2), new IntegerNode(2));
        $shouldBe = new RationalNode(1, 4);
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->division(new IntegerNode(1), new IntegerNode(2));
        $shouldBe = new RationalNode(1, 2);
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->division(new VariableNode('x'), new VariableNode('x'));
        $shouldBe = new IntegerNode(1);
        $this->assertNodesEqual($node, $shouldBe);
    }

    public function testDivisionNodeFactoryThrows()
    {
        $this->expectException(DivisionByZeroException::class);
        $node = $this->factory->division(new VariableNode('x'), new IntegerNode(0));
    }

    public function testDivisionNodeFactoryThrows2()
    {
        $this->expectException(DivisionByZeroException::class);
        $node = $this->factory->division(new VariableNode('x'), new RationalNode(0, 2));
    }

    public function testDivisionNodeFactoryThrows3()
    {
        $this->expectException(DivisionByZeroException::class);
        $node = $this->factory->division(new VariableNode('x'), new NumberNode(0));
    }

    public function testExponentiationNodeFactory()
    {
        $node = $this->factory->exponentiation(new VariableNode('x'), new VariableNode('y'));
        $shouldBe = $this->parser->parse('x^y');
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->exponentiation(new VariableNode('x'), new IntegerNode(0));
        $shouldBe = new IntegerNode(1);
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->exponentiation(new VariableNode('x'), new RationalNode(0, 2));
        $shouldBe = new IntegerNode(1);
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->exponentiation(new VariableNode('x'), new NumberNode(0.0));
        $shouldBe = new IntegerNode(1);
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->exponentiation(new VariableNode('x'), new IntegerNode(1));
        $shouldBe = new VariableNode('x');
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->exponentiation(new VariableNode('x'), new RationalNode(1, 1));
        $shouldBe = new VariableNode('x');
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->exponentiation(new VariableNode('x'), new NumberNode(1.0));
        $shouldBe = new VariableNode('x');
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->exponentiation(new NumberNode(2.0), new NumberNode(3.0));
        $shouldBe = new NumberNode(8.0);
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->exponentiation(new IntegerNode(2), new IntegerNode(3));
        $shouldBe = new IntegerNode(8);
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->exponentiation(new RationalNode(1, 2), new RationalNode(1, 2));
        $shouldBe = $this->parser->parse('(1/2)^(1/2)');
        $this->assertNodesEqual($node, $shouldBe);

        $op1 = $this->factory->exponentiation(new VariableNode('x'), new IntegerNode(2));
        $node = $this->factory->exponentiation($op1, new VariableNode('x'));
        $shouldBe = $this->parser->parse('x^(2*x)');
        $this->assertNodesEqual($node, $shouldBe);
    }

    public function testMultiplicationNodeFactory()
    {
        $node = $this->factory->multiplication(new VariableNode('x'), new IntegerNode(1));
        $shouldBe = new VariableNode('x');
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->multiplication(new VariableNode('x'), new NumberNode(1.0));
        $shouldBe = new VariableNode('x');
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->multiplication(new VariableNode('x'), new RationalNode(1, 1));
        $shouldBe = new VariableNode('x');
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->multiplication(new IntegerNode(1), new VariableNode('x'));
        $shouldBe = new VariableNode('x');
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->multiplication(new NumberNode(1.0), new VariableNode('x'));
        $shouldBe = new VariableNode('x');
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->multiplication(new RationalNode(1, 1), new VariableNode('x'));
        $shouldBe = new VariableNode('x');
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->multiplication(new IntegerNode(0), new VariableNode('x'));
        $shouldBe = new IntegerNode(0);
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->multiplication(new RationalNode(0, 1), new VariableNode('x'));
        $shouldBe = new IntegerNode(0);
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->multiplication(new NumberNode(0), new VariableNode('x'));
        $shouldBe = new IntegerNode(0);
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->multiplication(new VariableNode('x'), new IntegerNode(0));
        $shouldBe = new IntegerNode(0);
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->multiplication(new VariableNode('x'), new RationalNode(0, 2));
        $shouldBe = new IntegerNode(0);
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->multiplication(new VariableNode('x'), new NumberNode(0));
        $shouldBe = new IntegerNode(0);
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->multiplication(new VariableNode('x'), new VariableNode('y'));
        $shouldBe = $this->parser->parse('x*y');
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->multiplication(new NumberNode(3.0), new NumberNode(1.5));
        $shouldBe = new NumberNode(4.5);
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->multiplication(new NumberNode(3.0), new RationalNode(3, 2));
        $shouldBe = new NumberNode(4.5);
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->multiplication(new NumberNode(3.0), new IntegerNode(2));
        $shouldBe = new NumberNode(6.0);
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->multiplication(new RationalNode(1, 2), new RationalNode(1, 3));
        $shouldBe = new RationalNode(1, 6);
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->multiplication(new RationalNode(1, 2), new IntegerNode(2));
        $shouldBe = new RationalNode(1, 1);
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->multiplication(new IntegerNode(1), new IntegerNode(2));
        $shouldBe = new IntegerNode(2);
        $this->assertNodesEqual($node, $shouldBe);

    }

    public function testSubtractionNodeFactory()
    {
        $node = $this->factory->subtraction(new VariableNode('x'), new IntegerNode(0));
        $shouldBe = new VariableNode('x');
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->subtraction(new VariableNode('x'), new RationalNode(0, 1));
        $shouldBe = new VariableNode('x');
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->subtraction(new VariableNode('x'), new NumberNode(0));
        $shouldBe = new VariableNode('x');
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->subtraction(new VariableNode('x'), new VariableNode('y'));
        $shouldBe = $this->parser->parse('x-y');
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->subtraction(new VariableNode('x'), new VariableNode('x'));
        $shouldBe = new IntegerNode(0);
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->subtraction(new NumberNode(1.5), new NumberNode(2.5));
        $shouldBe = new NumberNode(-1.0);
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->subtraction(new NumberNode(1.5), new RationalNode(1, 2));
        $shouldBe = new NumberNode(1.0);
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->subtraction(new NumberNode(1.5), new IntegerNode(1));
        $shouldBe = new NumberNode(0.5);
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->subtraction(new RationalNode(1, 2), new RationalNode(1, 3));
        $shouldBe = new RationalNode(1, 6);
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->subtraction(new RationalNode(1, 2), new IntegerNode(1));
        $shouldBe = new RationalNode(-1, 2);
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->subtraction(new IntegerNode(2), new IntegerNode(1));
        $shouldBe = new IntegerNode(1);
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->subtraction(new NumberNode(1.5), null);
        $shouldBe = new NumberNode(-1.5);
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->subtraction(new RationalNode(1, 5), null);
        $shouldBe = new RationalNode(-1, 5);
        $this->assertNodesEqual($node, $shouldBe);

        $node = $this->factory->subtraction(new IntegerNode(1), null);
        $shouldBe = new IntegerNode(-1);
        $this->assertNodesEqual($node, $shouldBe);

        $op1 = $this->factory->subtraction(new VariableNode('x'), null);
        $node = $this->factory->subtraction($op1, null);
        $shouldBe = new VariableNode('x');
        $this->assertNodesEqual($node, $shouldBe);

    }

}
