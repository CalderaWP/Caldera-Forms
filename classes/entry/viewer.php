<?php

/**
 * Creates entry viewer
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_Entry_Viewer {

	/**
	 * Get full viewer system
	 *
	 * Designed for use in admin
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	public static function full_viewer( $with_toolbar = true ){
		ob_start();
		include CFCORE_PATH . 'ui/entries/viewer.php';
		return ob_get_clean();
	}

	/**
	 * Print necessary scripts or admin viewer
	 *
	 * @since 1.5.0
	 */
	public static function print_scripts(){
		include CFCORE_PATH . 'ui/entries/scripts_templates.php';
	}

	/**
	 * Create span that triggers AJAX action for loading a single form's entries into viewer
	 *
	 * @since 1.5.0
	 *
	 * @param string $form_id The from ID
	 *
	 * @return string
	 */
	public static function entry_trigger( $form_id ){
		$atts = array(
			'class'               => 'form-control form-entry-trigger ajax-trigger',
			'id'                  => esc_attr( 'entry-trigger-' .  trim( $form_id ) ),
			'data-autoload'       => 'true',
			'data-page'           => '1',
			'data-status'         => 'active',
			'data-callback'       => 'setup_pagination',
			'data-group'          => 'entry_nav',
			'data-active-class'   => 'highlight',
			'data-load-class'     => 'spinner',
			'data-active-element' => '#form_row_' . $form_id,
			'data-template'       => '#forms-list-alt-tmpl',
			'data-form'           => $form_id,
			'data-target'         => '#form-entries-viewer',
			'data-action'         => 'browse_entries',
			'data-nonce'          => wp_create_nonce( 'view_entries' ),
		);

		return sprintf( '<span %s ></span>', caldera_forms_implode_field_attributes( caldera_forms_escape_field_attributes_array( $atts ) ) );

	}

	/**
	 * Show entry viewer (v1) for one form
	 *
	 * @since 1.5.0
	 *
	 * @param $form_id
	 * @param bool $with_toolbar
	 *
	 * @return string
	 */
	public static function form_entry_viewer_1( $form_id, $with_toolbar = false ){
		Caldera_Forms_Admin_Assets::admin_common();


		$viewer = self::full_viewer( $with_toolbar );
		$viewer .= self::entry_trigger( $form_id );
		if( ! did_action( 'wp_footer' ) ){
			add_action( 'wp_footer', array( __CLASS__, 'print_scripts' ) );
		}else{
			ob_start();
			self::print_scripts();
			$viewer .= ob_get_clean();
		}

		return $viewer;

	}

	/**
	 * Get saved # of entries per page to show
	 *
	 * @since 1.5.0
	 *
	 * @return int
	 */
	public static function entries_per_page(){
		return absint( get_option( '_caldera_forms_entry_perpage', 20 ) );
	}

	/**
	 * Updated saved # of entries per page to show
	 *
	 * @since 1.5.0
	 *
	 * @param int $per_page New value
	 *
	 * @return int
	 */
	public static function update_entries_per_page( $per_page ){
		update_option( '_caldera_forms_entry_perpage', absint( $per_page ) );
		return self::entries_per_page();
	}

	/**
	 * Factory Caldera_Forms_Entry_Vue class that creates entry viewer v2
	 *
	 * Enqueues script and outputs HTML
	 *
	 * @since 1.5.0
	 *
	 * @param array $form Form config
	 *
	 * @return string Rendered HTML of entry viewer
	 */
	public static function form_entry_viewer_2( array  $form, $config = array() ){
		$vue = new Caldera_Forms_Entry_Vue( $form, $config );
		return  $vue->display();

	}

}