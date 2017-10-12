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
	 * Object cache (non-persistent) for sync objects
	 *
	 * Not using actual object cache, see: https://github.com/CalderaWP/Caldera-Forms/issues/1860
	 *
	 * @since 1.5.5
	 *
	 * @var array
	 */
	protected static $cache;


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

		return md5( $form_id . $field_id . $field_base_id . $current_form_count );
	}

	/**
	 * Clear cache
	 *
	 * @since 1.5.0.4
	 *
	 * @uses "caldera_forms_save_form" action
	 */
	public static function clear_cache(){
		self::$cache = array();
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
		if( isset( self::$cache[ $identifier ] ) &&  is_object( self::$cache[ $identifier ] ) ){
			return self::$cache[ $identifier ];
		}

		return false;

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
		self::$cache[ $identifier ] = $object;
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