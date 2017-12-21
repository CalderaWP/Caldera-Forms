<?php

/**
 * Functions related to fallback email
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2017 CalderaWP LLC
 */
class Caldera_Forms_Email_Fallback{

    /**
     * Get fallback email sender address
     *
     * @since 1.5.8
     *
     * @param array $form
     * @return string
     */
    public static function get_fallback( array  $form ){

        /**
         * Change the fallback email used when no valid email is passed
         *
         * @since 1.5.8
         *
         * @param string $email Fallback email, by default admin email
         * @para array $form Form config for form fallback is being used for
         */
        return apply_filters( 'caldera_forms_fallback_email', get_option( 'admin_email' ), $form );
    }

}