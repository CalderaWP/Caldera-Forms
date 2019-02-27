<?php

/**
 * REST API route for settings
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_API_Settings implements  Caldera_Forms_API_Route{

	/**
	 * @since 1.5.0
	 * @inheritdoc
	 */
	public function add_routes( $namespace ) {
		register_rest_route( $namespace, '/settings/entries',
			array(
				'methods'         => array( 'POST' ),
				'callback'        => array( $this, 'update_entry_settings' ),
				'permission_callback' => array( $this, 'create_item_permissions_check' ),
				'args'            => array(
					'per_page' => array(
						'required'          => 'false',
						'type'              => 'integer',
					)
				)
			)
		);
        register_rest_route( $namespace, '/settings',
            array(
                'methods'         => array( 'POST' ),
                'callback'        => array( $this, 'update_settings' ),
                'permission_callback' => array( $this, 'create_item_permissions_check' ),
                'args'            => array(
                    'styleIncludes' => array(
                        'required'          => 'false',
                        'type'              => 'array',
                    ),
                    'cdnEnable' => array(
                        'required'          => 'false',
                        'type'              => 'boolean',
                    ),
                )
            )
        );
        register_rest_route( $namespace, '/settings',
            array(
                'methods'         => array( 'GET' ),
                'callback'        => array( $this, 'get_settings' ),
                'permission_callback' => array( $this, 'get_items_permissions_check' ),
            )
        );

	}

	/**
	 * Update entry settings
	 *
	 * @since 1.5.0
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return Caldera_Forms_API_Response
	 */
	public function update_entry_settings( WP_REST_Request $request ){
		Caldera_Forms_Entry_Viewer::update_entries_per_page( $request[ 'per_page' ] );
		$response = new Caldera_Forms_API_Response( array(
			'per_page' => Caldera_Forms_Entry_Viewer::entries_per_page()
		));
		return $response;
	}

    /**
     * Update general settings
     *
     * @since 1.7.3
     *
     * @param WP_REST_Request $request
     * @return Caldera_Forms_API_Response
     */
	public function update_settings( \WP_REST_Request $request ){
        $style_includes = Caldera_Forms_Render_Assets::get_style_includes();
        $new_values = [];
        foreach ( $style_includes as $key => $saved ){
			$request[$key] = !is_bool($request[$key]) && $request[$key] === "false" ? false : $request[$key];
            $new_values[ $key ] = isset($request[$key]) ? boolval($request[$key]) : $saved;
        }

        update_option( '_caldera_forms_styleincludes', $new_values);

        if( $request['cdnEnable'] === true ){
            Caldera_Forms::settings()->get_cdn()->enable();
        }else if( $request['cdnEnable'] === false ) {
            Caldera_Forms::settings()->get_cdn()->disable();
        }

        return Caldera_Forms_API_Response_Factory::general_settings_response(
            $new_values,
            Caldera_Forms::settings()->get_cdn()->enabled(),
            201
        );

    }

    /**
     * Get general settings
     *
     * @since 1.7.3
     *
     * @return Caldera_Forms_API_Response
     */
    public function get_settings(){
        return Caldera_Forms_API_Response_Factory::general_settings_response(
            Caldera_Forms_Render_Assets::get_style_includes(),
            Caldera_Forms::settings()->get_cdn()->enabled(),
            201
        );
    }


	/**
	* Permissions for settings read
	*
	* @since 1.8.0
	*
	* @param WP_REST_Request $request
	*
	* @return bool
	*/
	public function get_items_permissions_check( WP_REST_Request $request ){

		$allowed = current_user_can( Caldera_Forms::get_manage_cap( 'entry-view' ) );

		/**
		 * Filter permissions for viewing settings config via Caldera Forms REST API
		 *
		 * @since 1.8.0
		 *
		 * @param bool $allowed Is request authorized?
		 * @param WP_REST_Request $request The current request
		 */
		return apply_filters( 'caldera_forms_api_allow_settings_view', $allowed, $request );

	}

	/**
	 * Permissions for settings create/update/delete
	 *
	 * @since 1.8.0
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return bool
	 */
	public function create_item_permissions_check( WP_REST_Request $request ){

		$allowed = current_user_can( Caldera_Forms::get_manage_cap( 'entry-edit' ) );

		/**
		 * Filter permissions for updating  settings via Caldera Forms REST API
		 *
		 * @since 1.8.0
		 *
		 * @param bool $allowed Is request authorized?
		 * @param WP_REST_Request $request The current request
		 */
		return apply_filters( 'caldera_forms_api_allow_settings_edit', $allowed, $request );

	}

}