<?php

/**
 * AJAX loaded for admin panels of form editor
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_Admin_AJAXPanels {

	/**
	 * AJAX callback for panel loading via AJAX
	 *
	 * @since 1.5.0
	 *
	 * @uses "wp_ajax_caldera_forms_ajax_canvas" action
	 */
	public static function callback(){
		if( isset( $_GET[ 'panel' ], $_GET[ 'form' ] ) && current_user_can( Caldera_Forms::get_manage_cap( 'admin' ) ) ){
			$panel = strip_tags( $_GET[ 'panel' ] );
			if( ! isset( $_GET[ 'nonce' ] ) || ! wp_verify_nonce( $_GET[ 'nonce' ], self::nonce_action( $panel ) ) ){
				status_header( 403 );
				wp_send_json_error( array( 'html' => __( 'Unauthorized', 'caldera-forms' ) ) );
				exit;
			}

			$form = strip_tags( $_GET[ 'form' ] );

			$panel = Caldera_Forms_Admin_Panel::get_panel( $panel );
			if( false == $panel ){
				wp_send_json_error( array( 'html' => ';(' ) );
				exit;
			}

			$html = Caldera_Forms_Admin_Panel::panel_html( $panel, Caldera_Forms_Forms::get_form( $form ) );
			wp_send_json_success( array( 'html' => $html ) );
			exit;
		}

	}

	/**
	 * Create the nonce for panel loading via AJAX
	 *
	 * @since 1.5.0
	 *
	 * @param string $panel Panel name
	 *
	 * @return string
	 */
	public static function nonce( $panel ){
		return wp_create_nonce( self::nonce_action( $panel ) );
	}

	/**
	 * Nonce action for the nonce for panel loading via AJAX
	 *
	 * @since 1.5.0
	 *
	 * @param string $panel Panel name
	 *
	 * @return string
	 */
	protected static function nonce_action( $panel ){
		return 'cf-admin-panel-' . $panel;
	}

}