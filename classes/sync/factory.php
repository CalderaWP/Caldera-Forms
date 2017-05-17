<?php
/**
 * Factory for creating or retrieving from cache Caldera_Forms_Field_Sync objects
 *
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_Sync_Factory {


	/**
	 * Get a Caldera_Forms_Field_Sync by creating it or pulling from cache
	 *
	 * @since 1.5.0
	 *
	 * @param array $form Form config
	 * @param array $field Field config
	 * @param string $field_base_id Field ID attribute
	 * @param int|null $current_form_count Optional. Current form ID.  Global is used if not provided
	 *
	 * @return Caldera_Forms_Sync_Sync|Caldera_Forms_Sync_HTML|Caldera_Forms_Sync_Summary
	 */
	public static function get_object( $form, $field, $field_base_id, $current_form_count = null ){
		if( ! $current_form_count ){
			$current_form_count = Caldera_Forms_Render_Util::get_current_form_count();
		}

		$id = self::identifier( $form[ 'ID' ], $field[ 'ID' ], $field_base_id, $current_form_count );

		$object = self::get_cache( $id );
		if ( ! is_object( $object ) ) {
			$object = self::create( $form, $field, $field_base_id, $current_form_count );
			self::add_to_cache( $id, $object );
		}

		return $object;

	}

	/**
	 * Get identifier for cache
	 *
	 * @since 1.5.0
	 *
	 * @param string $form_id ID of form
	 * @param string $field_id If of field
	 * @param string $field_base_id Field ID attribute
	 * @param int|null $current_form_count Optional. Current form ID.  Global is used if not provided
	 *
	 * @return string
	 */
	public static function identifier( $form_id, $field_id, $field_base_id, $current_form_count ){
		if( ! $current_form_count ){
			$current_form_count = Caldera_Forms_Render_Util::get_current_form_count();
		}

		return self::get_prefix() . md5(  __CLASS__ . CFCORE_VER . $form_id . $field_id . $field_base_id, $current_form_count );
	}

	/**
	 * Clear cache
	 *
	 * @since 1.5.0.4
	 *
	 * @uses "caldera_forms_save_form" action
	 */
	public static function clear_cache(){
		wp_cache_incr( self::get_prefix() );
		wp_cache_set(  __CLASS__ . 'ns', __CLASS__ . 'ns_prefix' . rand() );
	}

	/**
	 * Get cache prefix
	 *
	 * Needs to be set seperate form identifier so we can increment it.
	 *
	 * @since 1.5.0.4
	 *
	 * @return string
	 */
	protected static  function get_prefix(){
		$prefix =  wp_cache_get( __CLASS__ . 'ns' );
		if( empty( $prefix ) ){
			$prefix = __CLASS__ . 'ns_prefix';
			wp_cache_set(  __CLASS__ . 'ns', $prefix );

		}

		return $prefix;

	}

	/**
	 * Get object from object cache
	 *
	 * @since 1.5.0
	 *
	 * @param string $identifier Unique identifier for this object
	 *
	 * @return bool|Caldera_Forms_Sync_Sync|Caldera_Forms_Sync_HTML
	 */
	protected static function get_cache( $identifier ){
		return wp_cache_get( $identifier, __CLASS__ );

	}

	/**
	 * Place object in cache
	 *
	 * @since 1.5.0
	 *
	 * @param string $identifier Unique identifier for this object
	 * @param Caldera_Forms_Sync_Sync|Caldera_Forms_Sync_HTML $object Object tocache
	 */
	protected static function add_to_cache( $identifier, Caldera_Forms_Sync_Sync $object ){
		wp_cache_set( $identifier, $object, __CLASS__ );
	}

	/**
	 * Create object
	 *
	 * This is the actual factory, but not exposed publicly, because Josh wanted to force cache/container usage
	 *
	 * @since 1.5.0
	 *
	 * @param array $form Form config
	 * @param array $field Field config
	 * @param string $field_base_id Field ID attribute
	 *
	 * @return Caldera_Forms_Sync_Sync|Caldera_Forms_Sync_HTML|Caldera_Forms_Sync_Calc|Caldera_Forms_Sync_Summary
	 */
	protected static function create( $form, $field, $field_base_id, $current_form_count ){
		$type = Caldera_Forms_Field_Util::get_type( $field );
		switch( $type ) {
			case 'html' :
				return new  Caldera_Forms_Sync_HTML( $form, $field, $field_base_id, $current_form_count );
			break;
			case 'calculation' :
				return new  Caldera_Forms_Sync_Calc( $form, $field, $field_base_id, $current_form_count );
			break;
			case 'summary' :
				return new Caldera_Forms_Sync_Summary( $form, $field, $field_base_id, $current_form_count);
			break;
			default :
				return new  Caldera_Forms_Sync_Sync( $form, $field, $field_base_id, $current_form_count );
			break;
		}

	}




}