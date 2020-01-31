<?php

use MathParser\Lexing\TokenDefinition;
use MathParser\Lexing\TokenType;
use PHPUnit\Framework\TestCase;

class TokenDefinitionTest extends TestCase
{
    private $tokenDefinition;

    public function setUp()
    {
        $this->tokenDefinition = new TokenDefinition('/\d+/', TokenType::PosInt);
    }

    public function testMatchReturnsTokenObjectIfMatchedInput()
    {
        $token = $this->tokenDefinition->match('123');

        $this->assertInstanceOf('MathParser\Lexing\Token', $token);

        $this->assertEquals('123', $token->getValue());
        $this->assertEquals(TokenType::PosInt, $token->getType());
    }

    public function testNoMatchReturnsNull()
    {
        $this->assertNull($this->tokenDefinition->match('@'));
    }

    public function testMatchReturnsNullIfOffsetNotZero()
    {
        $this->assertNull($this->tokenDefinition->match('@123'));
    }
}
