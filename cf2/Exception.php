<?php


namespace calderawp\calderaforms\cf2;


class Exception extends \Exception
{
    /**
     * @param array $data
     * @return \WP_Error
     */
    public function toWpError(array $data = [])
    {
        return new \WP_Error($this->getCode(), $this->getMessage(), $data);
    }

    /**
     * @param array $data
     * @param array $headers
     * @return \WP_REST_Response
     */
    public function toResponse(array $data = [], array $headers = [])
    {
        $data = array_merge($data, ['message' => $this->getMessage()]);
        return new \WP_REST_Response($data, absint($this->getCode() ? $this->getCode() : 500), $headers);
    }

    /**
     * Convert any Exception to this type of Exception
     *
     * @param \Exception $exception
     * @return Exception
     */
    public static function formOtherException(\Exception $exception)
    {
        return new static(
            $exception->getMessage(),
            $exception->getCode(),
            $exception
        );
    }

    /**
     * Convert a WP_Error object to an Exception
     * @param \WP_Error $error
     * @return Exception
     */
    public static function fromWpError(\WP_Error $error)
    {
        return new static(
            $error->get_error_message(),
            $error->get_error_code()
        );
    }
}