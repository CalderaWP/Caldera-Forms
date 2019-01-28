<?php

/**
 * Class Caldera_Forms_Entry_Factory
 *
 * Factories for entry objects

 */
class Caldera_Forms_Entry_Factory
{

    /**
     * Factory for Caldera_Forms_Entry_Field objects
     *
     * @since 1.7.0
     *
     * @param array|stdClass|Caldera_Forms_Entry_Field $entry_field
     * @return Caldera_Forms_Entry_Field|object
     */
    public static function entry_field($entry_field){
        if( ! is_a( $entry_field, Caldera_Forms_Entry_Field::class ) ){
            if( is_array( $entry_field )){
                $entry_field = (object)$entry_field;
            }
            if( is_object($entry_field)){
                $entry_field = new Caldera_Forms_Entry_Field($entry_field);
            }
        }

        return $entry_field;
    }

}