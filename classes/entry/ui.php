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

	/**
	 * Get the entry editor (v1) buttons
	 *
	 * @since 1.5.0
	 *
	 * @uses "caldera_forms_entry_actions"
	 */
	public static function get_entry_actions(){
		/**
		 * Change allowed entry viewer (v1) buttons
		 *
		 * @since unknown
		 *
		 * @params array $buttons
		 */
		$viewer_buttons_array = apply_filters( 'caldera_forms_entry_viewer_buttons', array());
		$viewer_buttons = null;
		if(!empty($viewer_buttons_array)){
			$viewer_buttons = array();
			foreach($viewer_buttons_array as $button){

				if(is_array($button['config'])){
					$config = $button['label'].'|'.json_encode($button['config']);
				}else{
					$config = $button['label'].'|'.$button['config'];
				}
				if( isset( $button['class'] ) ){
					$config .= '|' . $button['class'];
				}
				$viewer_buttons[] = $config;
			}

			$viewer_buttons = 'data-modal-buttons=\'' . implode(';', $viewer_buttons) . '\'';
		}

		/**
		 * Change allowed entry editor (v1) buttons
		 *
		 * @since unknown
		 *
		 * @params array $buttons
		 */
		$editor_buttons_array = apply_filters( 'caldera_forms_entry_editor_buttons', array());
		$editor_buttons = null;
		if(!empty($editor_buttons_array)){
			$editor_buttons = array();
			foreach($editor_buttons_array as $button){

				if(is_array($button['config'])){
					$config = $button['label'].'|'.json_encode($button['config']);
				}else{
					$config = $button['label'].'|'.$button['config'];
				}
				if( isset( $button['class'] ) ){
					$config .= '|' . $button['class'];
				}

				$editor_buttons[] = $config;
			}

			$editor_buttons = 'data-modal-buttons=\'' . implode(';', $editor_buttons) . '\'';
		}

		if( empty( get_option('permalink_structure') ) ){
			$separator = "&";
		} else {
			$separator = "?";
		}

		if( current_user_can( 'edit_others_posts' ) ){
			echo '{{#if ../../is_active}}<button class="hidden button button-small cfajax-trigger edit-entry-btn" id="edit-entry-{{_entry_id}}" data-active-class="current-edit" data-static="true" data-load-class="spinner" ' . $editor_buttons . ' data-modal-element="div" data-group="editentry" data-entry="{{_entry_id}}" data-form="{{../../form}}" data-request="' . esc_url( Caldera_Forms::get_submit_url() ) . '{{../../form}}/{{_entry_id}}/' . $separator . 'cf-api={{../../form}}" data-method="get" data-modal="view_entry" data-modal-width="700" data-modal-height="auto" data-modal-title="' . esc_attr__( 'Editing Entry ', 'caldera-forms' ) . ' #{{_entry_id}}" type="button" >' . esc_html__( 'Edit', 'caldera-forms' ) . '</button> {{/if}}';
		}
		
		echo '{{#if ../../is_active}}<button class="button button-small ajax-trigger view-entry-btn" id="view-entry-{{_entry_id}}" data-active-class="current-view"  data-static="true" data-load-class="spinner" ' . $viewer_buttons . ' data-group="viewentry" data-entry="{{_entry_id}}" data-form="{{../../form}}" data-action="get_entry" data-modal="view_entry" data-modal-width="700" data-modal-height="700" data-modal-title="' . esc_attr__('Entry', 'caldera-forms' ) . ' #{{_entry_id}}" data-template="#view-entry-tmpl" type="button" data-nonce="' .  wp_create_nonce( 'cf_view_entry'  ) . '">' . esc_html__( 'View', 'caldera-forms' ) . '</button> {{/if}}';
		if( current_user_can( 'delete_others_posts' ) ){
			echo '<button type="button" class="button button-small ajax-trigger" data-load-class="active" data-panel="{{#if ../../is_trash}}trash{{/if}}{{#if ../../is_active}}active{{/if}}" data-do="{{#if ../../is_trash}}active{{/if}}{{#if ../../is_active}}trash{{/if}}" data-callback="cf_refresh_view" data-form="{{../../form}}" data-active-class="disabled" data-group="row{{_entry_id}}" data-load-element="#entry_row_{{_entry_id}}" data-action="cf_bulk_action" data-items="{{_entry_id}}">{{#if ../../is_trash}}' . __('Restore', 'caldera-forms' ) . '{{/if}}{{#if ../../is_active}}' . esc_html_x( 'Trash', 'Verb: Action of moving to trash', 'caldera-forms' ) . '{{/if}}</button>';
		}

		if ( current_user_can( Caldera_Forms::get_manage_cap( 'resend-email' ) ) ) {
			echo '<a href="' . esc_url( add_query_arg( '_cf_resend', Caldera_Forms_Admin_Resend::resend_nonce(), admin_url() ) ) . '&e={{_entry_id}}&f={{../../form}}" class=" button button-small  edit-entry-btn" data-active-class="current-edit" title="' . esc_attr( __( 'Click to resend email from this message', 'caldera-forms' ) ) . ' ">' . esc_html__( 'Resend', 'caldera-forms' ) . '</a>';
		}

	}

	/**
	 * Filter permissions for entry view or export
	 *
	 * @since 1.5.0
	 *
	 * @uses "caldera_forms_manage_cap"
	 *
	 * @param string $cap A capability. By default "manage_options".
	 * @param string $context Context to check in.
	 * @param array|null $form Form config if it was passed.
	 *
	 * @return int|string
	 */
	public static function permissions_filter( $cap, $context, $form ){
		if( ! is_array( $form ) ){
			return $cap;
		}

		switch( $context ) {
			case 'delete-entry':
					$cap = 'delete_others_posts';
				break;
			case 'edit-entry' :
					$cap = 'edit_others_posts';
				break;
			case 'export' :
			case 'entry-view' :
				if( ! empty( $form[ 'pinned' ] ) ){
					if( isset( $form[ 'pin_roles' ][ 'access_role' ] ) && is_array($form[ 'pin_roles' ][ 'access_role' ] ) ){
						$user = wp_get_current_user();
						foreach( $form[ 'pin_roles' ][ 'access_role' ] as $role => $i ) {
							if( in_array( $role, $user->roles ) ){
								return $role;
							}
						}
					}
				}

				break;

		}

		return $cap;
	}

	public static function is_public( array $form ){
		return apply_filters( 'caldera_forms_entry_viewer_public', false, $form );

	}





}
