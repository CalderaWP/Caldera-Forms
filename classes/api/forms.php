<?php

/**
 * CRUD via REST API for forms
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_API_Forms extends  Caldera_Forms_API_CRUD {


    /**
     * Get a form via REST API
     *
     * /cf-api/v2/forms/<form_id>
     *
     * @since 1.5.0
     *
     * @param WP_REST_Request $request
     *
     * @return Caldera_Forms_API_Error|Caldera_Forms_API_Response
     */
    public function get_item(WP_REST_Request $request){
	    try{
		    $this->form_object_factory( $request[ 'form_id' ], $request );
	    }catch ( Exception $e ){
		    return Caldera_Forms_API_Response_Factory::error_form_not_found();
	    }

        $response_form = $this->prepare_form_for_response( $this->form, $request );
        return new Caldera_Forms_API_Response( $response_form, 200, array( ) );

    }

    /**
     * Get all form via REST API
     *
     * /cf-api/v2/forms/
     *
     * @since 1.5.0
     *
     * @param WP_REST_Request $request
     *
     * @return Caldera_Forms_API_Error|Caldera_Forms_API_Response
     */
    public function get_items(WP_REST_Request $request){

        $forms = Caldera_Forms_Forms::get_forms( $request[ 'details' ] );
	    $prepared = array();
        if( ! empty( $forms ) && $request[ 'full' ] ){
            foreach( $forms as $id => $form ){
	            try{
		            $form = $this->form_object_factory( $id, $request );
	            }catch ( Exception $e ){
		           continue;
	            }
                $prepared[ $id ] = $this->prepare_form_for_response( $form, $request );
            }

        }

        $response = new Caldera_Forms_API_Response( $prepared, 200, array( ) );
        $response->set_total_header( count( $forms ) );
        return $response;
    }

    /**
     * @since 1.5.0
     *
     * @inheritdoc
     */
    public function route_base(){
        return 'forms';
    }

    /**
     * @since 1.5.0
     *
     * @inheritdoc
     */
    public function request_args(){
        return array(
            'details' => array(
                'required' => false,
                'default' => true,
            ),
            'full' => array(
                'required' => false,
                'default' => false,
            )
        );
    }


    /**
     * Permissions for form read
     *
     * @since 1.5.0
     *
     * @param WP_REST_Request $request
     *
     * @return bool
     */
    public function get_items_permissions_check( WP_REST_Request $request ){
        $allowed = current_user_can( Caldera_Forms::get_manage_cap( 'entry-view' ), $request[ 'form_id' ] );
	    if( ! $allowed ){
		    $allowed = Caldera_Forms_API_Util::check_api_token( $request );
	    }
        /**
         * Filter permissions for viewing form config via Caldera Forms REST API
         *
         * @since 1.5.0
         *
         * @param bool $allowed Is request authorized?
         * @param string $form_id The form ID
         * @param WP_REST_Request $request The current request
         */
        return apply_filters( 'caldera_forms_api_allow_form_view', $allowed, $request[ 'form_id' ], $request );

    }

    /**
     * Permissions for form create/update/delete
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
         * Filter permissions for creating, updating or deleting forms via Caldera Forms REST API
         *
         * @since 1.5.0
         *
         * @param bool $allowed Is request authorized?
         * @param string $form_id The form ID
         * @param WP_REST_Request $request The current request
         */
        return apply_filters( 'caldera_forms_api_allow_form_edit', $allowed, $request[ 'form_id' ], $request );

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
        return '/' . $this->route_base() . '/(?P<form_id>[\w-]+)';
    }

	/**
	 * Format repsonse for form
	 *
	 * @param Caldera_Forms_API_Form $form
	 * @param WP_REST_Request $request
	 *
	 * @return array|mixed
	 */
    protected function prepare_form_for_response( Caldera_Forms_API_Form $form, WP_REST_Request $request ){

    	$form = $this->prepare_field_details( $form, $request );

        $form = $this->prepare_processors_for_response( $form );

        $form = $this->prepare_mailer_for_response( $form );

        return $form;

    }

	/**
	 * Prepare field details section of form response
	 *
	 * @since 1.5.0
	 *
	 * @param Caldera_Forms_API_Form $form Form config
	 *
	 * @return array
	 */
    protected function prepare_field_details( Caldera_Forms_API_Form $form, WP_REST_Request $request ){
	    $order = $form->get_fields();
	    $entry_list = $form->get_entry_list_fields();

	    $form = $form->toArray();

	    $form[ 'field_details' ] = array(
	    	'order'      => array(),
		    'entry_list' => array()
	    );

	    array_walk( $order, array( $this, 'prepare_field' ) );
	    array_walk( $entry_list, array( $this, 'prepare_field' ) );

	    if( false == $request[ 'entry_list_only_fields' ] ){
		    foreach ( $order as $field_id => $field ){
			    $type = Caldera_Forms_Field_Util::get_type( Caldera_Forms_Field_Util::get_field( $field_id, $form ) );
			    if ( Caldera_Forms_Fields::not_support( $type, 'entry_list' ) ){
				    unset( $order[ $field_id ] );
			    }
		    }

		    foreach ( $entry_list as $field_id => $field ){
			    $type = Caldera_Forms_Field_Util::get_type( Caldera_Forms_Field_Util::get_field( $field_id, $form ) );
			    if ( Caldera_Forms_Fields::not_support( $type, 'entry_list' ) ){
				    unset( $entry_list[ $field_id ] );
			    }
		    }

	    }

	    $form[ 'field_details' ][ 'order' ] = $order;
	    $entry_list_defaults = array(
	    	'id' => array(
	    		'id' => 'id',
			    'label' => __( 'ID', 'caldera-forms' )
		    ),
		    'datestamp' => array(
			    'id' => 'datestamp',
			    'label' => __( 'Submitted', 'caldera-forms' )
		    ),
	    );

	    if( is_array( $entry_list ) && ! empty( $entry_list ) ){
		    $form[ 'field_details' ][ 'entry_list' ] = array_merge( $entry_list_defaults, $entry_list );
	    }else{
		    $form[ 'field_details' ][ 'entry_list' ]  = $entry_list_defaults;
	    }

	    return $form;

    }

	/**
	 * Reduce field to id/label
	 *
	 * Designed to be callback for array_walk used in $this->prepare_field_details
	 *
	 * @since 1.5.0
	 *
	 * @param $field
	 */
    protected function prepare_field(  &$field ){
		$field = array(
			'id' => $field[ 'ID' ],
			'label' => $field[ 'label' ]
		);
    }

    /**
     * @param $form
     * @return mixed
     */
    protected function prepare_processors_for_response($form) {
        if (!empty($form['processors'])) {
            $processors = array();
            foreach ($form['processors'] as $id => $processor) {
                $processors[$id] = array(
                    'type' => $processor['type'],
                    'id' => $id
                );
            }
            $form['processors'] = $processors;
            return $form;

        }
        return $form;
    }

    /**
     * @param $form
     * @return mixed
     */
    protected function prepare_mailer_for_response($form) {
        if (!empty($form['mailer'])) {
            if (!empty($form['mailer']['on_insert'])) {

                $form['mailer'] = array(
                    'active' => true
                );
                return $form;
            } else {
                $form['mailer'] = array(
                    'active' => false
                );
                return $form;
            }
        } else {

            $form['mailer'] = array(
                'active' => false
            );
            return $form;
        }
    }

}