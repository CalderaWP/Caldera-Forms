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
				'permission_callback' => array( $this, 'permissions_check' ),
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
                'permission_callback' => array( $this, 'permissions_check' ),
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
                'permission_callback' => array( $this, 'permissions_check' ),
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
            $new_values[ $key ] = isset($request[ 'styleIncludes' ][$key]) && $request[ 'styleIncludes' ][$key] ? true : false
        }

        update_option( '_caldera_forms_styleincludes', $new_values);

        if( $request['cdnEnable' ] ){
            Caldera_Forms::settings()->get_cdn()->enable();
        }else{
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

}