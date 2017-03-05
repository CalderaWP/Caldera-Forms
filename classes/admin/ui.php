<?php

/**
 * Admin UI field generator cache layer
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2017 CalderaWP LLC
 */
class Caldera_Forms_Admin_UI {

	/**
	 * The Admin fields generator interface
	 *
	 * DO NOT Use directly, use getter method
	 *
	 * @since 1.5.1
	 *
	 * @var Caldera_Forms_Admin_Fields
	 */
	protected static $generator;

	/**
	 * Holds a generic object for placeholder field
	 *
	 * DO NOT Use directly, use getter method
	 *
	 * @since 1.5.1
	 *
	 * @var Caldera_Forms_Admin_Field
	 */
	protected static $placeholder;

	/**
	 * Holds a generic object for default field
	 *
	 * DO NOT Use directly, use getter method
	 *
	 * @since 1.5.1
	 *
	 * @var Caldera_Forms_Admin_Field
	 */
	protected static $default;

	/**
	 * Generate field HTML from array of field
	 *
	 * Attempts to generate HTML for each field from cache first, else generates using Caldera_Forms_Admin_Fields
	 *
	 * @since 1.5.1
	 *
	 * @param array $fields Contents must be Caldera_Forms_Admin_Field or the name of a field that has generic generator in the class
	 * @param string $field_type The CF field type (text, advanced_file, etc) used for cache.
	 *
	 * @return string
	 */
	public static function fields( array $fields, $field_type ){
		$html = self::get_cache( $field_type );
		if ( ! is_string( $html ) ) {
			$generator = self::get_generator();
			foreach ( $fields as $field ){
				if( is_a( $field, 'Caldera_Forms_Admin_Field' ) ){
					$generator->add_field( $field );
				}elseif ( is_string( $field ) ){
					if( method_exists( __CLASS__, $field ) ){
						$generator->add_field( self::$field() );
					}//this sucks, but default can't be the name of method in PHP5
					elseif ( method_exists( __CLASS__, $field . '_field'  ) ){
						$method = $field . '_field';
						$generator->add_field( self::$method() );
					}
				}

			}
			$html = $generator->html();
			self::set_cache( $html, $field_type );
		}

		return $html;
	}


	/**
	 * Generator for text fields
	 *
	 * @since 1.5.1
	 *
	 * @param string $field_name Field name
	 * @param string $label_text Label (legend) text
	 * @param string $description Optional. Field description.
	 *
	 * @return Caldera_Forms_Admin_Field
	 */
	public static function text_field( $field_name, $label_text,$description = '' ){
		$field = new Caldera_Forms_Admin_Field();
		$field->set_from_array(
			array(
				'type' => 'text',
				'name' => $field_name,
				'label' => $label_text,
				'args' => array(
					'description' => $description,
					'block' => true,
					'magic' => true,
				)
			)
		);

		return $field;
	}

	/**
	 * Generator for select fields
	 *
	 * @since 1.5.1
	 *
	 * @param string $field_name Field name
	 * @param string $label_text Label text
	 * @param array|string $options Array of options 'option' => 'label' or dynamic options string
	 * @param string $description Optional. Field description.
	 *
	 * @return Caldera_Forms_Admin_Field
	 */
	public static function select_field( $field_name, $label_text, $options, $description = '' ){
		$field = new Caldera_Forms_Admin_Field();
		$field->set_from_array(
			array(
				'type' => 'select',
				'name' => $field_name,
				'label' => $label_text,
				'options' => $options,
				'args' => array(
					'description' => $description,
					'block' => false,
					'magic' => false,
				)
			)
		);

		return $field;
	}

	/**
	 * Generator for checkbox fields
	 *
	 * @since 1.5.1
	 *
	 * @param string $field_name Field name
	 * @param string $label_text Label (legend) text
	 * @param array $options Array of options 'option' => 'label'
	 * @param string $description Optional. Field description.
	 *
	 * @return Caldera_Forms_Admin_Field
	 */
	public static function checkbox_field( $field_name, $label_text, $options, $description = '' ){
		$field = new Caldera_Forms_Admin_Field();
		$field->set_from_array(
			array(
				'type' => 'checkbox',
				'name' => $field_name,
				'label' => $label_text,
				'options' => $options,
				'args' => array(
					'description' => $description,
					'block' => false,
					'magic' => false,
				)
			)
		);

		return $field;
	}

	/**
	 * A generic Caldera_Forms_Admin_Field object for placeholder settings fields
	 *
	 * @since 1.5.1
	 *
	 * @return Caldera_Forms_Admin_Field
	 */
	protected static function placeholder( ){
		if( is_null( self::$placeholder ) ){
			self::$placeholder = new Caldera_Forms_Admin_Field();
			self::$placeholder->set_from_array( array(
				'type' => 'text',
				'name' => 'placeholder',
				'label' => __( 'Placeholder', 'caldera-forms' ),
				'args' => array(
					'description' => '',
					'block' => true,
					'magic' => true,
				)
			));
		}

		return self::$placeholder;
	}

	/**
	 * A generic Caldera_Forms_Admin_Field object for default settings fields
	 *
	 * @since 1.5.1
	 *
	 * @return Caldera_Forms_Admin_Field
	 */
	protected static function default_field(){
		if( is_null( self::$default ) ){
			self::$default = new Caldera_Forms_Admin_Field();
			self::$default->set_from_array(array(
				'type' => 'text',
				'name' => 'default',
				'label' => __( 'Default', 'caldera-forms' ),
				'args' => array(
					'description' => '',
					'block' => true,
					'magic' => true,
				)
			));
		}

		return self::$default;
	}

	/**
	 * Get fresh Caldera_Forms_Admin_Fields instance
	 *
	 * Makes a new one first time, other times clears properties of existing instance
	 *
	 * @since 1.5.1
	 *
	 * @return Caldera_Forms_Admin_Fields
	 */
	protected static function get_generator(){
		if( null === self::$generator ){
			self::$generator = new Caldera_Forms_Admin_Fields();
		}
		self::$generator->reset();

		return self::$generator;
	}

	/**
	 * Clear cache
	 *
	 * @since 1.5.1
	 *
	 */
	public static function clear_cache(){
		wp_cache_incr( self::get_prefix() );
		wp_cache_set( __CLASS__ . 'ns', __CLASS__ . 'ns_prefix' . rand() );
	}

	/**
	 * Get cached field HTML
	 *
	 * @since 1.5.1
	 *
	 * @param string $field_type Field type
	 *
	 * @return bool|string
	 */
	protected static function get_cache( $field_type ){
		if( WP_DEBUG ){
			return false;

		}

		$cached = wp_cache_get( self::identifier( $field_type ), __CLASS__ );
		if( is_string( $cached ) && ! empty( $cached ) ){
			return $cached;

		}

		return false;

	}

	/**
	 * Cache field HTML
	 *
	 * @since 1.5.1
	 *
	 * @param string $html Field HTML
	 * @param string $field_type Field type identifier
	 */
	protected static function set_cache( $html, $field_type ){
		wp_cache_set( self::identifier( $field_type ), $html, __CLASS__, HOUR_IN_SECONDS );
	}


	/**
	 * Get identifier for cache
	 *
	 * @since 1.5.1
	 *
	 * @param string $field_type
	 *
	 * @return string
	 */
	protected static function identifier( $field_type ){
		return self::get_prefix() . md5( __CLASS__ . $field_type );
	}


	/**
	 * Get cache prefix
	 *
	 * Needs to be set separate form identifier so we can increment it.
	 *
	 * @since 1.5.1
	 *
	 * @return string
	 */
	protected static function get_prefix(){
		$prefix = wp_cache_get( __CLASS__ . 'ns' );
		if ( empty( $prefix ) ) {
			$prefix = __CLASS__ . 'ns_prefix';
			wp_cache_set( __CLASS__ . 'ns', $prefix );

		}

		return $prefix;


	}

}