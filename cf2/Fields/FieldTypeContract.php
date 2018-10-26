<?php


namespace calderawp\calderaforms\cf2\Fields;


interface FieldTypeContract
{
    /**
     * Get the field's type
     *
     * @since 1.8.0
     *
     * @return string
     */
    public static function getType();

    /**
     * Get the field's identifier for use in cf1
     *
     * @since 1.8.0
     *
     * @return string
     */
    public static function getCf1Identifier();
}