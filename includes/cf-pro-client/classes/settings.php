<?php


namespace calderawp\calderaforms\pro;

use calderawp\calderaforms\pro\api\keys;
use calderawp\calderaforms\pro\settings\form;
use Monolog\Logger as Monolog;


/**
 * Class settings
 *
 * Handles CF Pro settings
 *
 * @package calderawp\calderaforms\pro
 */
class settings extends repository
{

	/**
	 * ID of default layout
	 *
	 * @since 0.0.1
	 *
	 * @var int
	 */
	protected $default_layout;

	/**
	 * Option key for storage
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	protected $option_key = '_cf_pro_settings';

	/**
	 * Option key for storage
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	protected static $_option_key = '_cf_pro_settings';

	/**
	 * CF Pro Account ID
	 *
	 * @since 0.0.1
	 *
	 * @var int
	 */
	protected $account_id;

	/**
	 * Create object from saved data
	 *
	 * @since 0.0.1
	 *
	 * @return settings
	 */
	public static function from_saved()
	{
		$settings = new static();
		$saved = get_option(self::$_option_key, []);
		if ( !empty($saved[ 'account_id' ]) ) {
			$settings->set_account_id($saved[ 'account_id' ]);
		}
		if ( !empty($saved[ 'apiKeys' ]) ) {
			$keys = keys::fromArray($saved[ 'apiKeys' ]);
			$settings->set_api_keys($keys);
		}

		if ( isset($saved[ 'enhancedDelivery' ]) ) {
			$settings->set_enhanced_delivery($saved[ 'enhancedDelivery' ]);
		}

		if ( !empty($saved[ 'logLevel' ]) ) {
			$settings->set_log_level($saved[ 'logLevel' ]);
		} else {
			$settings->set_log_level(Monolog::NOTICE);
		}

		if ( !empty($saved[ 'plan' ]) ) {
			$settings->set_plan($saved[ 'plan' ]);
		} else {
			$settings->set_plan('basic');
		}

		return $settings;
	}


	/**
	 * Add an individual form's settings
	 *
	 * @since 0.0.1
	 *
	 * @param form $form $form Form Settings object
	 *
	 * @return form
	 */
	public function set_form($form)
	{

		if ( !$this->has($form->get_form_id()) ) {
			$forms = $this->get('forms', []);

			$forms[] = $form->get_form_id();
			$this->set('forms', $forms);
		}

		$this->set($form->get_form_id(), $form);


		return $this->get($form->get_form_id());

	}

	/**
	 * Get form object by ID
	 *
	 * Will attempt to get from repo, then DB. Failing that will create empty instance
	 *
	 * @since 0.0.1
	 *
	 * @param string $form_id Form ID
	 *
	 * @return form
	 */
	public function get_form($form_id)
	{
		if ( !$this->has($form_id) ) {
			$_form = form::from_saved($form_id);
			if ( is_object($_form) ) {
				$this->set($form_id, $_form);
			}
		}

		return $this->get($form_id, new form($form_id));

	}

	/**
	 * Set CF Pro account ID
	 *
	 * @since 0.0.1
	 *
	 * @param int $id Account ID
	 *
	 * @return $this
	 */
	public function set_account_id($id)
	{
		$this->account_id = absint($id);
		return $this;
	}

	/**
	 * Get CF Pro account ID
	 *
	 * @since 0.0.1
	 *
	 * @return int
	 */
	public function get_account_id()
	{
		return absint($this->account_id);
	}

	/**
	 * Set CF Pro public key
	 *
	 * @since 0.0.1
	 *
	 * @param string $public
	 *
	 * @return $this
	 */
	public function set_api_public($public)
	{
		$keys = $this->get_api_keys();
		$keys->set_public($public);
		return $this;
	}

	/**
	 * Set CF Pro secret key
	 *
	 * @since 0.0.1
	 *
	 * @param $secret
	 *
	 * @return $this
	 */
	public function set_api_secret($secret)
	{
		$keys = $this->get_api_keys();
		$keys->set_secret($secret);
		return $this;

	}

	/**
	 * Get saved api keys object
	 *
	 * @since 0.0.1
	 *
	 * @return keys
	 */
	public function get_api_keys()
	{
		if ( !$this->has('apiKeys') ) {
			$this->set('apiKeys', new keys());
		}

		return $this->get('apiKeys');

	}

	/**
	 * Set API keys object
	 *
	 * @since 0.0.1
	 *
	 * @param keys $keys
	 */
	public function set_api_keys($keys)
	{
		$this->set('apiKeys', $keys);
	}

	/**
	 * Set if enhanced delivery is enabled
	 *
	 * @param bool $enable
	 */
	public function set_enhanced_delivery($enable)
	{
		$enable = rest_sanitize_boolean($enable);
		$this->set('enhanced_delivery', $enable);
	}

	/**
	 * Check if enhanced delivery setting is enabled
	 *
	 * NOTE: Does not check if it is possible, which $this->send_local() and $this->send_remote() do.
	 *
	 * @since 0.0.1
	 *
	 * @return bool
	 */
	public function get_enhanced_delivery()
	{
		return $this->get('enhanced_delivery', true);

	}

	/**
	 * Checks if we should use local email system or not
	 *
	 * @since 0.0.1
	 *
	 * @return bool
	 */
	public function send_local()
	{
		return !$this->send_remote();
	}

	/**
	 * Checks if we should send with remote API or not
	 *
	 * @since 0.0.1
	 *
	 * @return bool
	 */
	public function send_remote()
	{
		return $this->get_enhanced_delivery();

	}

	/**
	 * Set plan type
	 *
	 * @since 0.0.1
	 *
	 * @param string $plan
	 */
	public function set_plan($plan)
	{
		if ( in_array($plan, [ 'basic', 'apex', 'awesome' ]) ) {
			$this->set('plan', $plan);
		}

	}

	/**
	 * Get plan type
	 *
	 * @since 0.0.1
	 *
	 * @return string
	 */
	public function get_plan()
	{
		return $this->get('plan', 'basic');
	}

	/**
	 * List all log levels from Monolog
	 *
	 * @since 1.5.9
	 *
	 * @return array
	 */
	public function log_levels()
	{
		$levels = Monolog::getLevels();
		$all_levels = [];
		$i = 0;
		foreach ( $levels as $name => $number ) {
			$all_levels[ $i ] = [
				'name' => $name,
				'number' => $number,
			];
			$i++;
		}
		return $all_levels;
	}

	/**
	 * Set log level
	 *
	 * @since 1.5.9
	 *
	 * @param integer $level
	 */
	public function set_log_level($level)
	{
		$all_levels = $this->log_levels();

		$levels = [];
		foreach ( $all_levels as $levelindex ) {
			$levels[] = $levelindex[ 'number' ];
		}
		if ( in_array($level, $levels) ) {
			$this->set('logLevel', $level);
		}
	}

	/**
	 * Get log level
	 *
	 * @since 1.5.9
	 *
	 * @return integer
	 */
	public function get_log_level()
	{
		return $this->get('logLevel', Monolog::NOTICE);
	}

	/**
	 * Return if is basic plan
	 *
	 * @since 0.0.1
	 *
	 * @return bool
	 */
	public function is_basic()
	{
		return 'basic' === $this->get_plan();
	}

	/**
	 * Save settings to database
	 *
	 * @since 0.0.1
	 */
	public function save()
	{
		foreach ( $this->forms() as $form ) {
			$form->save();
		}

		if ( $this->get_api_keys()->get_public() && $this->get_api_keys()->get_secret() ) {

		}

		$data = $this->toArray();
		unset($data[ 'forms' ]);
		update_option($this->option_key, $data);

	}


	/**
	 * Get settings as array
	 *
	 * @since 0.0.1
	 *
	 * @return array
	 */
	public function toArray()
	{
		$data = [
			'account_id' => $this->get_account_id(),
			'apiKeys' => $this->get_api_keys()->toArray(),
			'forms' => $this->forms_to_array(),
			'enhancedDelivery' => $this->get_enhanced_delivery(),
			'plan' => $this->get_plan(),
			'logLevel' => $this->get_log_level(),
		];

		return $data;
	}

	/**
	 * Get all form settings as array
	 *
	 * @since 0.0.1
	 *
	 * @return array
	 */
	protected function forms()
	{
		$forms = [];
		$all_forms = \Caldera_Forms_Forms::get_forms();
		foreach ( $all_forms as $form ) {
			$forms[] = $this->get_form($form);
		}
		return $forms;
	}

	/**
	 * Get all forms as an array
	 *
	 * @since 0.0.1
	 *
	 * @return array
	 */
	protected function forms_to_array()
	{
		$array = [];
		foreach ( $this->forms() as $form ) {
			$array[] = $form->toArray();
		}
		return $array;
	}


}
