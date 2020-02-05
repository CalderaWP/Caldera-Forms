<?php

/**
 * Interface Caldera_Forms_Processor_Data
 *
 * Contract for Caldera_Forms_Processor_Get_Data
 */
interface Caldera_Forms_Processor_Data{
    /**
     * Add an error message to the errors property
     *
     * @param string $message Message for error
     * @since 1.8.10
     *
     */
    public function add_error($message);

    /**
     * Get the errors
     *
     * @return array|null
     * @since 1.8.10
     *
     */
    public function get_errors();

    /**
     * Get the values
     *
     * @return array|null
     * @since 1.8.10
     *
     */
    public function get_values();

    /**
     * Get prepared fields
     *
     * @return array|null
     * @since 1.8.10
     *
     */
    public function get_fields();

    /**
     * Get one value from the processor
     *
     * @param string $field Name of field
     * @param mixed $default Optional. Default value to return if none set.
     *
     * @return mixed
     * @since 1.8.10
     *
     */
    public function get_value($field, $default = null);
}