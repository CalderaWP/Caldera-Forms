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
abstract class Caldera_Forms_API_CRUD implements Caldera_Forms_API_Route
{

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
    public function add_routes($namespace)
    {
        $this->namespace = $namespace;
        register_rest_route($namespace, $this->non_id_endpoint_url(), array(
                array(
                    'methods' => \WP_REST_Server::READABLE,
                    'callback' => array($this, 'get_items'),
                    'permission_callback' => array($this, 'get_items_permissions_check'),
                    'args' => $this->get_items_args(),
                ),
                array(
                    'methods' => \WP_REST_Server::CREATABLE,
                    'callback' => array($this, 'create_item'),
                    'permission_callback' => array($this, 'create_item_permissions_check'),
                    'args' => $this->request_args()
                ),
            )
        );

        register_rest_route($namespace, $this->id_endpoint_url(), array_values($this->id_route()));

        register_rest_route($namespace, '/' . $this->route_base(), array(
            'methods' => 'GET',
            'callback' => array($this, 'index'),
        ));
    }

    /**
     * Get the allow attributes for get items calls
     *
     * @return array
     * @since unknown
     *
     */
    public function get_item_args()
    {
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
     * @param WP_REST_Request $request
     *
     * @return Caldera_Forms_API_Response
     * @since 1.4.4
     *
     */
    public function index(WP_REST_Request $request)
    {
        /** @var WP_REST_Server $wp_rest_server */
        global $wp_rest_server;
        $routes = $wp_rest_server->get_routes();
        $endpoints = array();
        foreach ($routes as $route => $route_endpoints) {
            if (false !== strpos($route, $this->namespace . '/' . $this->route_base())) {
                $endpoints[$route] = $route_endpoints;
            }
        }

        $data = array(
            'namespace' => $this->namespace,
            'routes' => $wp_rest_server->get_data_for_routes($endpoints, $request['context']),
        );

        return new Caldera_Forms_API_Response($data, 200, array());

    }

    /**
     * Define query arguments for GET requests
     *
     * @return array
     * @since 1.4.4
     *
     */
    public function request_args()
    {
        //must ovveride, should be abstract but PHP5.2
        _doing_it_wrong(__FUNCTION__, '', '1.5.0');
    }

    /**
     * Define query arguments for POST/PUT requests
     *
     * @return array
     * @since 1.8.0
     *
     */
    public function args_for_create()
    {
        return $this->request_args();
    }

    /**
     * Check if a given request has access to get items
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return \WP_Error|bool
     * @since 1.4.4
     *
     */
    public function get_items_permissions_check(WP_REST_Request $request)
    {
        //must ovveride, should be abstract but PHP5.2
        _doing_it_wrong(__FUNCTION__, '', '1.5.0');
    }

    /**
     * Check if a given request has access to get a specific item
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return \WP_Error|bool
     * @since 1.4.4
     *
     */
    public function get_item_permissions_check(WP_REST_Request $request)
    {
        return $this->get_items_permissions_check($request);
    }

    /**
     * Check if a given request has access to create items
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return \WP_Error|bool
     * @since 1.4.4
     *
     */
    public function create_item_permissions_check(WP_REST_Request $request)
    {
        //must ovveride, should be abstract but PHP5.2
        _doing_it_wrong(__FUNCTION__, '', '1.5.0');
    }

    /**
     * Check if a given request has access to update a specific item
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return \WP_Error|bool
     * @since 1.4.4
     *
     */
    public function update_item_permissions_check(WP_REST_Request $request)
    {
        return $this->create_item_permissions_check($request);
    }

    /**
     * Check if a given request has access to delete a specific item
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return \WP_Error|bool
     * @since 1.4.4
     *
     */
    public function delete_item_permissions_check(WP_REST_Request $request)
    {
        return $this->create_item_permissions_check($request);
    }

    /**
     * Get a collection of items
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return \WP_Error|\WP_REST_Response
     * @since 1.4.4
     *
     */
    public function get_items(WP_REST_Request $request)
    {
        return $this->not_yet_response();
    }

    /**
     * Get one item from the collection
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return \WP_Error|\WP_REST_Response
     * @since 1.4.4
     *
     */
    public function get_item(WP_REST_Request $request)
    {
        return $this->not_yet_response();
    }

    /**
     * Create one item from the collection
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return Caldera_Forms_API_Response|Caldera_Forms_API_Error
     * @since 1.4.4
     *
     */
    public function create_item(WP_REST_Request $request)
    {
        return $this->not_yet_response();
    }

    /**
     * Update one item from the collection
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return Caldera_Forms_API_Response|Caldera_Forms_API_Error
     * @since 1.4.4
     *
     */
    public function update_item(WP_REST_Request $request)
    {
        return $this->not_yet_response();
    }

    /**
     * Delete one item from the collection
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return Caldera_Forms_API_Response|Caldera_Forms_API_Error
     * @since 1.4.4
     *
     */
    public function delete_item(WP_REST_Request $request)
    {
        return $this->not_yet_response();
    }

    /**
     * Return a 501 error for non-existant route
     *
     * @return Caldera_Forms_API_Response
     * @since 1.4.4
     *
     */
    protected function not_yet_response()
    {
        $error = new Caldera_Forms_API_Error('not-implemented-yet',
            __('Route Not Yet Implemented :(', 'caldera-forms'));
        return new Caldera_Forms_API_Response($error, 501, array());
    }

    /**
     * Get class shortname and use as base
     *
     * @since 1.4.4
     *
     * MUST  ovveride in subclass with a hardcoded string.
     */
    protected function route_base()
    {
        //must ovveride, should be abstract but PHP5.2
        _doing_it_wrong(__FUNCTION__, '', '1.5.0');
    }

    /**
     * Form the endpoint URL that deos not include item ID
     *
     * Used by for get_items() and create_items()
     *
     * @return string
     * @since 1.4.4
     *
     */
    protected function non_id_endpoint_url()
    {
        return '/' . $this->route_base();

    }

    /**
     * Form the endpoint URL that includes item ID
     *
     * Used by for get_item() and update_time() and delete_item()
     *
     * @return string
     * @since 1.4.4
     *
     */
    public function id_endpoint_url()
    {
        return '/' . $this->route_base() . '/(?P<id>[\w-]+)';
    }

    /**
     * @return array
     */
    public function get_items_args()
    {
        return array(
            'page' => array(
                'default' => 1,
                'sanitize_callback' => 'absint',
            ),
            'per_page' => array(
                'default' => 20,
                'sanitize_callback' => 'absint',
            ),
            'limit' => array(
                'default' => 10,
                'sanitize_callback' => 'absint',
            )
        );
    }

    /**
     * Factory for Caldera_Forms_API_Form objects
     *
     * @param string $id Form ID
     * @param WP_REST_Request $request Current REST API request
     * @param bool $set_prop Optional. Set in $form property of object if true, the default. If false, return.
     * @param bool $privacy_context Optional. If false, a Caldera_Forms_API_Privacy is returned. If true, the default, a Caldera_Forms_API_Form is returned.
     *
     * @return Caldera_Forms_API_Form|Caldera_Forms_API_Privacy
     * @throws Exception
     * @since 1.5.0
     *
     */
    protected function form_object_factory($id, WP_REST_Request $request, $set_prop = true, $privacy_context = false)
    {
        $form = Caldera_Forms_Forms::get_form($id);

        if (empty($form) || (empty($form['ID']) && empty($form['name']))) {
            throw new Exception();
        }

        if ($privacy_context) {
            $obj = new Caldera_Forms_API_Form($form);
        } else {
            $obj = new Caldera_Forms_API_Privacy($form);
        }

        $obj->set_request($request);
        if ($set_prop) {
            $this->form = $obj;
        } else {

            return $obj;
        }

    }

    /**
     * Get the 3rd array of arguments for register_rest_route for the route with id in it
     *
     *
     * @return array Indexed by HTTP method, use array_values() on result please.
     * @since 1.9.2
     *
     * BTW This is work-around for the real problem $this->request_args() serves too many purposes.
     * It's not the best API design, I was still an egg.
     * This solution works, but please no one read this and think Josh thinks this is a good idea.
     */
    protected function id_route()
    {
        return [
            \WP_REST_Server::READABLE => [
                'methods' => \WP_REST_Server::READABLE,
                'callback' => array($this, 'get_item'),
                'permission_callback' => array($this, 'get_item_permissions_check'),
                'args' => $this->get_item_args()
            ],
            \WP_REST_Server::EDITABLE => [
                'methods' => \WP_REST_Server::EDITABLE,
                'callback' => array($this, 'update_item'),
                'permission_callback' => array($this, 'update_item_permissions_check'),
                'args' => $this->request_args()
            ],
            \WP_REST_Server::DELETABLE => [
                'methods' => \WP_REST_Server::DELETABLE,
                'callback' => array($this, 'delete_item'),
                'permission_callback' => array($this, 'delete_item_permissions_check'),
                'args' => array(
                    'force' => array(
                        'default' => false,
                        'required' => false,
                    ),
                    'all' => array(
                        'default' => false,
                        'required' => false,
                    ),
                    'id' => array(
                        'default' => 0,
                        'sanatization_callback' => 'absint'
                    )
                ),
            ]
        ];
    }

}