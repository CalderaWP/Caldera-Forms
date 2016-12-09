<?php
/**
 * Handles optin-based sharing of usage tracking
 *
 * @package   Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_Tracking {

	/**
	 * URL of tracking API
	 *
	 * @since 1.3.5
	 *
	 * @var string
	 */
	protected static $api_url = 'https://asimov.caldera.space/cf-tracking/v3';

	/**
	 * Option key for tracking last row sent
	 *
	 * @var string
	 */
	protected static $row_tracking_key = '_caldera_forms_tracking_last_row';

	/**
	 * Class instance
	 *
	 * @since 1.3.5
	 *
	 * @var \Caldera_Forms_Tracking
	 */
	private static $instance;

	/**
	 * Setup actions
	 *
	 * @since 1.3.5
	 *
	 */
	protected function __construct(){
		$enabled = self::tracking_allowed();
		if ( $enabled ) {
			add_action( 'init', array( 'Caldera_Forms_DB_Track', 'get_instance' ), 1 );
		}

		add_action( 'caldera_forms_tracking_send_rows', array( __CLASS__, 'send_rows' ) );
	}

	/**
	 * Get class instance
	 *
	 * @return \Caldera_Forms_Tracking
	 */
	public static function get_instance(){
		if( null == self::$instance ){
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Get the status of the usage tracking
	 *
	 * @since 1.3.5
	 *
	 * @return string|int
	 */
	public static function tracking_optin_status(){
		return get_option( '_caldera_forms_tracking_allowed', 0 );
	}

	/**
	 * Is data tracking allowed?
	 *
	 * @since 1.3.5
	 *
	 * @return bool
	 */
	public static function tracking_allowed(){
		return 1 <= self::tracking_optin_status();
	}

	/**
	 * Send tracking rows if allowed
	 *
	 * @since 1.3.5
	 */
	public static function send_rows( ){

		if( false == self::tracking_allowed() ){
			return;
		}

		$last_row = self::get_last_sent_row();
		include_once CFCORE_PATH . 'classes/db/track.php';
		$highest = Caldera_Forms_DB_Track::get_instance()->highest_id();

		if( $highest <= $last_row ){
			return;
		}

		$partial = false;
		if( $highest - $last_row > 1000 ){
			$partial = true;
			$highest = $last_row - $highest;

		}

		$rows = self::prepare_rows_to_send( $last_row, $highest );
		$body = array(
			'rows' => $rows,
			'plugins' => Caldera_Forms_Support::get_plugins()
		);

		$body = array_merge( $body, self::site_data() );

		$sent = self::send_to_api( self::$api_url . '/tracking', 'POST', $body );
		if( ! is_numeric( $sent  ) ){
			end( $rows );
			$key = key( $rows );
			update_option( self::$row_tracking_key, intval( $rows[ $key ] ) );
		}

		if(  $partial ){
			wp_schedule_single_event( time() + 59,  'caldera_forms_tracking_send_rows' );
		}

	}

	/**
	 * Get last row sent
	 *
	 * @since 1.3.5
	 *
	 * @return int
	 */
	protected static function get_last_sent_row(){
		return get_option( self::$row_tracking_key, 0 );
	}

	/**
	 * Send to API
	 *
	 * @since 1.3.5
	 *
	 * @param string $url URL to request
	 * @param string $method Optional. Transport method. Default is 'GET'
	 * @param array $body Required for POST, not used for GET Body for POST request. Will be JSON encoded.
	 *
	 * @return array|int|string Returned data or response code
	 */
	public static function send_to_api( $url, $method = 'GET', $body = array() ){
		$method = strtoupper( $method );
		$args = array(
			'method' => $method,
		);
		if( 'POST' == $method ) {
			if( empty( $body ) ){
				return;
			}

			$args[ 'body' ] = json_encode( $body );
		}

		$r = wp_remote_request( $url,  $args  );
		if( ! is_wp_error( $r ) && 200 == wp_remote_retrieve_response_code( $r ) ) {
			$body = json_decode( wp_remote_retrieve_body( $r ) );

			return $body;
		}

		return wp_remote_retrieve_response_code( $r );

	}

	/**
	 * Get the API url with all of the correct query vars and such
	 *
	 * @since 1.3.5
	 *
	 * @param string $endpoint Endpoint to add -- DON'T PREFIX WITH SLASH PLEASE!
	 *
	 * @return string
	 */
	public static function api_url( $endpoint ){

		$url = trailingslashit( self::$api_url ) . $endpoint;
		$url = add_query_arg( self::site_data(), $url );

		return $url;
	}

	/**
	 * Site data for API
	 *
	 * @since 1.4.4
	 *
	 * @return array
	 */
	protected static function site_data(){
		global $wp_version;
		$tracking_optin = intval( self::tracking_optin_status() );
		$data = array( 'wp' => urlencode( $wp_version ), 'php' => urlencode( PHP_VERSION ), 'db' => get_option( 'CF_DB', 0 ), 'url' => urlencode( site_url() ), 'tracking_optin' => $tracking_optin  );
		if( self::tracking_allowed() ){
			$data[ 'email' ] = urlencode( get_option( 'admin_email' ) );
		}else{
			$data[ 'email' ] = 0;
		}

		return $data;
	}

	/**
	 * Prepare rows to send to remote API
	 *
	 * @since 1.3.5
	 *
	 * @param $last_row
     *
	 * @param $highest
	 *
	 * @return array|null
	 */
	protected static function prepare_rows_to_send( $last_row, $highest ) {
		$ids[] = $last_row + 1;

		for ( $i = 2; $i <= $highest; $i ++ ) {
			$ids[] = $last_row + $i;
		}

		$rows = Caldera_Forms_DB_Track::get_instance()->get_record( $ids );
		if ( ! empty( $rows ) ) {
			$fields    = Caldera_Forms_DB_Track::get_instance()->get_fields();
			$meta_keys = $fields[ 'meta_keys' ];
			foreach ( $rows as $i => $row ) {
				$rows[ $i ][ 'event_id' ] = $row[ 'ID' ];
				unset( $rows[ $i ][ 'ID' ] );
				$rows[ $i ][ 'meta' ] = array();
				foreach ( $meta_keys as $meta_key ) {
					if ( isset( $row[ $meta_key ] ) ) {
						$rows[ $i ][ 'meta' ][ $meta_key ] = $row[ $meta_key ];

					}
					unset( $rows[ $i ][ $meta_key ] );
				}
			}

			return $rows;
		}

		return $rows;
	}

	/**
	 * Get tracking optin url
	 *
	 * @since 1.4.5
	 *
	 * @return string
	 */
	protected static function optin_url(){
		$url = add_query_arg( array(
			'page' => 'caldera-forms',
			'cal_tracking_nonce' => wp_create_nonce(),
		), self_admin_url( 'admin.php' ) );

		return $url;
	}

	/**
	 * Get the allow tracking URL
	 *
	 * @since 1.4.5
	 *
	 * @return string
	 */
	public static function allow_url(){
		return add_query_arg( array( 'cal_tracking' => 1 ), self::optin_url() );
	}

	/**
	 * Get the dismiss tracking URL
	 *
	 * @since 1.4.5
	 *
	 * @return string
	 */
	public static function dismiss(){
		return add_query_arg( array( 'cal_tracking' =>'dismiss' ), self::optin_url()  );
	}
}
