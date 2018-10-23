<?php


namespace calderawp\calderaforms\cf2\Fields;


interface FieldTypeContract
{
    /**
     * Get the field's type
     *
     * @since 1.8.0
     */
    public static function getType();
    public static function getCf1Identifier();
}