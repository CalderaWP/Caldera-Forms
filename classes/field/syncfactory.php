<?php

/**
 * Factory for creating or retrieving from cache/container Caldera_Forms_Field_Sync objects
 *
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_Field_Syncfactory {


	/**
	 * Get a Caldera_Forms_Field_Sync by creating it, pulling it from container or cache
	 *
	 * @since 1.5.0
	 *
	 * @param array $form Form config
	 * @param array $field Field config
	 * @param string $field_base_id Field ID attribute
	 *
	 * @return Caldera_Forms_Field_Sync
	 */
	public static function get_object( $form, $field, $field_base_id ){

		$syncs = Caldera_Forms_Field_Syncs::get_instance();
		$id = self::identifier( $form[ 'ID' ], $field[ 'ID' ], $field_base_id );
		if ( $syncs->has( $id ) ){
			return $syncs->get( $id );
		}else{
			$object = self::get_cache( $id );
			if( $object ){
				$syncs->add( $id, $object );
			}else{
				$object = self::create( $form, $field, $field_base_id );
				$syncs->add( $id, $object );
			}

			return $object;


		}
	}

	/**
	 * Get identifier for cache/container
	 *
	 * @since 1.5.0
	 *
	 * @param string $form_id ID of form
	 * @param string $field_id If of field
	 * @param string $field_base_id Field ID attribute
	 *
	 * @return string
	 */
	public static function identifier( $form_id, $field_id, $field_base_id ){
		return md5(  __CLASS__ .  $form_id . $field_id . $field_base_id );
	}

	/**
	 * Get object from object cache
	 *
	 * @since 1.5.0
	 *
	 * @param string $identifier Unique identifier for this object
	 *
	 * @return bool|Caldera_Forms_Field_Sync|Caldera_Forms_Field_SyncHTML
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
	 * @param Caldera_Forms_Field_Sync|Caldera_Forms_Field_SyncHTML $object Object tocache
	 */
	protected static function add_to_cache( $identifier, Caldera_Forms_Field_Sync $object ){
		wp_cache_set( $identifier, $object, __CLASS__ );
	}

	/**
	 * Create object
	 *
	 * This is the actual factory, but not exposed publically, because Josh wanted to force cache/containter usage
	 *
	 * @since 1.5.0
	 *
	 * @param array $form Form config
	 * @param array $field Field config
	 * @param string $field_base_id Field ID attribute
	 *
	 * @return Caldera_Forms_Field_Sync|Caldera_Forms_Field_SyncHTML
	 */
	protected static function create( $form, $field, $field_base_id ){
		if( 'html' === Caldera_Forms_Field_Util::get_type( $field ) ){
			return new  Caldera_Forms_Field_SyncHTML( $form, $field, $field_base_id );

		}
		return new  Caldera_Forms_Field_Sync( $form, $field, $field_base_id );
	}


}