<?php
/*
 * @package     Parsing
 * @author      Frank Wikström <frank@mossadal.se>
 * @copyright   2015 Frank Wikström
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 *
 */

namespace MathParser\Parsing\Nodes;

use MathParser\Interpreting\Visitors\Visitor;

/**
 * AST node representing a variable
 */
class VariableNode extends Node
{
    /** string $name Name of represented variable, e.g. 'x' */
    private $name;

    /** Constructor. Create a VariableNode with a given variable name. */
    function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Return the name of the variable
     * @retval string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Implementing the Visitable interface.
     */
    public function accept(Visitor $visitor)
    {
        return $visitor->visitVariableNode($this);
    }

    /** Implementing the compareTo abstract method. */
    public function compareTo($other)
    {
        if ($other === null) {
            return false;
        }
        if (!($other instanceof VariableNode)) {
            return false;
        }

        return $this->getName() == $other->getName();
    }

}
