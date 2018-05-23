<?php

/**
 * CRUD via REST API for entries
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_API_Entries extends Caldera_Forms_API_CRUD {

    /**
     * @inheritdoc
     *
     * @since 1.7.0
     */
    public function add_routes( $namespace ) {
        parent::add_routes($namespace);
        register_rest_route($namespace, $this->non_id_endpoint_url() . '/delete',
            array(
                'methods'             => array( \WP_REST_Server::READABLE ),
                'callback'            => array($this, 'delete_entries'),
                'permission_callback' => array($this, 'update_item_permissions_check')
            )
        );
    }

	/**
	 * Get an entry
	 *
	 * GET /cf-api/v2/entries/form-id/entry-id
	 *
	 * @since 1.5.0
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return Caldera_Forms_API_Error|Caldera_Forms_API_Response
	 */
	public function get_item( WP_REST_Request $request ) {
		try{
			$this->form_object_factory( $request[ 'form_id' ], $request );
		}catch ( Exception $e ){
			return Caldera_Forms_API_Response_Factory::error_form_not_found();
		}

		$entry = new Caldera_Forms_Entry( $this->form->toArray(), $request[ 'entry_id' ] );

		if( null == $entry->get_entry() ){
			return Caldera_Forms_API_Response_Factory::error_entry_not_found();
		}

		$data = $this->add_entry_to_response( $entry, array() );
		$data = $data[ $request[ 'entry_id' ] ];
		return Caldera_Forms_API_Response_Factory::entry_data( $data, 1, 1 );

	}

	/**
	 * Get entries
	 *
	 * GET /cf-api/v2/entries/form-id
	 *
	 * @since 1.5.0
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return Caldera_Forms_API_Error|Caldera_Forms_API_Response
	 */
	public function get_items( WP_REST_Request $request ) {
		try{
			$this->form_object_factory( $request[ 'form_id' ], $request );
		}catch ( Exception $e ){
			return Caldera_Forms_API_Response_Factory::error_form_not_found();
		}

		$per_page = $request[ 'per_page' ];
		if( 0 == $request[ 'per_page' ] ){
			$per_page = 1;
		}

		$entries = new Caldera_Forms_Entry_Entries( $this->form->toArray(), $per_page );
		$data = $this->prepare_entries_for_response( $entries->get_page( $request[ 'page' ], $request[ 'status' ] ) );
		$entries->get_page( $request[ 'page' ], $request[ 'status' ] );
		$pages = ceil( $entries->get_total( $request[ 'status' ] ) / $per_page );

		return Caldera_Forms_API_Response_Factory::entry_data(
			$data,
			count( $data ),
			$pages
		);
	}

	/**
	 * Delete an entry
	 *
	 * DELETE /cf-api/v2/entries/form-id/entry-id
	 *
	 * @since 1.5.0
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return Caldera_Forms_API_Error|Caldera_Forms_API_Response
	 */
	public function delete_item( WP_REST_Request $request ) {
		$form_id = $request[ 'form_id' ];
		$form = Caldera_Forms_Forms::get_form( $form_id );
		if( ! is_array( $form ) ){
			return Caldera_Forms_API_Response_Factory::error_form_not_found();
		}

		$result = Caldera_Forms_Entry_Bulk::delete_entries( array( $request[ 'entry_id' ] ) );
		if( $result ){
			return new Caldera_Forms_API_Response( __( 'Entry Deleted', 'caldera-forms' ), 200, array() );
		}else{
			return new Caldera_Forms_API_Error( 'not-deleted', __( 'Entry Not Deleted', 'caldera-forms' ) );
		}
	}

    /**
     * Delete all entries of a form based on Form ID
     *
     * GET /cf-api/v2/entries/form-id/delete
     *
     * @since 1.7.0
     *
     * @param WP_REST_Request $request
     *
     * @return Caldera_Forms_API_Error|Caldera_Forms_API_Response
     */
    public function delete_entries( WP_REST_Request $request ) {
        $formID = sanitize_text_field( $request[ 'form_id' ] );

        if( false === Caldera_Forms_Forms::is_internal_form( $formID ) ){
            $data = array(
                'deleted' => false,
                'message' =>  __( 'Form not found', 'caldera-forms')
            );
            return new Caldera_Forms_API_Response( $data, 404, array() );
        }

        $entries = \calderawp\CalderaFormsQueries\CalderaFormsQueries()->selectByFormId(  $formID, false );

        if( null != $entries ) {
            $entryIds = [];
            foreach( array_column( $entries, 'entry' ) as $entry ){
                $entryIds[] = $entry->id;
            }

            Caldera_Forms_Entry_Bulk::delete_entries( $entryIds );
            $data = array(
                'deleted' => true,
                'message' =>  __( 'Entries deleted', 'caldera-forms')
            );
            return new Caldera_Forms_API_Response( $data, 200, array() );

        } else {
            $data = array(
                'deleted' => false,
                'message' =>  __( 'No entries found', 'caldera-forms')
            );
            return new Caldera_Forms_API_Response( $data, 404, array() );
        }

    }


	/**
	 * Prepare entry data for a response
	 *
	 * @since 1.5.0
	 *
	 * @param array $entries Array of found Caldera_Forms_Entry objects
	 *
	 * @return array
	 */
	protected function prepare_entries_for_response( $entries ){
		$response_data = array();

		if ( ! empty( $entries ) ) {
			/** @var Caldera_Forms_Entry $entry Entry Object */
			foreach ($entries as $id => $entry) {
				$response_data = $this->add_entry_to_response($entry, $response_data);

			}
		}

		return $response_data;

	}

	/**
	 * Add an entry to a response collection
	 *
	 * @since 1.5.0
	 *
	 * @param Caldera_Forms_Entry $entry Entry object
	 * @param array $response_data Current response collection
	 *
	 * @return array
	 */
	protected function add_entry_to_response( Caldera_Forms_Entry $entry,  array $response_data ){
		$id = $entry->get_entry_id();
		$response_data[ $id ] = array();

		$response_data[ $id ] = $entry->get_entry()->to_array( false );
		$response_data[ $id ][ 'user' ] = array(
			'id' => '',
			'name' => '',
			'email' => ''
		);
		$user = get_user_by( 'ID', $entry->get_entry()->user_id );

		if( is_object( $user ) ){
			$response_data[ $id ][ 'user' ][ 'name' ] = $user->display_name;
			if( current_user_can( 'edit_users' ) ){
				$response_data[ $id ][ 'user' ][ 'email' ] = $user->user_email;
				$response_data[ $id ][ 'user' ][ 'id' ] = $entry->get_entry()->user_id;
			}
		}

		unset( $response_data[ $id ][ 'user_id' ] );

		$fields = $entry->get_fields();
        $response_data[ $id ][ 'fields' ] = array();
		if( ! empty( $fields ) ){
			/** @var Caldera_Forms_Entry_Field $field */
			foreach(  $fields as $field ){
                if ( $this->form->is_api_field( $field->field_id ) &&  is_object( $field ) ) {
	                $response_data[ $id ][ 'fields' ][ $field->field_id ] = $field->to_array( false );
                }
			}

		}

		$metas = $entry->get_meta();
        $response_data[ $id ][ 'meta' ] = array();
		if( ! empty( $metas ) ){
			/** @var Caldera_Forms_Entry_Meta $meta */
			foreach ( $metas as $meta ){
			    if( is_object( $meta ) ){
                    $response_data[ $id ][ 'meta' ][ $meta->id ] = $meta->to_array( false );
                }

			}

		}

		return $response_data;
	}

	/**
	 * Permissions for entry read
	 *
	 * @since 1.5.0
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return bool
	 */
	public function get_items_permissions_check( WP_REST_Request $request ){
		$form_id =  $request[ 'form_id' ];
		$allowed = current_user_can( Caldera_Forms::get_manage_cap( 'entry-view' ), $form_id );

		if( ! $allowed ){
			$allowed = Caldera_Forms_API_Util::check_api_token( $request );
		}
		
		/**
		 * Filter permissions for viewing entries via Caldera Forms REST API
		 *
		 * @since 1.5.0
		 *
		 * @param bool $allowed Is request authorized?
		 * @param string $form_id The form ID
		 * @param WP_REST_Request $request The current request
		 */
		return apply_filters( 'caldera_forms_api_allow_entry_view', $allowed, $form_id, $request );

	}

	/**
	 * Permissions for entry create/update/delete
	 *
	 * @since 1.5.0
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return bool
	 */
	public function create_item_permissions_check( WP_REST_Request $request ){
		$allowed = current_user_can( Caldera_Forms::get_manage_cap( 'entry-edit' ), $request[ 'form_id' ] );

		/**
		 * Filter permissions for creating, updating or deleting entries via Caldera Forms REST API
		 *
		 * @since 1.5.0
		 *
		 * @param bool $allowed Is request authorized?
		 * @param string $form_id The form ID
		 * @param WP_REST_Request $request The current request
		 */
		return apply_filters( 'caldera_forms_api_allow_entry_edit', $allowed, $request[ 'form_id' ], $request );

	}

	/**
	 * @inheritdoc
	 *
	 * @since 1.5.0
	 */
	public function get_items_args() {
		return wp_parse_args( array(
			'status' => array(
				'default' => 'active',
				'validate_callback' => array( $this, 'validate_status' )
			)
		), parent::get_items_args() );
	}

	/**
	 * Form the endpoint URL that deos not include item ID
	 *
	 * Used by for get_items() and create_items()
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	protected function non_id_endpoint_url() {
		return '/' . $this->route_base() . '/(?P<form_id>[\w-]+)';

	}

	/**
	 * @inheritdoc
	 *
	 * @since 1.5.0
	 */
	public function request_args() {
		return array(
		);
	}

	/**
	 * Form the endpoint URL that includes item ID
	 *
	 * Used by for get_item() and update_time() and delete_item()
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	public function id_endpoint_url() {
		return $this->non_id_endpoint_url() . '/' . '(?P<entry_id>[\d]+)';
	}

	/**
	 * @inheritdoc
	 *
	 * @since 1.5.0
	 */
	protected function route_base(){
		return 'entries';
	}

	/**
	 * Validate status argument
	 *
	 * @since 1.5.0
	 *
	 * @param $value
	 *
	 * @return bool
	 */
	public function validate_status( $value ){
		return in_array( $value, array(
			'active',
			'pending',
			'trash'
		));

	}

}
