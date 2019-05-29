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
  * Exception thrown when parsing expressions having non-matching left and
  * right parentheses.
  */
class ParenthesisMismatchException extends MathParserException
{
    /** Constructor. Create a ParenthesisMismatchException */
    public function __construct($data = null)
    {
        parent::__construct("Unable to match delimiters.");

        $this->data = $data;
    }

    /**
     * Get the incorrect data that was encountered.
     *
     * @retval string
     */
    public function getData()
    {
        return $this->data;
    }
}
