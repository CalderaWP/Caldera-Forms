<?php


namespace calderawp\calderaforms\pro;

use calderawp\calderaforms\pro\exceptions\Exception;
use calderawp\calderaforms\pro\exceptions\factory;


/**
 * Class messages
 *
 * CRUD for local DB records of CF Pro messages
 *
 * @package calderawp\calderaforms\pro
 */
class messages
{

	/***
	 * @var \wpdb
	 */
	protected $wpdb;

	/**
	 * The name of table we are using to store
	 *
	 * @var string
	 */
	protected $table_name;

	/**
	 * Message types
	 *
	 * @var array
	 */
	protected $types = [
		'main',
		'auto',
	];

	/**
	 * messages constructor.
	 *
	 * @param \wpdb $wpdb WPDB object
	 * @param string $table_name The name of table we are using to store -- SHOULD BE PREFIXED
	 */
	public function __construct(\wpdb $wpdb, $table_name)
	{
		$this->wpdb = $wpdb;
		$this->table_name = $table_name;
	}


	/**
	 * Store message record
	 *
	 * @since 0.0.1
	 *
	 * @param int $cfp_id Message ID from app
	 * @param string $hash Message hash
	 * @param int $entry_id Entry ID
	 * @param string $type The message type main|auto
	 *
	 * @return message
	 * @throws Exception
	 */
	public function create($cfp_id, $hash, $entry_id = 0, $type = 'main')
	{
		$type = $this->validate_type($type);
		$data = [
			'cfp_id' => $cfp_id,
			'hash' => $hash,
			'entry_id' => $entry_id,
			'type' => $type,
		];
		$this->wpdb->insert($this->table_name, $data, [
			'%d',
			'%s',
			'%d',
			'%s',
		]);

		if ( is_numeric($this->wpdb->insert_id) ) {
			$data[ 'local_id' ] = $this->wpdb->insert_id;
			return message::from_array($data);
		}

		throw New Exception(__('Could not store message', 'caldera-forms'));

	}

	/**
	 * Validate message type
	 *
	 * @since 0.0.1
	 *
	 * @param string $type The message type
	 *
	 * @return string
	 */
	protected function validate_type($type)
	{
		if ( !in_array($type, $this->types) ) {
			return 'main';
		}

		return $type;
	}


	/**
	 * Find by CF Pro app message ID
	 *
	 * @since 0.0.1
	 *
	 * @param int $id Message ID from app
	 *
	 * @return message|\WP_Error
	 */
	public function get_by_remote_id($id)
	{
		try {
			$message = $this->get_by('cfp_id', $id);
			return $message;
		} catch ( Exception $e ) {
			return $e->log([
				'id' => $id,
				'method' => __METHOD__,
			])->to_wp_error();
		}
	}

	/**
	 * Find by local (database table) ID
	 *
	 * @since 0.0.1
	 *
	 * @param int $id Message ID in local db
	 *
	 * @return message|\WP_Error
	 */
	public function get_by_local_id($id)
	{
		try {
			$message = $this->get_by('ID', $id);
			return $message;
		} catch ( Exception $e ) {
			return $e->log([
				'id' => $id,
				'method' => __METHOD__,
			])->to_wp_error();
		}
	}

	/**
	 * Find by entry ID
	 *
	 * @since 0.0.1
	 *
	 * @param int $id
	 * @param string $type Optional The message type. Default is 'main'
	 *
	 * @return message|\WP_Error
	 */
	public function get_by_entry_id($id, $type = 'main')
	{
		try {
			$message = $this->get_by('entry_id', $id, $type);
			return $message;
		} catch ( Exception $e ) {
			return $e->log([
				'id' => $id,
				'type' => $type,
				'method' => __METHOD__,
			])->to_wp_error();
		}
	}


	/**
	 * Search for saved message by value and optionally type
	 *
	 * @since 0.0.1
	 *
	 * @param string $field field to search by
	 * @param int $value Value to search for cfp_id|ID|entry_id
	 * @param null|string Optional. Message type to search by
	 *
	 * @return message
	 * @throws Exception
	 */
	protected function get_by($field, $value, $type = null)
	{
		$table = $this->table_name;
		$sql = sprintf("SELECT * FROM $table WHERE `%s` = %d", $field, absint($value));
		if ( $type ) {
			$sql .= sprintf(" AND `type` = '%s'", $this->validate_type($type));
		}
		$results = $this->wpdb->get_results($sql, ARRAY_A);
		if ( $results ) {
			return message::from_array($results);
		}

		throw new Exception(__('Could not find message.', 'caldera-forms'));

	}

	/**
	 * Delete a saved entry
	 *
	 * @since 0.0.1
	 *
	 * @param string $field field to search by
	 * @param int $value Value to search for cfp_id|ID|entry_id
	 *
	 * @return false|int
	 */
	public function delete($field, $value)
	{
		return $this->wpdb->delete($this->table_name, [
			$field => $value,
		]);

	}

}
