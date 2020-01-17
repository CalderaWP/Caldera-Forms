<?php
/*
 * @package     Exceptions
 * @author      Frank Wikström <frank@mossadal.se>
 * @copyright   2015 Frank Wikström
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 *
 */

/**
  * @namespace MathParser::Exceptions
  *
  * Exceptions thrown by the MathParser library.
  */
namespace MathParser\Exceptions;

/**
 * Base class for the exceptions thrown by the MathParser library.
 */
abstract class MathParserException extends \Exception
{
    /** @var string Additional information about the exception. */
    protected $data;

    /**
     * Get additional information about the exception.
     *
     * @retval string
     */
    public function getData()
    {
        return $this->data;
    }

}
