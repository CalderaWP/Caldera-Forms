<?php


namespace calderawp\calderaforms\pro;

use calderawp\calderaforms\pro\api\client;
use calderawp\calderaforms\pro\api\log;


/**
 * Class container
 *
 * Contains "main" instances of all reusable classes
 *
 * @package calderawp\calderaforms\pro
 */
class container extends repository
{


	/**
	 * Holds main instance
	 *
	 * @since 0.0.1
	 *
	 * @var container
	 */
	protected static $instance;


	/**
	 * Holds main instance
	 *
	 * @since 0.0.1
	 *
	 * @return container
	 */
	public static function get_instance()
	{
		if ( !self::$instance ) {
			self::$instance = new self();

		}

		return self::$instance;
	}

	/**
	 * Get the messages DB abstraction
	 *
	 * @since 0.0.1
	 *
	 * @return messages
	 */
	public function get_messages_db()
	{
		if ( !$this->has('db') ) {
			global $wpdb;
			$table_name = $wpdb->prefix . 'cf_pro_messages';
			$this->set(
				'db',
				new messages($wpdb, $table_name)
			);
		}
		return $this->get('db');
	}

	/**
	 * Get the main settings object
	 *
	 * @since 0.0.1
	 *
	 * @return settings
	 */
	public function get_settings()
	{
		if ( !$this->has('settings') ) {

			$this->set(

				'settings',
				settings::from_saved()
			);
		}
		return $this->get('settings');
	}

	/**
	 * Get hooks class
	 *
	 * @since 0.0.1
	 *
	 * @return hooks
	 */
	public function get_hooks()
	{
		if ( !$this->get('hooks') ) {
			$this->set(
				'hooks',
				new hooks()
			);
		}

		return $this->get('hooks');
	}

	/**
	 * Get remote logger
	 *
	 * @since 0.2.0
	 *
	 * @return log
	 */
	public function get_logger()
	{
		if ( !$this->get('logger') ) {
			$this->set(
				'logger',
				new log($this->get_settings()->get_api_keys())
			);
		}

		return $this->get('logger');

	}

	/**
	 * Get API client
	 *
	 * @since 0.10.0
	 *
	 * @return client
	 */
	public function get_api_client()
	{
		if ( !$this->get('api_client') ) {
			$this->set(
				'api_client',
				new client(container::get_instance()->get_settings()->get_api_keys())
			);
		}

		return $this->get('api_client');

	}

	/**
	 * Get the HTML to be used in Caldera Forms Pro tab
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_tab_html()
	{
		if ( !$this->has('tab_html') ) {
			return '';
		}
		return $this->get('tab_html');
	}

	/**
	 * Set the HTML to be used in Caldera Forms Pro tab
	 *
	 * @since 1.0.0
	 *
	 * @param $html
	 */
	public function set_tab_html($html)
	{
		$this->set('tab_html', $html);
	}

	/**
	 * Set main instance of Caldera_Forms_DB_Tables class
	 *
	 * @since 0.5.0
	 *
	 * @param \Caldera_Forms_DB_Tables $DB_Tables
	 */
	public function set_tables(\Caldera_Forms_DB_Tables $DB_Tables)
	{
		$this->set('tables', $DB_Tables);
	}

	/**
	 * Get main instance of Caldera_Forms_DB_Tables class
	 *
	 * @since 0.5.0
	 *
	 * @return \Caldera_Forms_DB_Tables
	 */
	public function get_tables()
	{
		return $this->get('tables');
	}

	/**
	 * Set anti-spam helper object in container
	 *
	 * @since 1.6.0
	 *
	 * @param array $args
	 */
	public function set_anti_spam_args(array $args)
	{
		$this->set('anti_spam_args', $args);
	}

	/**
	 * Get anti-spam args from container
	 *
	 * @since 1.6.0
	 *
	 * @return array
	 */
	public function get_anti_spam_args()
	{
		return is_array($this->get('anti_spam_args')) ? $this->get('anti_spam_args') : [];
	}


}
