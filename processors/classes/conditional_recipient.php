<?php

/**
 * Handle conditional recipients
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_Processor_Conditional_Recipient  {

	/**
	 * An array of recipients to add
	 *
	 * @since 1.5.0
	 *
	 * @var array
	 */
	protected static $recipients;

	/**
	 * Find recipient for a processor that passed conditional logic
	 *
	 * @since 1.5.0
	 *
	 * @param array $config Processor config
	 */
	public static function post_processor( $config  ){

		if( is_email( trim( $config[ 'conditional-recipient' ] ) )){
			self::$recipients[] = $config[ 'conditional-recipient' ];
		}else{
			// not an email - try convert to field
			$field = trim( Caldera_Forms::do_magic_tags($config['conditional-recipient']) );
			if(is_email( $field )){
				self::$recipients[] = $field;
			}
		}

		if( ! empty( $config[ 'remove-default' ]  ) && $config[ 'remove-default' ] ){
			add_filter( 'caldera_forms_mailer', array( __CLASS__, 'remove_default' ), 2  );
		}

		add_filter( 'caldera_forms_mailer', array( __CLASS__, 'set_mailer_recipients') );
	}


	/**
	 * Add the conditional recipients to the mailer
	 *
	 * @since 1.5.0
	 *
	 * @uses "caldera_forms_mailer" filter
	 *
	 * @param array $mail Mail config
	 *
	 * @return array
	 */
	public static function set_mailer_recipients( $mail ){
		if ( ! empty( self::$recipients ) && is_array( self::$recipients ) ) {
			foreach ( self::$recipients as $recipient ) {
				if ( ! in_array( $recipient, $mail[ 'recipients' ] ) ) {
					$mail[ 'recipients' ][] = $recipient;
				}
			}
		}

		return $mail;
	}

	/**
	 * Remove preset recipients
	 *
	 * @since 1.5.0
	 *
	 * @uses "caldera_forms_mailer" filter
	 *
	 * @param array $mail Mail config
	 *
	 * @return array
	 */
	public static function remove_default( $mail ){
		$mail[ 'recipients' ] = array();
		return $mail;
	}


}