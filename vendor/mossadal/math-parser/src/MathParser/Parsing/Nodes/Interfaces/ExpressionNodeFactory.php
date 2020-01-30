<?php
/*
* @package     Parsing
* @author      Frank Wikström <frank@mossadal.se>
* @copyright   2015 Frank Wikström
* @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
*
*/

/**
 * @namespace MathParser::Parsing::Nodes::Interfaces
 *
 * Interfaces for Nodes, in particular Node factories.
 */
namespace MathParser\Parsing\Nodes\Interfaces;

use MathParser\Parsing\Nodes\NumberNode;

/**
 * Interface for construction of ExpressionNode, the
 * implementations of the interface, usually involves
 * some simplification of the operands.
 *
 */
interface ExpressionNodeFactory
{
    /**
    * Factory method to create an ExpressionNode with given operands.
    *
    * @param mixed $leftOperand
    * @param mixed $rightOperand
    * @retval ExpressionNode|NumberNode
    */
    public function makeNode($leftOperand, $rightOperand);
}
