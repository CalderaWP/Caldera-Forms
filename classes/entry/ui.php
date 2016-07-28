<?php

/**
 * AJAX callbacks for viewing entries
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_Entry_UI {

	/**
	 * Class instance
	 *
	 * @since 1.4.0
	 *
	 * @var \Caldera_Forms_Entry_UI
	 */
	protected static $instance;

	/**
	 * Get class instance
	 *
	 * @since 1.4.0
	 *
	 * @return \Caldera_Forms_Entry_UI
	 */
	public static function get_instance(){
		if( null == self::$instance ){
			self::$instance = new self();
		}

		return self::$instance;

	}

	/**
	 * Get a single entry viewer via AJAX
	 *
	 * @since 1.4.0
	 *
	 * @uses "wp_ajax_get_entry" action
	 */
	public function view_entry(){
		if( isset( $_POST, $_POST[ 'nonce' ], $_POST[ 'entry'], $_POST[ 'form' ] ) ){

			$form = Caldera_Forms_Forms::get_form( strip_tags( $_POST[ 'form' ] ) );
			if( ! current_user_can( Caldera_Forms::get_manage_cap( 'entry-view', $form )  ) || ! wp_verify_nonce( $_POST[ 'nonce' ], 'cf_view_entry' ) ){
				wp_send_json_error( $_POST );
			}

			$entry_id = absint( $_POST[ 'entry' ] );

			if( 0 < $entry_id && is_array( $form ) ){
				$entry = Caldera_Forms::get_entry( $entry_id, $form );
				if( is_wp_error( $entry ) ){
					wp_send_json_error( $entry );
				}else{
					status_header( 200 );
					wp_send_json( $entry );
				}
			}else{
				wp_send_json_error( $_POST );
			}



		}

		wp_send_json_error( $_POST );
	}

	/**
	 * Get entry viewer list via AJAX
	 *
	 * @since 1.4.0
	 *
	 * @uses "wp_ajax_browse_entries" action
	 */
	public function view_entries(){
		if( ! isset( $_POST[ 'page' ], $_POST[ 'form' ], $_POST[ 'nonce' ] ) ){
			wp_send_json_error( $_POST );
		}

		$form = Caldera_Forms_Forms::get_form( strip_tags( $_POST[ 'form' ] ) );

		if( ! current_user_can( Caldera_Forms::get_manage_cap( 'entry-view', $form ) ) || ! wp_verify_nonce( $_POST['nonce' ], 'view_entries' ) ){
			wp_send_json_error( $_POST );
		}


		if ( isset( $_POST[ 'page' ] ) && 0 < $_POST[ 'page' ] ) {
			$page = absint( $_POST[ 'page' ] );
		}else{
			$page = 1;
		}

		$entry_perpage = get_option( '_caldera_forms_entry_perpage', 20 );
		if ( isset( $_POST[ 'perpage' ] ) && 0 < $_POST[ 'perpage' ] ) {
			$perpage = absint( (int) $_POST[ 'perpage' ] );
			if( $entry_perpage != $perpage ){
				update_option( '_caldera_forms_entry_perpage', $perpage );
			}
		}else{
			$perpage = $entry_perpage;
		}

		if ( isset( $_POST[ 'status' ] ) ) {
			$status = strip_tags( $_POST[ 'status' ] );
		}else{
			$status = 'active';
		}

		$data = Caldera_Forms_Admin::get_entries( $form, $page, $perpage, $status );

		$data['is_' . $status ] = true;

		status_header( 200 );
		wp_send_json( $data );


	}


}
