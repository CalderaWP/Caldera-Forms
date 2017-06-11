<?php
/**
 * Support page for admin
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */

class Caldera_Forms_Support {

	/**
	 * Plugin slug for menu page
	 *
	 * @since 1.3.5
	 *
	 * @var      string
	 */
	protected $plugin_slug;

	/**
	 * Class instance
	 *
	 * @since 1.3.5
	 *
	 * @var Caldera_Forms_Support
	 */
	private static  $instance;

	/**
	 * Add hooks
	 *
	 * @since 1.3.5
	 */
	protected function __construct(){
		$this->plugin_slug = Caldera_Forms::PLUGIN_SLUG;
		add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
	}

	/**
	 * Get class instance
	 *
	 * @since 1.3.5
	 *
	 * @return \Caldera_Forms_Support
	 */
	public static function get_instance(){
		if( null == self::$instance ){
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Add support sub menu page
	 *
	 * @since 1.3.5
	 *
	 * @uses "admin_menu" hook
	 */
	public static function add_menu_page(){
		add_submenu_page(
			'caldera-forms',
			__( 'Support', 'caldera-forms' ),
			'<span class="caldera-forms-menu-dashicon"><span class="dashicons dashicons-sos"></span></span>' . __( 'Support', 'caldera-forms' ) . '</span>',
			Caldera_Forms::get_manage_cap( 'admin' ),
			'caldera-form-support',
			array( __CLASS__, 'page' )

		);
	}

	/**
	 * Return an array of plugin names and versions
	 *
	 * @since 1.3.5
	 *
	 * @return array
	 */
	public static function get_plugins() {
		$plugins     = array();
		include_once ABSPATH  . '/wp-admin/includes/plugin.php';
		$all_plugins = get_plugins();
		foreach ( $all_plugins as $plugin_file => $plugin_data ) {
			if ( is_plugin_active( $plugin_file ) ) {
				$plugins[ $plugin_data[ 'Name' ] ] = $plugin_data[ 'Version' ];
			}
		}

		return $plugins;
	}

	/**
	 * Add scripts for this page
	 *
	 * @since 1.3.5
	 *
	 * @uses "admin_enqueue_scripts" hook
	 *
	 */
	public function scripts(  ){
		if( Caldera_Forms_Admin::is_page( 'caldera-form-support' ) ){
			Caldera_Forms_Admin_Assets::enqueue_style( 'admin' );
			Caldera_Forms_Admin_Assets::enqueue_script( 'support-page' );
		}
	}

	/**
	 * Callback for support menu page render
	 *
	 * @since 1.3.5
	 */
	public static function page(){
		include CFCORE_PATH . 'ui/support/page.php';
	}

	/**
	 * Debug Information
	 *
	 * @since 1.3.5
	 *
	 * @param bool $html Optional. Return as HTML or not
	 *
	 * @return string
	 */
	public static function debug_info( $html = true ) {
		global $wp_version, $wpdb;
		$wp          = $wp_version;
		$php         = phpversion();
		$mysql       = $wpdb->db_version();
		$plugins = self::get_plugins();
		$stylesheet    = get_stylesheet();
		$theme         = wp_get_theme( $stylesheet );
		$theme_name    = $theme->get( 'Name' );
		$theme_version = $theme->get( 'Version' );
		$opcode_cache  = array(
			'Apc'       => function_exists( 'apc_cache_info' ) ? 'Yes' : 'No',
			'Memcached' => class_exists( 'eaccelerator_put' ) ? 'Yes' : 'No',
			'Redis'     => class_exists( 'xcache_set' ) ? 'Yes' : 'No',
		);
		$object_cache  = array(
			'Apc'       => function_exists( 'apc_cache_info' ) ? 'Yes' : 'No',
			'Apcu'      => function_exists( 'apcu_cache_info' ) ? 'Yes' : 'No',
			'Memcache'  => class_exists( 'Memcache' ) ? 'Yes' : 'No',
			'Memcached' => class_exists( 'Memcached' ) ? 'Yes' : 'No',
			'Redis'     => class_exists( 'Redis' ) ? 'Yes' : 'No',
		);
		$versions      = array(
			'WordPress Version'           => $wp,
			'PHP Version'                 => $php,
			'MySQL Version'               => $mysql,
			'Server Software'             => $_SERVER[ 'SERVER_SOFTWARE' ],
			'Your User Agent'             => $_SERVER[ 'HTTP_USER_AGENT' ],
			'Session Save Path'           => session_save_path(),
			'Session Save Path Exists'    => ( file_exists( session_save_path() ) ? 'Yes' : 'No' ),
			'Session Save Path Writeable' => ( is_writable( session_save_path() ) ? 'Yes' : 'No' ),
			'Session Max Lifetime'        => ini_get( 'session.gc_maxlifetime' ),
			'Opcode Cache'                => $opcode_cache,
			'Object Cache'                => $object_cache,
			'WPDB Prefix'                 => $wpdb->prefix,
			'WP Multisite Mode'           => ( is_multisite() ? 'Yes' : 'No' ),
			'WP Memory Limit'             => WP_MEMORY_LIMIT,
			'Currently Active Theme'      => $theme_name . ': ' . $theme_version,
			'Currently Active Plugins'    => $plugins
		);
		if ( $html ) {
			$debug = '';
			foreach ( $versions as $what => $version ) {
				$debug .= '<p><strong>' . $what . '</strong>: ';
				if ( is_array( $version ) ) {
					$debug .= '</p><ul class="ul-disc">';
					foreach ( $version as $what_v => $v ) {
						$debug .= '<li><strong>' . $what_v . '</strong>: ' . $v . '</li>';
					}
					$debug .= '</ul>';
				} else {
					$debug .= $version . '</p>';
				}
			}

			return $debug;
		} else {
			return $versions;
		}
	}

	public static function short_debug_info( $html = true ){
		global $wp_version, $wpdb;

		$data = array(
			'WordPress Version'     => $wp_version,
			'PHP Version'           => phpversion(),
			'MySQL Version'         => $wpdb->db_version(),
			'Caldera Forms Version' => CFCORE_VER,
			'WP_DEBUG'              => WP_DEBUG
		);
		if( $html ){
			$html = '';
			foreach ( $data as $what_v => $v ) {
				$html .= '<li style="display: inline;"><strong>' . $what_v . '</strong>: ' . $v . '</li>';
			}

			return '<ul>' . $html . '</ul>';
		}
	}

}
