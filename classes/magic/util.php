<?php
/**
 * Helper functions for magic tag parsing
 *
 * @package   Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2017 CalderaWP LLC
 */
class Caldera_Forms_Magic_Util {

	/**
	 * Find field based on field magic tags
	 *
	 * @since 1.5.0
	 *
	 * @param string $field_magic
	 * @param array $form Optional. Form config.
	 *
	 * @return bool|mixed|void
	 */
	public static function find_field( $field_magic, $form = array() ) {
		if( empty( $form ) ){
			global  $form;
		}


		$part_tags = self::split_tags( $field_magic );
		if ( ! empty( $part_tags[1] ) ) {
			$tag = $part_tags[0];

		}else{
			$tag = $field_magic;

		}

		return Caldera_Forms_Field_Util::get_field_by_slug( $tag, $form );

	}

	/**
	 * Prepare a magic tag that uses brackets
	 *
	 * @since 1.5.0
	 *
	 * @param $value
	 *
	 * @return array
	 */
	public static function explode_bracket_magic( $value ){
		preg_match_all( "/\{(.+?)\}/", $value, $matches );
		return $matches;
	}

	/**
	 * Prepare a magic tag that uses % %
	 *
	 * @since 1.5.0
	 *
	 * @param $value
	 *
	 * @return array
	 */
	public static function explode_field_magic( $value ){
		$regex = "/%([a-zA-Z0-9_:]*)%/";

		preg_match_all( $regex, $value, $matches );
		return $matches;
	}

	/**
	 * Split tags by colon
	 *
	 * @since 1.5.0
	 *
	 * @param string $field_magic
	 *
	 * @return array
	 */
	public static function split_tags( $field_magic ){
		$part_tags = explode( ':', $field_magic );

		return $part_tags;
	}



}