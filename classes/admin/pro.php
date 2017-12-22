<?php

/**
 * Sets up Caldera Forms Pro menu page when to create cf-pro admin page WHEN CF PRO CAN NOT BE USED
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
		add_action( 'admin_menu', array( $this, 'maybe_add_menu_page' ) );
	}

	/**
	 * Add the CF Pro menu page if CF Pro client is not usable
	 *
	 * @uses "admin_menu" action
	 *
	 * @since 1.5.1
	 */
	public function maybe_add_menu_page(  ){

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

		include  CFCORE_PATH . '/ui/pro.php';

	}


}
