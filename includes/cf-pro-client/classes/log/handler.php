<?php


namespace calderawp\calderaforms\pro\log;
use calderawp\calderaforms\pro\container;
use Monolog\Handler\AbstractHandler;
use Monolog\Logger as Monolog;


/**
 * Class handler - Monolog handler for CF Pro remote logging
 * @package calderawp\calderaforms\pro\log
 */
class handler extends  AbstractHandler {

	/**
	 * Recent messages
	 *
	 * @since 0.11.0
	 *
	 * @var  array
	 */
	protected $recents;

	/**
	 * Name of CF transient for tracking recents
	 *
	 * @since 0.11.0
	 *
	 * @var string
	 */
	protected $tracking_key;

	/**
	 * {@inheritdoc}
	 * @since 0.5.0
	 */
	public function handle( array $record ) {
		$level = isset( $record[ 'level_name' ] ) ? $record[ 'level_name' ] : Monolog::NOTICE;

		if( ! $this->high_enough_level( $level ) ){
			return;
		}

		$message = isset( $record[ 'message' ] ) ? $record[ 'message' ] : '' ;

		if( empty( $message ) || $this->is_recent_repeat( $message ) ){
			return;
		}

		$prepared = $this->prepare( $record );

		container::get_instance()->get_logger()->send(
			$message,
			$prepared,
			$level
		);

	}

    /**
     * Find if a log level is high enough to report according to settings and filters.
     *
     * @since 1.5.9
     *
     * @param string $level The level
     * @return bool
     */
	protected function high_enough_level( $level ){
		$levels = Monolog::getLevels();
		if (  array_key_exists( $level, $levels )  ) {
			$level = array_search( $level, array_flip( $levels ) );
		}else{
			return true;
		}

		/**
		 * Change the minimum level for a message to be logged at
		 *
		 * @since 0.11.0
		 *
		 */
		$highest = apply_filters( 'caldera_forms_pro_minimum_level', container::get_instance()->get_settings()->get_log_level() );
		if( ! is_numeric( $highest ) && array_key_exists( $highest, $levels ) ){
			$highest = array_search( $highest, array_flip( $levels ) );

		}
		if( $highest > $level ){
			return false;
		}

		return true;
	}

	/**
	 * Prepare data to be sent to remote API
	 *
	 * @since 0.5.0
	 *
	 * @param array $record
	 *
	 * @return array
	 */
	protected function prepare( array $record ){
		$prepared = [];

		$prepared[ 'location' ] = [
			'file' => isset( $record[ 'context' ][ 'file' ] ) ?  $record[ 'context' ][ 'file' ] : '',
			'line' => isset( $record[ 'context' ][ 'line' ] ) ? $record[ 'context' ][ 'line' ] : '',

		];

		foreach ( array(
			'array',
			'property',
			'cb',
		) as $thing ){
			if( isset( $record[ 'context' ][ $thing ] ) ){
				$prepared[ $thing ] =  $record[ 'context' ][ $thing ] ;
			}

		}

		if( isset( $record[ 'extra' ] ) ){
			$prepared = array_merge( $prepared, $record[ 'extra' ] );
		}

		if( method_exists( 'Caldera_Forms_DB_Tables', 'get_missing_tables' ) && is_object( container::get_instance()->get_tables() ) ){
			$prepared[ 'missing_tables' ] = container::get_instance()->get_tables()->get_missing_tables();
		}

		return $prepared;



	}

	/**
	 * Check if this is a recent message
	 *
	 * @since 0.11.0
	 *
	 * @param string $message Message to test
	 *
	 * @return bool
	 */
	protected function is_recent_repeat( $message ){
		if( empty( $this->tracking_key ) ){
			$this->tracking_key = caldera_forms_pro_log_tracker_key( CF_PRO_VER );
		}

		$hash = md5( $message );
		if( in_array( $hash, $this->get_recent() ) ){
			return true;
		}

		$max = 10;
		if ( 2 < count( $this->recents ) ) {
			$this->recents = array_splice( $this->recents, 0 - $max  );
		}

		$this->recents[] = $hash;
		\Caldera_Forms_Transient::set_transient( $this->tracking_key, $this->recents  );
		return false;

	}

	/**
	 * Get array of recently tracked messages
	 *
	 * @since 0.11.0
	 *
	 * @return array
	 */
	protected function get_recent(){
		if( empty( $this->recents ) ){
			$this->recents = \Caldera_Forms_Transient::get_transient( $this->tracking_key );

		}

		if( empty( $this->recents ) ){
			$this->recents = [];
		}

		return array_unique(  $this->recents );
	}




}