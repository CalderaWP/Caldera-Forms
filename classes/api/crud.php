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
	 * @inheritdoc
	 *
	 * @since 1.5.0
	 */
	public function add_routes( $namespace ) {
		$base = $this->route_base();
		register_rest_route( $namespace, '/' . $base, array(
				array(
					'methods'         => \WP_REST_Server::READABLE,
					'callback'        => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
					'args'            => array(
						'page' => array(
							'default' => 1,
							'sanitize_callback'  => 'absint',
						),
						'limit' => array(
							'default' => 10,
							'sanitize_callback'  => 'absint',
						)
					),
				),
				array(
					'methods'         => \WP_REST_Server::CREATABLE,
					'callback'        => array( $this, 'create_item' ),
					'permission_callback' => array( $this, 'create_item_permissions_check' ),
					'args'            => $this->request_args()
				),
			)
		);
		register_rest_route( $namespace, '/' . $base . '/(?P<id>[\w-]+)', array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => array(
						'context' => array(
							'default' => 'view',
						)
					),
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
	}
	
	/**
	 * Define query arguments
	 *
	 * @since 1.5.0
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
	 * @since 1.5.0
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
	 * @since 1.5.0
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
	 * @since 1.5.0
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
	 * @since 1.5.0
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
	 * @since 1.5.0
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
	 * @since 1.5.0
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
	 * @since 1.5.0
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
	 * @since 1.5.0
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
	 * @since 1.5.0
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
	 * @since 1.5.0
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
	 * @since 1.5.0
	 *
	 * @return Caldera_Forms_API_Response
	 */
	protected function not_yet_response() {
		$error =  new Caldera_Forms_API_Error( 'not-implemented-yet', __( 'Route Not Yet Implemented :(', 'caldera-forms' )  );
		return new Caldera_Forms_API_Response( $error, 501, [] );
	}

	/**
	 * Get class shortname and use as base
	 *
	 * @since 1.5.0
	 *
	 * Probably better to ovveride in subclass with a hardcoded string.
	 */
	protected function route_base() {
		return substr( strrchr( get_class( $this ), '\\' ), 1 );
	}

}