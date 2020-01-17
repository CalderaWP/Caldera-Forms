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
  * Exception thrown when tokenizing expressions containing illegal
  * characters.
  */
class UnknownTokenException extends MathParserException
{
    /** Constructor. Create a UnknownTokenException */
    public function __construct($name)
    {
        parent::__construct("Unknown token $name encountered");

        $this->data = $name;
    }

    /**
     * Get the unknown token that was encountered.
     *
     * @retval string
     */
    public function getName()
    {
    	return $this->data;
    }
}
