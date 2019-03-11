<?php


namespace calderawp\calderaforms\pro\api\local;

use calderawp\calderaforms\pro\container;
use calderawp\calderaforms\pro\settings\active;


/**
 * Class route for settings save/get
 *
 * Local REST API route
 * @package calderawp\calderaforms\pro\api\local
 */
class settings implements \Caldera_Forms_API_Route
{

	/**
	 * @inheritdoc
	 */
	public function add_routes($namespace)
	{
		register_rest_route($namespace, '/settings/pro',
			[
				'methods' => 'POST',
				'callback' => [ $this, 'update_settings' ],
				'args' => [
					'accountId' => [
						'type' => 'integer',
						'required' => false,
						'default' => 0,
						'sanatization_callback' => 'absint',
					],
					'apiKey' => [
						'type' => 'string',
						'required' => false,
					],
					'apiSecret' => [
						'type' => 'string',
						'required' => false,
					],
					'generatePDFs' => [
						'type' => 'boolean',
						'required' => false,
						'sanitize_callback' => 'rest_sanitize_boolean',
					],
					'enhancedDelivery' => [
						'type' => 'boolean',
						'required' => false,
						'sanitize_callback' => 'rest_sanitize_boolean',
					],
					'plan' => [
						'type' => 'string',
						'required' => false,
					],
					'activate' => [
						'type' => 'boolean',
					],
					'logLevel' => [
						'type' => 'integer',
						'required' => false,
						'default' => 250,
						'sanitize_callback' => 'absint',
					],
				],
				'permission_callback' => [ $this, 'permissions' ],
			]
		);
		register_rest_route($namespace, '/settings/pro',
			[
				'methods' => 'GET',
				'callback' => [ $this, 'get_settings' ],
				'args' => [
					'form' => [
						'type' => 'string',
						'required' => false,
					],
				],
				'permission_callback' => [ $this, 'permissions' ],
			]
		);
	}

	/**
	 * Check request permissions
	 *
	 * @since 0.0.1
	 *
	 * @return bool
	 */
	public function permissions()
	{
		return current_user_can(\Caldera_Forms::get_manage_cap('admin'));
	}

	/**
	 * Update settings via WP REST API
	 *
	 * @since 0.0.1
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return mixed|\WP_REST_Response
	 */
	public function update_settings(\WP_REST_Request $request)
	{
		$settings = container::get_instance()->get_settings();
		if ( !empty($request[ 'accountId' ]) ) {
			$settings->set_account_id($request[ 'accountId' ]);
		}

		if ( isset($request[ 'apiKey' ]) ) {
			$settings->set_api_public($request[ 'apiKey' ]);
		}

		if ( isset($request[ 'apiSecret' ]) ) {
			$settings->set_api_secret($request[ 'apiSecret' ]);
		}

		if ( !empty($request[ 'forms' ]) ) {
			foreach ( $request[ 'forms' ] as $form ) {
				$this->handle_form($form);
			}
		}

		if ( !empty($request[ 'plan' ]) ) {
			$settings->set_plan($request[ 'plan' ]);
		}

		if ( false === $request[ 'enhancedDelivery' ] || 'false' === $request[ 'enhancedDelivery' ] ) {
			$settings->set_enhanced_delivery(false);
		}

		if ( true === $request[ 'enhancedDelivery' ] ) {
			$settings->set_enhanced_delivery('true');
		}

		if ( !empty($request[ 'logLevel' ]) ) {
			$settings->set_log_level($request[ 'logLevel' ]);
		}

		$settings->save();

		return rest_ensure_response(container::get_instance()->get_settings()->toArray());

	}

	/**
	 * Handle saving settings of a form
	 *
	 * @since 0.0.1
	 *
	 * @param array $form
	 */
	protected function handle_form(array $form)
	{
		$setting = container::get_instance()->get_settings()->get_form($form[ 'form_id' ]);
		foreach ( $setting->get_properties() as $property ) {
			if ( isset($form[ $property ]) ) {
				$setting->$property = $form[ $property ];
			}
		}

		$setting->save();

	}

	/**
	 * Read settings via WP REST API
	 *
	 * @since 0.0.1
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return mixed|\WP_REST_Response
	 */
	public function get_settings(\WP_REST_Request $request)
	{
		if ( $request->get_param('form') ) {

		}
		return rest_ensure_response(container::get_instance()->get_settings()->toArray());

	}
}
