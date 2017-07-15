<?php
/**
 * Autoloader for Caldera Forms and Caldera Forms add-ons
 *
 * IMPORTANT: This autoloader has some stupid quirks that we have to live with until we're ready to break backwards compat. <em>Most importantly</em> sub-directories/ sub-prefixes have to be registered separately and you have to be careful about order of registering a prefix. For example, if "Caldera_Forms" had been registered before "Caldera_Forms_DB" it wouldn't have worked, need to make sure that the longest prefix goes in first.
 *
 * PREFIXES MUST START WITH "CF_" or "Caldera_Forms"
 *
 * @package   Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_Autoloader {

	/**
	 * Prefixes for our psuedo-namespaces
	 *
	 * 'prefix' => 'path'
	 *
	 * @since 1.3.5.3
	 *
	 * @var array
	 */
	protected static $roots = array();

	/**
	 * Add a route path and prefix
	 *
	 * @since 1.3.5.3
	 *
	 * @param string $prefix Class prefix -- Must start with "Caldera_Forms" or "CF_" use "CF_" for add-ons
	 * @param string $dir Full path to directory
	 */
	public static function add_root( $prefix, $dir ){
		self::$roots[ $prefix ] = $dir;
	}


	/**
	 * Handles autoloading of Caldera Forms and Caldera Forms add-on classes.
	 *
	 * @since 1.3.5.3
	 *
	 * @param string $class
	 */
	public static function autoload( $class ) {
		if ( 0 === strpos( $class, 'Caldera_Forms' ) || 0 === strpos( $class, 'CF_' )  ) {


			$root = self::find_root( $class );
			if ( ! $root ) {
				return;
			}

			$dir = self::get_dir( $root );

			if ( 'Caldera_Forms' == $class ) {
				$file = $dir . 'core.php';
			} elseif ( 'Caldera_Forms_Fields' === $class ) {
				$file = CFCORE_PATH . 'classes/fields.php';
			} elseif ( 'Caldera_Forms_Magic' === $class ) {
				$file = CFCORE_PATH . 'classes/magic.php';
			}elseif ( 'Caldera_Form_Grid' == $class ) {
				$file = $dir . 'caldera-grid.php';
			} elseif( 'Caldera_Forms_Entry' == $class ) {
				$file = CFCORE_PATH . 'classes/entry.php';
			} elseif ( 'Caldera_Forms_Save_Final' == $class ){
				$file = CFCORE_PATH . 'classes/save.php';
			} elseif( 'Caldera_Forms_Admin' == $class ){
				$file = CFCORE_PATH . 'classes/admin.php';
			} elseif( 'Caldera_Forms_CDN' == $class ){
				$file = CFCORE_PATH . 'classes/cdn.php';
			} elseif( 'Caldera_Forms_Settings' == $class ){
				$file = CFCORE_PATH . 'classes/settings.php';
			}else {
				$file = $dir . self::get_base( $class, $root );
			}

			if ( is_file( $file ) ) {
				require_once $file;
			}else{
				/**
				 * Runs when the autoloader fails to load a file
				 *
				 * @since 1.5.1
				 *
				 * @param string $class Name of class that that was attempted to load
				 * @param string $file File that that was attempted to require_once
				 */
				do_action( 'caldera_forms_autoloader_fail', $class );
			}
			
		}

	}

	/**
	 * Get the root prefix for a class
	 *
	 * @since 1.3.5.3
	 *
	 * @param string $class Class name
	 *
	 * @return string|void
	 */
	protected static function find_root( $class ){
		foreach( self::$roots as $root => $dir ){
			if( 0 === strpos( $class, $root ) ){
				return $root;
			}
		}


	}

	/**
	 * Get the directory for a prefix
	 *
	 * @since 1.3.5.3
	 *
	 * @param string $root Prefix root
	 *
	 * @return string|void
	 */
	protected static function get_dir( $root ){
		if( 'Caldera_Forms_Fields')
		if( array_key_exists( $root, self::$roots ) ){
			return trailingslashit( self::$roots[ $root ] );
		}
	}

	/**
	 * Get file name for class
	 *
	 * @param string $class Class name
	 * @param string $root Prefix root
 	 *
	 * @return string
	 */
	protected static function get_base( $class, $root ){
		return strtolower( str_replace( $root . '_', '', $class ) ) . '.php';
	}

	/**
	 * Registers Caldera_Forms_Autoloader as an SPL autoloader.
	 *
	 * @since 1.3.5.3

	 */
	public static function register( ) {
		if ( version_compare( phpversion(), '5.3.0', '>=' ) ) {
			spl_autoload_register( array( new self(), 'autoload' ), true, false );
		} else {
			spl_autoload_register( array( new self(), 'autoload' ) );
		}

	}

}
