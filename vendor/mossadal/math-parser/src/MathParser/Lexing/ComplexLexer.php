<?php
/*
 * @package     Lexical analysis
 * @author      Frank Wikström <frank@mossadal.se>
 * @copyright   2016 Frank Wikström
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 *
 */

namespace MathParser\Lexing;

use MathParser\Lexing\Lexer;
use MathParser\Lexing\TokenDefinition;
use MathParser\Lexing\TokenType;

/**
 * Lexer capable of recognizing all standard complex mathematical expressions.
 *
 * Subclass of the generic Lexer, with TokenDefinition patterns for
 * numbers, elementary functions, arithmetic operations and variables.
 *
 * ### Recognized tokens
 *
 *  `/\~?\d+/` matching integers matching
 *  `/sqrt/`  matching square root function
 *  `/sinh/` matching hyperbolic sine
 *  `/cosh/` matching hyperbolic cosine
 *  `/tanh/` matching hyperbolic tangent
 *  `/coth/` matching hyperbolic cotangent
 *  `/sin/` matching sine
 *  `/cos/` matching cosine
 *  `/tan/` matching tangent
 *  `/cot/` matching cotangent
 *  `/arsinh|arcsinh|asinh/` matching inverse hyperbolic sine
 *  `/arcosh|arccosh|acosh/` matching inverse hyperbolic cosine
 *  `/artanh|arctanh|atanh/` matching inverse hyperbolic tangent
 *  `/arcoth|arccoth|acoth/` matching inverse hyperbolic cotangent
 *  `/arcsin|asin/` matching inverse sine
 *  `/arccos|acos/` matching inverse cosine
 *  `/arctan|atan/` matching inverse tangent
 *  `/arccot|acot/` matching inverse cotangent
 *  `/exp/` matching exponential function
 *  `/log10|lg/` matching logarithm (base 10)
 *  `/log|ln/` matching natural logarithm
 *  '/abs/' matching modulus (absolute value)
 *  '/arg/' matching (principal) argument
 *  '/conj/' matching conjugate
 *  '/re/' matching real part
 *  '/im/' matching imaginary part
 *  `/\(/` matching opening parenthesis (both as delimiter and function evaluation)
 *  `/\)/` matching closing parenthesisis (both as delimiter and function evaluation)
 *  `/\+/` matching + for addition (or unary +)
 *  `/\-/` matching - for subtraction (or unary -)
 *  `/\* /` matching * for multiplication
 *  `/\//` matching / for division
 *  `/\^/` matching ^ for exponentiation
 *  `/pi/` matching constant pi
 *  `/e/` matching constant e
 *  `/i/` matching imaginary unit i
 *  `/[a-zA-Z]/` matching variables (note that we only allow single letter identifiers,
 * this improves parsing of implicit multiplication)
 *  `/\n/` matching newline
 *  `/\s+/` matching whitespace
 */
class ComplexLexer extends Lexer
{
    public function __construct()
    {
        $this->add(new TokenDefinition('/\d+[,\.]\d+/', TokenType::RealNumber));
        $this->add(new TokenDefinition('/\d+/', TokenType::PosInt));

        $this->add(new TokenDefinition('/sqrt/', TokenType::FunctionName));

        $this->add(new TokenDefinition('/sinh/', TokenType::FunctionName));
        $this->add(new TokenDefinition('/cosh/', TokenType::FunctionName));
        $this->add(new TokenDefinition('/tanh/', TokenType::FunctionName));
        $this->add(new TokenDefinition('/coth/', TokenType::FunctionName));

        $this->add(new TokenDefinition('/sin/', TokenType::FunctionName));
        $this->add(new TokenDefinition('/cos/', TokenType::FunctionName));
        $this->add(new TokenDefinition('/tan/', TokenType::FunctionName));
        $this->add(new TokenDefinition('/cot/', TokenType::FunctionName));

        $this->add(new TokenDefinition('/arsinh|arcsinh|asinh/', TokenType::FunctionName, 'arsinh'));
        $this->add(new TokenDefinition('/arcosh|arccosh|acosh/', TokenType::FunctionName, 'arcosh'));
        $this->add(new TokenDefinition('/artanh|arctanh|atanh/', TokenType::FunctionName, 'artanh'));
        $this->add(new TokenDefinition('/arcoth|arccoth|acoth/', TokenType::FunctionName, 'arcoth'));

        $this->add(new TokenDefinition('/arcsin|asin/', TokenType::FunctionName, 'arcsin'));
        $this->add(new TokenDefinition('/arccos|acos/', TokenType::FunctionName, 'arccos'));
        $this->add(new TokenDefinition('/arctan|atan/', TokenType::FunctionName, 'arctan'));
        $this->add(new TokenDefinition('/arccot|acot/', TokenType::FunctionName, 'arccot'));

        $this->add(new TokenDefinition('/exp/', TokenType::FunctionName));
        $this->add(new TokenDefinition('/log10|lg/', TokenType::FunctionName, 'lg'));
        $this->add(new TokenDefinition('/log/', TokenType::FunctionName, 'log'));
        $this->add(new TokenDefinition('/ln/', TokenType::FunctionName, 'ln'));

        $this->add(new TokenDefinition('/abs/', TokenType::FunctionName));
        $this->add(new TokenDefinition('/arg/', TokenType::FunctionName));
        $this->add(new TokenDefinition('/conj/', TokenType::FunctionName));
        $this->add(new TokenDefinition('/re/', TokenType::FunctionName));
        $this->add(new TokenDefinition('/im/', TokenType::FunctionName));

        $this->add(new TokenDefinition('/\(/', TokenType::OpenParenthesis));
        $this->add(new TokenDefinition('/\)/', TokenType::CloseParenthesis));

        $this->add(new TokenDefinition('/\+/', TokenType::AdditionOperator));
        $this->add(new TokenDefinition('/\-/', TokenType::SubtractionOperator));
        $this->add(new TokenDefinition('/\*/', TokenType::MultiplicationOperator));
        $this->add(new TokenDefinition('/\//', TokenType::DivisionOperator));
        $this->add(new TokenDefinition('/\^/', TokenType::ExponentiationOperator));

        $this->add(new TokenDefinition('/pi/', TokenType::Constant));
        $this->add(new TokenDefinition('/e/', TokenType::Constant));
        $this->add(new TokenDefinition('/i/', TokenType::Constant));

        $this->add(new TokenDefinition('/[a-zA-Z]/', TokenType::Identifier));

        $this->add(new TokenDefinition('/\n/', TokenType::Terminator));
        $this->add(new TokenDefinition('/\s+/', TokenType::Whitespace));

    }
}
