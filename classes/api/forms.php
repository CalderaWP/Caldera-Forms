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
        $form_id = $request[ 'form_id' ];
        $form = Caldera_Forms_Forms::get_form( $form_id );
        if( ! is_array( $form ) ){
            return Caldera_Forms_API_Response_Factory::error_form_not_found();
        }


        $form = $this->prepare_form_for_response($form);
        return new Caldera_Forms_API_Response( $form, 200, array( ) );

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
        if( ! empty( $forms ) && $request[ 'full' ] ){
            $prepared = array();
            foreach( $forms as $id => $form ){
                $prepared[ $id ] = $this->prepare_form_for_response( Caldera_Forms_Forms::get_form( $id ) );
            }

        }

        $response = new Caldera_Forms_API_Response( $forms, 200, array( ) );
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
     * @param $form
     * @return mixed
     */
    protected function prepare_form_for_response( $form ){

    	$form = $this->prepare_field_details( $form );

        $form = $this->prepare_processors_for_response($form);

        $form = $this->prepare_mailer_for_response($form);

        return $form;

    }

    protected function prepare_field_details( $form ){
	    $form[ 'field_details' ] = array(
	    	'order'      => array(),
		    'entry_list' => array()
	    );
    	$in_order = Caldera_Forms_Forms::get_fields( $form, true );
	    $form[ 'field_details' ][ 'order' ] = wp_list_pluck( $in_order, 'ID' );
	    $form[ 'field_details' ][ 'entry_list' ] = Caldera_Forms_Forms::entry_list_fields( $form );

	    return $form;

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