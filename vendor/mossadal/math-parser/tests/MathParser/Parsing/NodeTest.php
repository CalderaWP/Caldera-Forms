<?php

use MathParser\Interpreting\TreePrinter;
use MathParser\Parsing\Nodes\ConstantNode;
use MathParser\Parsing\Nodes\ExpressionNode;
use MathParser\Parsing\Nodes\Factories\DivisionNodeFactory;
use MathParser\Parsing\Nodes\Factories\NodeFactory;
use MathParser\Parsing\Nodes\FunctionNode;
use MathParser\Parsing\Nodes\IntegerNode;
use MathParser\Parsing\Nodes\NumberNode;
use MathParser\Parsing\Nodes\RationalNode;
use MathParser\Parsing\Nodes\SubExpressionNode;
use MathParser\Parsing\Nodes\VariableNode;
use MathParser\RationalMathParser;
use PHPUnit\Framework\TestCase;

class NodeTest extends TestCase
{
    private $parser;
    private $factory;

    public function setUp()
    {
        $this->parser = new RationalMathParser();
        $this->factory = new NodeFactory();
    }

    public function testCanCompareSubExpressionNodes()
    {
        $node = new SubExpressionNode('%');
        $other = new NumberNode(1);

        $this->assertFalse($node->compareTo(null));
        $this->assertFalse($node->compareTo($other));
        $this->assertTrue($node->compareTo($node));
        $this->assertFalse($node->compareTo(new SubExpressionNode('$')));
    }

    public function testCanCompareConstantNodes()
    {
        $node = new ConstantNode('pi');
        $other = new NumberNode(1);

        $this->assertFalse($node->compareTo(null));
        $this->assertFalse($node->compareTo($other));
        $this->assertTrue($node->compareTo($node));
        $this->assertFalse($node->compareTo(new ConstantNode('e')));
    }

    public function testCanCompareFunctionNodes()
    {
        $node = new FunctionNode('sin', new VariableNode('x'));
        $other = new NumberNode(1);

        $this->assertFalse($node->compareTo(null));
        $this->assertFalse($node->compareTo($other));
        $this->assertTrue($node->compareTo($node));
        $this->assertFalse($node->compareTo(new FunctionNode('cos', new VariableNode('x'))));
    }

    public function testCanCompareVariableNodes()
    {
        $node = new VariableNode('x');
        $other = new NumberNode(1);

        $this->assertFalse($node->compareTo(null));
        $this->assertFalse($node->compareTo($other));
        $this->assertTrue($node->compareTo($node));
        $this->assertFalse($node->compareTo(new VariableNode('y')));
    }

    public function testCanCompareNumberNodes()
    {
        $node = new NumberNode(3);
        $other = new VariableNode('x');
        $inode = new IntegerNode(2);
        $rnode = new RationalNode(4, 2);

        $this->assertFalse($node->compareTo(null));
        $this->assertFalse($node->compareTo($other));
        $this->assertTrue($node->compareTo($node));
        $this->assertFalse($node->compareTo(new IntegerNode(7)));

        $this->assertTrue($inode->compareTo($rnode));
        $this->assertTrue($rnode->compareTo($inode));
        $this->assertTrue($inode->compareTo($inode));
        $this->assertTrue($rnode->compareTo($rnode));
        $this->assertFalse($inode->compareTo(new Integernode(3)));

        $this->assertFalse($node->compareTo(null));
        $this->assertFalse($other->compareTo(null));
        $this->assertFalse($inode->compareTo(null));
        $this->assertFalse($rnode->compareTo(null));

        $this->assertFalse($rnode->compareTo(new IntegerNode(3)));
        $this->assertFalse($rnode->compareTo($other));

        $this->assertFalse($inode->compareTo(new RationalNode(3, 5)));
        $this->assertFalse($inode->compareTo($other));

    }

    public function testCanCompareExpressionNodes()
    {
        $node = new ExpressionNode(new VariableNode('x'), '+', new VariableNode('y'));
        $node2 = new ExpressionNode(new VariableNode('x'), '-', new VariableNode('y'));
        $node3 = new ExpressionNode(new VariableNode('x'), '-', null);
        $node4 = new ExpressionNode(null, '-', new VariableNode('y'));
        $other = new VariableNode('x');

        $this->assertFalse($node->compareTo(null));
        $this->assertFalse($node->compareTo($other));
        $this->assertTrue($node->compareTo($node));
        $this->assertTrue($node2->compareTo($node2));
        $this->assertTrue($node3->compareTo($node3));
        $this->assertTrue($node4->compareTo($node4));

        $this->assertFalse($node->compareTo($node2));
        $this->assertFalse($node->compareTo($node3));
        $this->assertFalse($node->compareTo($node4));
        $this->assertFalse($node2->compareTo($node3));
        $this->assertFalse($node2->compareTo($node4));
        $this->assertFalse($node3->compareTo($node4));
        $this->assertFalse($node2->compareTo($node4));

    }

    public function testCanCompareUnaryMinus()
    {
        $node = new FunctionNode('sin', new ExpressionNode(new VariableNode('x'), '-', null));
        $other = new FunctionNode('sin', new ExpressionNode(new VariableNode('a'), '-', null));

        $this->assertTrue($node->compareTo($node));
        $this->assertTrue($other->compareTo($other));
        $this->assertFalse($node->compareTo($other));
        $this->assertFalse($other->compareTo($node));
    }

    public function testCanComputeComplexity()
    {
        $node = new NumberNode(1);
        $this->assertEquals($node->complexity(), 2);

        $node = new IntegerNode(1);
        $this->assertEquals($node->complexity(), 1);

        $node = new RationalNode(1, 2);
        $this->assertEquals($node->complexity(), 2);

        $node = new VariableNode('x');
        $this->assertEquals($node->complexity(), 1);

        $node = new ConstantNode('pi');
        $this->assertEquals($node->complexity(), 1);

        $f = $this->parser->parse('x+y');
        $this->assertEquals($f->complexity(), 3);

        $f = $this->parser->parse('x-y');
        $this->assertEquals($f->complexity(), 3);

        $f = $this->parser->parse('x*y');
        $this->assertEquals($f->complexity(), 3);

        $f = $this->parser->parse('x/y');
        $this->assertEquals($f->complexity(), 5);

        $f = $this->parser->parse('x^y');
        $this->assertEquals($f->complexity(), 10);

        $f = $this->parser->parse('sin(x)');
        $this->assertEquals($f->complexity(), 6);

        $f = $this->parser->parse('x + sin(x^2)');
        $this->assertEquals($f->complexity(), 17);

        $node = new SubExpressionNode('(');
        $this->assertEquals($node->complexity(), 1000);
    }

    public function testCanCreateSubExpressionNode()
    {
        $node = new SubExpressionNode('%');
        $this->assertEquals($node->getValue(), '%');
        $this->assertNull($node->accept(new TreePrinter()));
    }

    public function testCanCreateIntegerNode()
    {
        $node = new IntegerNode(1);
        $this->assertEquals($node->getValue(), 1);

        $node = new IntegerNode(-1);
        $this->assertEquals($node->getValue(), -1);

        $this->expectException(\UnexpectedValueException::class);
        $node = new IntegerNode(1.2);
    }

    public function testCanCreateRationalNode()
    {
        $node = new RationalNode(1, 2);
        $this->assertEquals($node->getNumerator(), 1);
        $this->assertEquals($node->getDenominator(), 2);

        $this->assertEquals($node->getValue(), 0.5);

        $node = new RationalNode(4, 8);
        $this->assertEquals($node->getNumerator(), 1);
        $this->assertEquals($node->getDenominator(), 2);

        $node = new RationalNode(-1, 2);
        $this->assertEquals($node->getNumerator(), -1);
        $this->assertEquals($node->getDenominator(), 2);

        $node = new RationalNode(1, -2);
        $this->assertEquals($node->getNumerator(), -1);
        $this->assertEquals($node->getDenominator(), 2);

        $this->expectException(\UnexpectedValueException::class);
        $node = new RationalNode(1.2, 2);
    }

    public function testDivisionNodeFactory()
    {
        $factory = new DivisionNodeFactory();

        $this->assertTrue(
            $factory->makeNode(new IntegerNode(1), new IntegerNode(3))->compareTo(
                new RationalNode(1, 3)
            )
        );
        $this->assertTrue(
            $factory->makeNode(new RationalNode(2, 3), new RationalNode(3, 5))->compareTo(
                new RationalNode(10, 9)
            )
        );
    }

}
