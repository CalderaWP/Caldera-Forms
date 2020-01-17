<?php
/*
 * @package     Exceptions
 * @author      Frank Wikström <frank@mossadal.se>
 * @copyright   2015 Frank Wikström
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 *
 */

 namespace MathParser\Exceptions;

 /**
  * Exception thrown when parsing expressions that are not well-formed.
  */
class SyntaxErrorException extends MathParserException
{
    /** Constructor. Create a SyntaxErrorException */
    public function __construct()
    {
        parent::__construct("Syntax error.");
    }
}
