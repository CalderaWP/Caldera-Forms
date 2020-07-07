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
	 * @inheritdoc
	 *
	 * @since 1.5.3
	 */
	public function add_routes( $namespace ) {
	    $this->request_args();
		parent::add_routes($namespace);

		//Get the id routes
		$id_route = $this->id_route();
		//Replace edit route
		$id_route[ \WP_REST_Server::EDITABLE ] = [
            'methods'             => \WP_REST_Server::EDITABLE,
            'callback'            => [ $this, 'save_form' ],
            'permission_callback' => [ $this, 'save_form_permissions_check' ],
            //These args are different then $this->request_args()
            'args'                => [
                'cf_edit_nonce' => [
                    'type' => 'string',
                    'description' => __('Caldera Forms editor nonce', 'caldera-forms'),
                    'required' => 'true'
                ],
                'config' => [
                    'type' => 'object',
                    'description' => __('Caldera Forms editor nonce', 'caldera-forms'),
                    'required' => 'true'
                ],
                'form' => [
                    'type' => 'string',
                    'description' => __('ID of form', 'caldera-forms'),
                    'required' => 'true'
                ],
            ]
        ];

		//Re-register them
        register_rest_route($namespace, $this->id_endpoint_url(), array_values( $id_route ),true );


        register_rest_route( $namespace, $this->id_endpoint_url() . '/revisions',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_revisions' ),
				'permission_callback' => array( $this, 'get_item_permissions_check' ),
				'args'                => $this->get_item_args()
			)
		);

        register_rest_route( $namespace, $this->id_endpoint_url() . '/preview',
            array(
                'methods'             => \WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_preview' ),
                'permission_callback' => array( $this, 'get_item_permissions_check' ),
                'args'                => $this->get_item_args()
            )
        );

        register_rest_route( $namespace, $this->id_endpoint_url() . '/privacy',
            array(
                'methods'             => \WP_REST_Server::EDITABLE,
                'callback'            => array( $this, 'update_privacy_settings' ),
                'permission_callback' => array( $this, 'update_item_permissions_check' ),
                'args'                => $this->get_item_args()
            )
        );

        register_rest_route( $namespace, $this->id_endpoint_url() . '/toggle-active',
            array(
                'methods'             => \WP_REST_Server::EDITABLE,
                'callback'            => array( $this, 'toggle_active' ),
                'permission_callback' => array( $this, 'update_item_permissions_check' ),
            )
        );


	}

    /**
     * Permissions for saving a form via REST API
     * 
     * Secures for POST /cf-api/v2/forms
     * 
     * @since 1.9.0
     */
	public function save_form_permissions_check(\WP_REST_Request $request){
        //Is allowed?
	    if( current_user_can( Caldera_Forms::get_manage_cap( 'manage' ) )  ){
            //Allow if nonce valid
	        return wp_verify_nonce( $request['cf_edit_nonce'],'cf_edit_element');
        }
	    return false;
    }

    /**
     * Save a form via REST API
     * 
     * Handler for POST /cf-api/v2/forms
     * 
     * @since 1.9.0
     */
	public function save_form(\WP_REST_Request $request){
        $saved = Caldera_Forms_Admin::save_a_form(array_merge(['ID' => $request['form_id']],$request['config']));
        if( ! $saved ){
           return new \WP_Error(500,__('Not saved', 'caldera-forms'));
        }
        return new WP_REST_Response(['form_id' => $saved,'form' => \Caldera_Forms_Forms::get_form($saved)], 201 );
    }

    /**
     * @inheritdoc
     *
     * @since 1.5.8
     */
	public function get_item_args(){
	    $args = parent::get_item_args();
	    $args[ 'preview' ] = array(
	        'type' => 'boolean',
            'default' => false,
            'sanitize_callback' => 'rest_sanitize_boolean'
        );

	    return $args;
    }

    /**
     * Fields for the privacy route
     *
     * @since 1.70
     *
     * @return array
     */
    protected function privacy_route_args()
    {
        return array(
            'emailIdentifiers' => array(
                'type' => 'array',
                'required' => false,
                'description' => esc_html__( 'Array of fields that can be used to find personally identifying information saved with this form.', 'caldera-forms' ),
                'sanitize_callback' => array( 'Caldera_Forms_API_Util', 'validate_array_of_field_ids' )
            ),
            'piiFields' => array(
                'type' => 'array',
                'required' => false,
                'description' => esc_html__( 'Array of fields that contain personally identifying information', 'caldera-forms' ),
                'sanitize_callback' => array( 'Caldera_Forms_API_Util', 'validate_array_of_field_ids' )
            ),
            'privacyExporterEnabled' => array(
                'type' => 'boolean',
                'required' => false,
                'description' => esc_html__( 'Array of fields that contain personally identifying information', 'caldera-forms' ),
                'sanitize_callback' => 'rest_sanitize_boolean'
            )
        );
    }

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
        if( $request->get_param( 'privacy' ) ){
            return $this->get_privacy_settings( $request );
        }

	    try{
		    $this->form_object_factory( $request[ 'form_id' ], $request );
	    }catch ( Exception $e ){
		    return Caldera_Forms_API_Response_Factory::error_form_not_found();
	    }

        if ( $request->get_param( 'preview' ) ) {
            return $this->preview_response();
        }



        $response_form = $this->prepare_form_for_response( $this->form, $request );
        return new Caldera_Forms_API_Response( $response_form, 200, array( ) );

    }

    /**
     * Get a form preview via REST API
     *
     * /cf-api/v2/forms/<form_id>/preview
     *
     * @since 1.5.8
     *
     * @param WP_REST_Request $request Request object
     *
     * @return Caldera_Forms_API_Error|Caldera_Forms_API_Response
     */
    public function get_preview( WP_REST_Request $request ){
        try{
            $this->form_object_factory( $request[ 'form_id' ], $request );
        }catch ( Exception $e ){
            return Caldera_Forms_API_Response_Factory::error_form_not_found();
        }

        return $this->preview_response();

    }

    /**
     * Get privacy settings for form
     *
     * @since 1.7.0
     *
     * @param WP_REST_Request $request
     * @return Caldera_Forms_API_Response|Caldera_Forms_API_Error
     */
    public function get_privacy_settings( WP_REST_Request $request )
    {
        try{
            $this->form_object_factory( $request[ 'form_id' ], $request );
        }catch ( Exception $e ){
            return Caldera_Forms_API_Response_Factory::error_form_not_found();
        }

        return new Caldera_Forms_API_Response( $this->form->toArray() );

    }

    /**
     * Update a form's privacy settings
     *
     * @since 1.7.0
     *
     * @param WP_REST_Request $request
     * @return Caldera_Forms_API_Error|Caldera_Forms_API_Response
     */
    public function update_privacy_settings( WP_REST_Request $request ){
        try{
            $this->form_object_factory( $request[ 'form_id' ], $request );
        }catch ( Exception $e ){
            return Caldera_Forms_API_Response_Factory::error_form_not_found();
        }

        if( isset( $request[ 'emailIdentifyingFields' ] ) && is_array( $request[ 'emailIdentifyingFields' ]  ) ){
            $this->form->set_email_identifying_fields( $request[ 'emailIdentifyingFields' ] );
        }

        if( isset( $request[ 'piiFields' ] ) && is_array( $request[ 'piiFields' ]  ) ){
            $this->form->set_pii_fields( $request[ 'piiFields' ] );
        }

        //This will save settings (form)
        if( true === boolval($request[ 'privacyExporterEnabled' ] ) ){
            $this->form = $this->form->enable_privacy_exporter();
        }else {
            $this->form->disable_privacy_exporter();
        }
        return new Caldera_Forms_API_Response( $this->form->toArray() );

    }


	/**
	 * Get form revisions
	 *
	 * GET /forms/<form-id>/revision
	 *
	 * @since 1.5.3
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return Caldera_Forms_API_Response
	 */
    public function get_revisions( WP_REST_Request $request ){
    	$form_id = $request[ 'form_id' ];
    	$revisions = Caldera_Forms_Forms::get_revisions( $request[ 'form_id' ], true );
	    $response_data = array();
	    foreach ( $revisions as $revision ){
		    $response_data[] = array(
		    	'id' => $revision,
			    'edit' => Caldera_Forms_Admin::form_edit_link( $form_id, $revision )
		    );
	    }
	    if( empty( $response_data ) ){
		    return new Caldera_Forms_API_Response( array( 'message' => __( 'No Revisions Found For This Form', 'caldera-forms' ) ) );

	    }
	    return new Caldera_Forms_API_Response( $response_data );
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
		            $this->form_object_factory( $id, $request );
	            }catch ( Exception $e ){
		           continue;
	            }
                $prepared[ $id ] = $this->prepare_form_for_response( $this->form, $request );
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
            ),
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
     * Create a form
     *
     * @since 1.8.0
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return Caldera_Forms_API_Response|Caldera_Forms_API_Error
     */
    public function create_item( WP_REST_Request $request ) {
        $data = [];
        foreach ( array_keys($this->args_for_create()) as $key ){
            $data[$key] = $request[$key];
        }

        $new_form = Caldera_Forms_Forms::create_form( $data );
        if( ! empty($new_form ) ){
            try{
                $this->form_object_factory( $new_form[ 'ID' ], $request );
            }catch ( Exception $e ){
                return Caldera_Forms_API_Response_Factory::error_form_not_found();
            }

            $response_form = $this->prepare_form_for_response( $this->form, $request );
            return new Caldera_Forms_API_Response( $response_form, 200, array( ) );
        }
        return Caldera_Forms_API_Response_Factory::error_form_not_created();

    }

    /**
     * @inheritdoc
     * @since 1.8.0
     */
    public function args_for_create(){
        $templates = Caldera_Forms_Admin::internal_form_templates();
        return [
            'ID' => [
                'type' => 'string',
                'description' => __( 'The desired form ID', 'caldera-forms' ),
                'required' => false,
                'default' => '',
                'sanitize_callback' => 'caldera_forms_very_safe_string'
            ],
            'name' => [
                'type' => 'string',
                'description' => __( 'The name for the form', 'caldera-forms' ),
                'required' => false,
                'default' => '',
                'sanitize_callback' => 'caldera_forms_very_safe_string'
            ],
            'type' => [
                'type' => 'string',
                'description' => __( 'The type of form to create', 'caldera-forms' ),
                'required' => true,
                'default' => 'primary',
                'enum' => [
                    'primary',
                    'revision'
                ]
            ],
            'template' => [
                'description' => __( 'The form template to use', 'caldera-forms' ),
                'type' => 'string',
                'required' => false,
                'enum' => array_keys($templates)
            ],
            'clone' => [
                'description' => __( 'The ID of a form to clone', 'caldera-forms' ),
                'type' => 'string',
                'required' => false,
            ]
        ];
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
		if(is_null($order)) {
			$order = [];
		}

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
        if( ! empty( $field[ 'label' ] ) ){
            $label = sanitize_text_field($field[ 'label' ]);
        }elseif ( ! empty( $field[ 'name' ] ) ){
            $label = sanitize_text_field($field[ 'name' ]);
        }else{
            $label = $field[ 'ID' ];
        }
		$field = array(
			'id' => caldera_forms_very_safe_string( $field[ 'ID' ] ),
			'label' =>  $label
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

    /**
     * Create form preview response
     *
     * @since 1.5.8
     *
     * @return Caldera_Forms_API_Response
     */
    protected function preview_response(){
        $html = Caldera_Forms::render_form($this->form->toArray());

        $css = array_merge(
            Caldera_Forms_Render_Assets::get_core_styles(),
            Caldera_Forms_Render_Assets::get_field_styles()
        );
        $js = array_merge(
            Caldera_Forms_Render_Assets::get_core_scripts(),
            Caldera_Forms_Render_Assets::get_field_scripts()
        );

        $prepared_css = array();
        $prepared_js = array();
        foreach ( $css as $key => $url ){
            $slug = Caldera_Forms_Render_Assets::make_slug( $key );
            if( ! wp_style_is( $slug ) ){
                continue;
            }
            $prepared_css[ $slug ] = esc_url( $url );
        }
        foreach ( $js as $key => $url ){
            $slug = Caldera_Forms_Render_Assets::make_slug( $key );
            if( ! wp_script_is( $slug ) ){
                continue;
            }
           $prepared_js[ $slug ] = esc_url( $url );
        }
        $data = array(
            'html' => $html,
            'css' => $prepared_css,
            'js' => $prepared_js,
        );
        return new Caldera_Forms_API_Response($data, 200, array());
    }


    /**
     * Toggle a form's active/inactive state
     *
     * @since 1.8.0
     *
     * @param WP_REST_Request $request
     * @return Caldera_Forms_API_Error|Caldera_Forms_API_Response
     */
    public function toggle_active(\WP_REST_Request $request ){
        add_filter( 'caldera_forms_save_revision', '__return_false' );
        try{
			$form = Caldera_Forms_Forms::get_form( $request[ 'form_id' ] );
        }catch ( Exception $e ){
            return Caldera_Forms_API_Response_Factory::error_form_not_found();
        }

        if ( ! empty( $form[ 'form_draft' ] ) ) {
            Caldera_Forms_Forms::form_state( $form );
        }else{
            Caldera_Forms_Forms::form_state( $form , false );
        }

        add_filter( 'caldera_forms_save_revision', '__return_true' );
        return new Caldera_Forms_API_Response(
            ['active' => $form[ 'form_draft' ]]
        );
    }

}