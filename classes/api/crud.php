<?php

/**
 * Abstract class the all REST API route collections that follow CRUD pattern should extend
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
abstract class Caldera_Forms_API_CRUD implements Caldera_Forms_API_Route {

	/**
	 * Form object for this response
	 *
	 * @since 1.5.0
	 *
	 * @var Caldera_Forms_API_Form|Caldera_Forms_API_Privacy
	 */
	protected $form;

	/**
	 * Namespace for API
	 *
	 * @since 1.5.0
	 *
	 * @var string
	 */
	protected $namespace;

	/**
	 * @inheritdoc
	 *
	 * @since 1.4.4 
	 */
	public function add_routes( $namespace ) {
		$this->namespace = $namespace;
		register_rest_route( $namespace, $this->non_id_endpoint_url(), array(
				array(
					'methods'         => \WP_REST_Server::READABLE,
					'callback'        => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
					'args'            => $this->get_items_args(),
				),
				array(
					'methods'         => \WP_REST_Server::CREATABLE,
					'callback'        => array( $this, 'create_item' ),
					'permission_callback' => array( $this, 'create_item_permissions_check' ),
					'args'            => $this->request_args()
				),
			)
		);
		register_rest_route( $namespace, $this->id_endpoint_url(), array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => $this->get_item_args()
				),
				array(
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_item' ),
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
					'args'                => $this->request_args(  )
				),
				array(
					'methods'             => \WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_item' ),
					'permission_callback' => array( $this, 'delete_item_permissions_check' ),
					'args'                => array(
						'force' => array(
							'default'  => false,
							'required' => false,
						),
						'all'   => array(
							'default'  => false,
							'required' => false,
						),
						'id'    => array(
							'default'               => 0,
							'sanatization_callback' => 'absint'
						)
					),
				),
			)
		);

		register_rest_route( $namespace, '/' . $this->route_base(), array(
			'methods' => 'GET',
			'callback'            => array( $this, 'index' ),
		) );
	}

    /**
     * Get the allow attributes for get items calls
     *
     * @since unknown
     *
     * @return array
     */
	public function get_item_args(){
		return array(
			'context' => array(
				'default' => 'view',
			),
			'entry_list_only_fields' => array(
				'required' => false,
				'default' => false,
			)
		);
	}

	/**
	 * Callback for the index of this collection
	 *
	 * @since 1.4.4
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return Caldera_Forms_API_Response
	 */
	public function index( WP_REST_Request $request ){
		/** @var WP_REST_Server $wp_rest_server */
		global $wp_rest_server;
		$routes = $wp_rest_server->get_routes();
		$endpoints = array();
		foreach ( $routes as $route => $route_endpoints ){
			if( false !== strpos( $route, $this->namespace . '/' . $this->route_base() ) ){
				$endpoints[ $route ] = $route_endpoints;
			}
		}

		$data = array(
			'namespace' => $this->namespace,
			'routes' => $wp_rest_server->get_data_for_routes( $endpoints, $request['context'] ),
		);

		return new Caldera_Forms_API_Response( $data, 200, array() );

	}

	/**
	 * Define query arguments
	 *
	 * @since 1.4.4 
	 *
	 * @return array(
	 */
	public function request_args(){
		//must ovveride, should be abstract but PHP5.2
		_doing_it_wrong( __FUNCTION__, '', '1.5.0' );
	}

	/**
	 * Check if a given request has access to get items
	 *
	 * @since 1.4.4 
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return \WP_Error|bool
	 */
	public function get_items_permissions_check( WP_REST_Request $request ){
		//must ovveride, should be abstract but PHP5.2
		_doing_it_wrong( __FUNCTION__, '', '1.5.0' );
	}

	/**
	 * Check if a given request has access to get a specific item
	 *
	 * @since 1.4.4 
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return \WP_Error|bool
	 */
	public function get_item_permissions_check( WP_REST_Request $request ) {
		return $this->get_items_permissions_check(  $request );
	}

	/**
	 * Check if a given request has access to create items
	 *
	 * @since 1.4.4 
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return \WP_Error|bool
	 */
	public function create_item_permissions_check( WP_REST_Request $request ){
		//must ovveride, should be abstract but PHP5.2
		_doing_it_wrong( __FUNCTION__, '', '1.5.0' );
	}

	/**
	 * Check if a given request has access to update a specific item
	 *
	 * @since 1.4.4 
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return \WP_Error|bool
	 */
	public function update_item_permissions_check( WP_REST_Request $request ) {
		return $this->create_item_permissions_check( $request );
	}

	/**
	 * Check if a given request has access to delete a specific item
	 *
	 * @since 1.4.4 
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return \WP_Error|bool
	 */
	public function delete_item_permissions_check( WP_REST_Request $request ) {
		return $this->create_item_permissions_check( $request );
	}

	/**
	 * Get a collection of items
	 *
	 * @since 1.4.4 
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function get_items( WP_REST_Request $request ) {
		return $this->not_yet_response();
	}

	/**
	 * Get one item from the collection
	 *
	 * @since 1.4.4 
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function get_item( WP_REST_Request $request ) {
		return $this->not_yet_response();
	}

	/**
	 * Create one item from the collection
	 *
	 * @since 1.4.4 
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return Caldera_Forms_API_Response|Caldera_Forms_API_Error
	 */
	public function create_item( WP_REST_Request $request ) {
		return $this->not_yet_response();
	}

	/**
	 * Update one item from the collection
	 *
	 * @since 1.4.4 
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return Caldera_Forms_API_Response|Caldera_Forms_API_Error
	 */
	public function update_item( WP_REST_Request $request ) {
		return $this->not_yet_response();
	}

	/**
	 * Delete one item from the collection
	 *
	 * @since 1.4.4 
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return Caldera_Forms_API_Response|Caldera_Forms_API_Error
	 */
	public function delete_item( WP_REST_Request $request ) {
		return $this->not_yet_response();
	}

	/**
	 * Return a 501 error for non-existant route
	 *
	 * @since 1.4.4 
	 *
	 * @return Caldera_Forms_API_Response
	 */
	protected function not_yet_response() {
		$error =  new Caldera_Forms_API_Error( 'not-implemented-yet', __( 'Route Not Yet Implemented :(', 'caldera-forms' )  );
		return new Caldera_Forms_API_Response( $error, 501, array() );
	}

	/**
	 * Get class shortname and use as base
	 *
	 * @since 1.4.4 
	 *
	 * MUST  ovveride in subclass with a hardcoded string.
	 */
	protected function route_base() {
		//must ovveride, should be abstract but PHP5.2
		_doing_it_wrong( __FUNCTION__, '', '1.5.0' );
	}

	/**
	 * Form the endpoint URL that deos not include item ID
	 *
	 * Used by for get_items() and create_items()
	 *
	 * @since 1.4.4 
	 *
	 * @return string
	 */
	protected function non_id_endpoint_url() {
		return '/' . $this->route_base();

	}

	/**
	 * Form the endpoint URL that includes item ID
	 *
	 * Used by for get_item() and update_time() and delete_item()
	 *
	 * @since 1.4.4 
	 *
	 * @return string
	 */
	public function id_endpoint_url() {
		return '/' . $this->route_base() . '/(?P<id>[\w-]+)';
	}

	/**
	 * @return array
	 */
	public function get_items_args() {
		return array(
			'page'     => array(
				'default'           => 1,
				'sanitize_callback' => 'absint',
			),
			'per_page' => array(
				'default'           => 20,
				'sanitize_callback' => 'absint',
			),
			'limit'    => array(
				'default'           => 10,
				'sanitize_callback' => 'absint',
			)
		);
	}

	/**
	 * Factory for Caldera_Forms_API_Form objects
	 *
	 * @since 1.5.0
	 *
	 * @param string $id Form ID
	 * @param WP_REST_Request $request Current REST API request
	 * @param bool $set_prop Optional. Set in $form property of object if true, the default. If false, return.
	 * @param bool $privacy_context Optional. If false, a Caldera_Forms_API_Privacy is returned. If true, the default, a Caldera_Forms_API_Form is returned.
	 *
	 * @return Caldera_Forms_API_Form|Caldera_Forms_API_Privacy
	 * @throws Exception
	 */
	protected function form_object_factory( $id, WP_REST_Request $request, $set_prop = true, $privacy_context= false ){
	    $form = Caldera_Forms_Forms::get_form( $id );

		if( empty( $form ) || empty( $form[ 'ID' ] ) || empty( $form[ 'name' ] ) ){
			throw new Exception();
		}

        if ($privacy_context) {
            $obj = new Caldera_Forms_API_Form($form);
        }else{
		    $obj = new Caldera_Forms_API_Privacy($form);
        }

        $obj->set_request( $request );
		if ( $set_prop ) {
			$this->form = $obj;
		} else {

			return $obj;
		}

	}

}