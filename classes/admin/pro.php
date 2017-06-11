<?php

/**
 * Sets up Caldera Forms Pro menu page when Caldera Forms Pro API client is not installed
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2017 CalderaWP LLC
 */
class Caldera_Forms_Admin_Pro {

	/**
	 * Menu slug for page and slug for plugin in instller
	 *
	 * @since 1.5.1
	 *
	 * @var string
	 */
	protected $slug = 'cf-pro';

	/**
	 * Holds basefile, if found for CF Pro API client plugin
	 *
	 * @since 1.5.1
	 *
	 * @var string
	 */
	protected  $basefile;

	/**
	 * Add hooks
	 *
	 * @since 1.5.1
	 */
	public function add_hooks(){
		add_filter( 'plugins_api', array( $this, 'client_installer' ), 11, 3 );
		add_action( 'admin_menu', array( $this, 'maybe_add_menu_page' ) );
	}

	/**
	 * Add the CF Pro menu page if CF Pro client is not isntalled
	 *
	 * @uses "admin_menu" action
	 *
	 * @since 1.5.1
	 */
	public function maybe_add_menu_page(  ){
		if( defined( 'CF_PRO_VER' ) ){
			return;
		}

		add_submenu_page(
			Caldera_Forms::PLUGIN_SLUG,
			__( 'Caldera Forms Pro', 'caldera-forms'),
			'<span class="caldera-forms-menu-dashicon"><span class="dashicons dashicons-star-filled"></span>' .__( 'Caldera Forms Pro', 'caldera-forms') . '</span>',
			Caldera_Forms::get_manage_cap( 'admin' ),
			$this->slug,
			array( $this, 'render_page' )
		);

	}

	/**
	 * Render menu page
	 *
	 * @since 1.5.1
	 */
	public function render_page(){
		Caldera_Forms_Admin_Assets::enqueue_style( 'admin' );
		ob_start();
		$activate_link = $this->activate_link();
		$install_link = $this->install_link();
		$is_installed = $this->is_installed();
		include  CFCORE_PATH . '/ui/pro.php';
		echo ob_get_clean();

	}

	/**
	 * Get client plugin install link
	 *
	 * @since 1.5.1
	 *
	 * @return string
	 */
	public function install_link(){
		return wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=' . $this->slug ), 'install-plugin_' . $this->slug );
	}

	/**
	 * Installer for client plugin
	 *
	 * @uses "client_installer" filter
	 *
	 * @since 1.5.1
	 *
	 * @param $obj
	 * @param $action
	 * @param $args
	 *
	 * @return stdClass
	 */
	public function client_installer( $obj, $action, $args ){

		if( $action !== 'plugin_information' || $this->slug !== $args->slug  ){
			return $obj;

		}

		$plugin = new \stdClass();
		$plugin->name 			= 'Caldera Forms Pro';
		$plugin->slug 			= $this->slug;
		$plugin->version		= '1.0.0';
		$plugin->download_link	= 'https://github.com/CalderaWP/caldera-forms-pro/archive/master.zip';
		$plugin->plugin			= 'caldera-forms-pro-master/cf-pro.php';

		return $plugin;

	}

	/**
	 * Create the activation link for client plugin
	 *
	 * @since 1.5.1
	 *
	 * @return string
	 */
	public function activate_link(){
		return wp_nonce_url( self_admin_url( 'plugins.php?action=activate&plugin=' . urlencode( $this->get_basefile() ) ), 'activate-plugin_' . $this->get_basefile() );
	}

	/**
	 * Check if client plugin is installed
	 *
	 * @since 1.5.1
	 *
	 * @return bool
	 */
	public function is_installed(){
		$this->get_basefile();
		return is_string( $this->basefile );
	}

	/**
	 * Get the client plugin basefile if installed
	 *
	 * Acts as lazy-setter for basename property
	 *
	 * @since 1.5.1
	 *
	 * @return bool|string
	 */
	protected function get_basefile(){
		if( is_string( $this->basefile ) ){
			return $this->basefile;
		}

		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		$plugins = get_plugins();
		$this->basefile = false;

		foreach( $plugins as $plugin_file => $a_plugin ){
			if( $a_plugin['Name'] === 'Caldera Forms Pro Client' ){
				$this->basefile = $plugin_file;
				break;

			}
		}

		return $this->basefile;

	}


}