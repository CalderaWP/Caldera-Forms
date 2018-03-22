<?php

/**
 * Class Caldera_Forms_Email_Prepare
 *
 * Methods for preparing email address strings.
 * Useful when dealing with strings that may be in the rfc822 format -- "name <email@example.com>"
 */
class Caldera_Forms_Email_Prepare{

    /**
     * Get email from rfc822 string
     *
     * @since 1.6.0
     *
     * @param string $rfc rfc822 (or not) email string
     * @return string|null
     */
    public static function email_rfc822($rfc){
        $regs = self::match_rfc($rfc);
        if ( ! empty( $regs )) {
            $parsed = $regs[0];
            return $parsed;
        }
        return null;
    }

    /**
     * If possible, get name from rfc_822 string
     *
     * @since 1.6.0
     *
     * @param string $rfc Email and name. SHOULD be in rfc_822 form
     * @return string|null Name only, if string was rfc_822. Null if not
     */
    public static function email_from_rfc($rfc){
        if (self::is_rfc822( $rfc)) {
            $regs = self::match_rfc($rfc);
            return str_replace(array($regs[0], '<', '>'), '', $rfc);
        }
        return null;
    }

    /**
     * Check if email is in rfc822 format
     *
     * @since 1.6.0
     *
     * @param string $email Email to test
     * @return bool
     */
    public static function is_rfc822($email){
        return ! is_null( self::email_rfc822( $email));
    }

    /**
     * Formats one or more emails into well formed array
     *
     * @since 1.6.0
     *
     * NOTE: This array form is intentionally the same structure as expected by the fromArray() method for recipients in CF Pro and caldera-interop
     *
     * @param array|string $emails Emails to prepare
     * @return array
     */
    public static function prepare_email_array( $emails ){
        $prepared = array();
        if( is_string( $emails ) ){
            $emails = array( $emails );
        }

        foreach ( $emails as &$email ){
            if( self::is_rfc822( $email ) ){
                $prepared[] = array(
                    'name' => self::email_from_rfc( $email ),
                    'email' => self::email_rfc822( $email )
                );
            }else{
                $prepared[] = array(
                    'name' => '',
                    'email' => $email
                );
            }
        }

        return $prepared;
    }

    /**
     * Check if string has comma and therefore is probably a list of email addresses
     *
     * @since 1.6.0
     *
     * @param  string $email_string Email address(s)
     * @return bool
     */
    public static function is_list($email_string){
        if( false !== strpos($email_string, ',' ) ){
            return true;
        }

        return false;
    }

    /**
     * Use pregmatch to detech emails in rfc_822 string
     *
     * @since 1.6.0
     *
     * @param string $rfc Email string
     * @return array
     */
    protected static function match_rfc($rfc) {
        preg_match('/[\w\.\-+=*_]*@[\w\.\-+=*_]*/', $rfc, $regs);
        return $regs;
    }
}