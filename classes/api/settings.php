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




}