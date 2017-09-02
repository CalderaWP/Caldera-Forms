<?php

/**
 * Honey-pot related functions
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2017 CalderaWP LLC
 */
class Caldera_Forms_Field_Honey{

    /**
     * Check honey pot
     *
     * @since 1.5.5
     *
     * @param array $data Submission data
     * @param array $form Form config
     *
     * @return bool
     */
    public static function check( array $data, $form ){
        $honey_words = self::words( $form );
        foreach ( $data as $honey_word => $honey_value ) {

            if ( ! is_array( $honey_value ) && strlen( $honey_value ) && in_array( $honey_word, $honey_words ) ) {
                return false;

            }
        }

        return true;
    }

    /**
     * Create URL for redirect triggered by honey pot failure
     *
     * @since 1.5.5
     *
     * @param array $referrer  Parsed HTTP referrer
     * @param string|int $form_instance_number Current form instance identifier
     * @param string $process_id Process ID for current submission attempt
     * @return string
     */
    public static function redirect_url( $referrer, $form_instance_number, $process_id ){
        $referrer[ 'query' ][ 'cf_su' ] = $form_instance_number;
        $query_str                      = array(
            'cf_er' => $process_id
        );
        if ( ! empty( $referrer[ 'query' ] ) ) {
            $query_str = array_merge( $referrer[ 'query' ], $query_str );
        }
        $url = add_query_arg( $query_str, $referrer[ 'path' ] );

        /**
         * Change URL URL for redirect triggered by honey pot failure
         *
         * @since 1.5.5
         *
         * @param string $url The URL to use
         */
        return apply_filters( 'caldera_forms_honey_redirect_url', $url );

    }

    /**
     * Create HTML markup for honey pot field
     *
     * @since 1.5.5
     *
     * @param array $form Form config
     * @return string
     */
    public static function field( array  $form ){
        $field = "<div class=\"hide\" style=\"display:none; overflow:hidden;height:0;width:0;\">\r\n";

        $honey_words = self::words( $form );
        $word        = $honey_words[ rand( 0, count( $honey_words ) - 1 ) ];
        $field .= "<label>" . esc_html__( ucwords( str_replace( '_', ' ', $word ) ) ) . "</label><input type=\"text\" name=\"" . esc_attr( $word ) . "\" value=\"\" autocomplete=\"off\">\r\n";
        $field .= "</div>";
        return $field;

    }

    /**
     * Is honey pot field active?
     *
     * @since 1.5.5
     *
     * @param array $form Form config
     * @return bool
     */
    public static function active( $form ){
       return isset( $form[ 'check_honey' ] );
    }

    /**
     * Create array of possible honey words
     *
     * Checks that there isn't a field ID that is one of the words and eliminates those.
     * Normally this does not happen because field IDs, which become field name attributes are fld12345, but they can be anything.
     *
     * @since 1.5.5
     *
     * @param array $form Form config Form config
     * @return array
     */
    protected static function words( $form ){
        $honey_words = apply_filters( 'caldera_forms_get_honey_words', array(
            'web_site',
            'url',
            'email',
            'company',
            'name',
            'phone',
            'twitter',
            'order_number'
        ) );

        foreach( Caldera_Forms_Forms::get_fields( $form, false ) as $id => $field ){
            if( in_array( $field[ 'ID' ], $honey_words  ) ){
                unset( $honey_words[ $field[ 'ID' ] ] );
            }
        }

        //This is sub-optimal, but need something.
        if( empty( $honey_words ) ){
            $honey_words = array( home_url() );
        }

        return $honey_words;

    }

}