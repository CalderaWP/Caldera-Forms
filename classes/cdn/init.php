<?php


/**
 * Class init
 */
class Caldera_Forms_CDN_Init {

	/**
	 * In use CDN implementation
	 *
	 * @since 1.5.3
	 *
	 * @var Caldera_Forms_CDN_Jsdelivr|Caldera_Forms_CDN
	 */
	protected static $cdn;

	/**
	 * Implement core settings
	 *
	 * @since 1.5.3
	 */
	public static function init(){
		$cdn_enabled = Caldera_Forms::settings()->get_cdn()->enabled();
		if( $cdn_enabled ){
			self::$cdn = new Caldera_Forms_CDN_Jsdelivr( CFCORE_URL, CFCORE_VER );
			self::$cdn->add_hooks();
		}
	}

	/**
	 * Get CDN implementation
	 *
	 * @since 1.5.3
	 *
	 * @return Caldera_Forms_CDN_Jsdelivr|Caldera_Forms_CDN
	 */
	public static function get_cdn(){
		return self::$cdn;
	}
}