<?php
/**
 * Track events in Caldera Forms
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_DB_Track extends Caldera_Forms_DB_Base {

	/**
	 * Primary fields
	 *
	 * @since 1.3.5
	 *
	 * @var array
	 */
	protected $primary_fields = array(
		'form_id'    => array(
			'%s',
			'strip_tags'
		),
		'process_id' => array(
			'%s',
			'strip_tags'
		)

	);

	/**
	 * Meta fields
	 *
	 * @since 1.3.5
	 *
	 * @var array
	 */
	protected $meta_fields = array(
		'event_id'   => array(
			'%d',
			'absint',
		),
		'meta_key'   => array(
			'%s',
			'strip_tags',
		),
		'meta_value' => array(
			'%s',
			'strip_tags',
		),
	);

	/**
	 * Meta keys
	 *
	 * @since 1.3.5
	 *
	 * @var array
	 */
	protected $meta_keys = array(
		'event' => array(
			'%s',
			'strip_tags',
		),
		'time'  => array(
			'%s',
			'strip_tags',
		),
		'recipients_set' => array(
			'%s',
			'strip_tags',
		),
		'referrer' => array(
			'%s',
			'esc_url_raw'
		)
	);

	/**
	 * Name of primary index
	 *
	 * @since 1.3.5
	 *
	 * @var string
	 */
	protected $index = 'event_id';

	/**
	 * Name of table
	 *
	 * @since 1.3.5
	 *
	 * @var string
	 */
	protected $table_name = 'cf_tracking';

	/**
	 * Class instance
	 *
	 * @since 1.3.5
	 *
	 * @var Caldera_Forms_DB_Track
	 */
	private static $instance;

	/**
	 * Setup the actions to track upon
	 *
	 * @since 1.3.5
	 */
	protected function __construct(){
		add_action( 'caldera_forms_submit_start',  array( $this, 'submit_start' ), 50, 2 );
		add_action( 'caldera_forms_submit_complete',  array( $this, 'submit_complete' ), 50, 3 );
		add_action( 'caldera_forms_submit_complete',  array( $this, 'email_tracking' ), 51, 3 );
		add_action( 'caldera_forms_mailer_complete', array( $this, 'email_sent'), 50, 3 );
		add_action( 'caldera_forms_mailer_failed', array( $this, 'email_fail' ), 50, 4 );
		add_action( 'caldera_forms_mailer_invalid', array( $this, 'email_invalid' ), 50 );

	}

	/**
	 * Get class instance
	 *
	 * @since 1.3.5
	 *
	 * @return \Caldera_Forms_DB_Track
	 */
	public static function get_instance(){
		if( null == self::$instance ){
			self::$instance = new self();
		}

		return self::$instance;

	}

	/**
	 * Track that a form started submitting
	 *
	 * @since 1.3.5
	 *
	 * @uses "caldera_forms_submit_start"
	 *
	 * @param array $form Form config
	 * @param string $process_id Form process ID
	 */
	public function submit_start( $form, $process_id ){
		if( isset( $form[ 'ID' ] ) ){
			$recorded = $this->create( array(
				'event' => 'submit_start',
				'form_id' => $form[ 'ID' ],
				'process_id' => $process_id,
				'time' => current_time( 'mysql' )
			));
			
		}
	}

	/**
	 * Track that a form completed submitting
	 *
	 * @since 1.3.5
	 *
	 * @uses "caldera_forms_submit_complete"
	 *
	 * @param array $form Form config
	 * @param array $referrer URL parts for submissions URL
	 * @param string $process_id Form process ID
	 */
	public function submit_complete( $form, $referrer, $process_id ){
		if( isset( $form[ 'ID' ] ) ){
			$recorded = $this->create( array(
				'event' => 'submit_complete',
				'form_id' => $form[ 'ID' ],
				'process_id' => $process_id,
				'time' => current_time( 'mysql' ),
				'referrer' => cf_http_build_url( '', $referrer )
			));
			
		}
	}

	/**
	 * Mark that an email <em>should</em> be sent
	 *
	 * @since 1.3.5
	 *
	 * @uses "caldera_forms_submit_complete"
	 *
	 * @param array $form Form config
	 * @param string $referrer URL referring
	 * @param string $process_id Form process ID
	 */
	public function email_tracking( $form, $referrer, $process_id ){
		if( isset( $form[ 'ID' ],  $form[ 'mailer' ] ) ){
			if( is_array( $form[ 'mailer' ] ) &&  isset( $form[ 'mailer' ][ 'on_insert' ] ) && 1 == $form[ 'mailer' ][ 'on_insert' ] ) {
				$recorded = $this->create( array(
					'event' => 'email_should_send',
					'form_id' => $form[ 'ID' ],
					'process_id' => $process_id,
					'time' => current_time( 'mysql' ),
				));
				


			}
		}
	}

	/**
	 * Track a successful email
	 *
	 * @uses "caldera_forms_mailer_complete"
	 *
	 * @since 1.3.5
	 *
	 * @param array $mail Mailer data
	 * @param array $data Submission data
	 * @param array $form Form config
	 */
	public function email_sent( $mail, $data, $form  ){
		global $process_id;
		if( isset( $form[ 'ID' ] ) ){
			$recorded = $this->create( array(
				'event' => 'email_sent',
				'form_id' => $form[ 'ID' ],
				'process_id' => $process_id,
				'time' => current_time( 'mysql' ),
				'recipients_set' => self::recipients_set( $mail )
			));
			
		}


	}

	/**
	 * Track a failed email
	 *
	 * @uses "caldera_forms_mailer_failed"
	 *
	 * @since 1.3.5
	 *
	 * @param array $mail Mailer data
	 * @param array $data Submission data
	 * @param array $form Form config
	 * @param string $method Send method
	 */
	public function email_fail( $mail, $data, $form, $method  ){
		global $process_id;
		if( isset( $form[ 'ID' ] ) ){
			$this->create( array(
				'event' => 'email_failed',
				'form_id' => $form[ 'ID' ],
				'process_id' => $process_id,
				'time' => current_time( 'mysql' ),
				'method' => strip_tags( $method ),
				'recipients_set' => self::recipients_set( $mail )
			));
			
		}

	}

	/**
	 * Track invalid mailer settings events
	 *
	 * @since 1.4.0
	 *
	 * @param array $form Form config
	 */
	public function email_invalid( $form){
		global $process_id;
		if( isset( $form[ 'ID' ] ) ){
			$this->create( array(
				'event' => 'email_failed',
				'form_id' => $form[ 'ID' ],
				'process_id' => $process_id,
				'time' => current_time( 'mysql' ),
			));

		}
	}

	protected function recipients_set( $mail ){
		if( ! is_array( $mail ) || ! isset( $mail[ 'recipients' ] ) || empty( $mail[ 'recipients' ] ) ){
			return false;
		}

		return true;
	}

	/**
	 * Get tracking data by event name
	 *
	 * @since 1.4.5
	 *
	 * @param string $event Event name
	 * @param bool $return_forms Optional. If true, the default, form IDs are returned. If false, event meta data is returned.
	 *
	 * @return array|null
	 */
	public function by_event( $event, $return_forms = true ){
		$metas = $this->query_meta( 'event', $event );
		if( ! empty( $metas ) ){

			if( $return_forms ){
				$event_ids = array_unique( wp_list_pluck( $metas, 'event_id' ) );
				$forms = $this->form_ids_for_events( $event_ids );
				if( ! empty( $forms ) ){
					return wp_list_pluck( $forms, 'form_id' );
				}

			}else{
				return $metas;
			}


		}

		return array();

	}

	/**
	 * Get form IDs or an array of event IDs
	 *
	 * @since 1.4.5
	 *
	 * @param array $event_ids Event IDs to find form IDs for
	 *
	 * @return array|null
	 */
	protected function form_ids_for_events( $event_ids ){
		global $wpdb;
		$table = $this->get_table_name( false );
		$sql = $wpdb->prepare( "SELECT `form_id` FROM $table WHERE `ID` IN( '%s' )", implode( ',', $event_ids ) );
		return $wpdb->get_results( $sql, ARRAY_A );
	}



}
