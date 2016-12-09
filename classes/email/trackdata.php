<?php

/**
 * Get email stats
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_Email_TrackData {

	/**
	 * Is stats array ready?
	 *
	 * @since 1.4.5
	 *
	 * @var bool
	 */
	protected $ready = false;

	/**
	 * Stats array
	 *
	 * @since 1.4.5
	 *
	 * @var array
	 */
	protected $stats = array(
		'sent' => 0,
		'failed' => 0,
		'total' => 0,
		'success_rate' => 0
	);

	/**
	 * Caldera_Forms_Email_TrackData constructor.
	 *
	 * @since 1.4.5
	 */
	public function __construct() {
		$_stats = get_transient( __CLASS__ );
		if( ! empty( $_stats ) ){
			$this->prepare_stats();
			set_transient( __CLASS__, $_stats, 599 );
			$this->stats = $_stats;
			$this->ready = true;
		}

	}

	/**
	 * Get the total sent successfully
	 *
	 * Note: Bypasses cache
	 *
	 * @since 1.4.5
	 *
	 * @return int
	 */
	public function success(){
		$events = Caldera_Forms_DB_Track::get_instance()->by_event( 'email_sent', false );
		$this->stats[ 'sent' ] = count( $events );
		return $this->stats[ 'sent' ];

	}

	/**
	 * Get the total fails
	 *
	 * Note: Bypasses cache
	 *
	 * @since 1.4.5
	 *
	 * @return int
	 */
	public function failed(){
		$events = Caldera_Forms_DB_Track::get_instance()->by_event( 'email_failed', false );
		$this->stats[ 'failed' ] = count( $events );
		return $this->stats[ 'failed' ];
	}

	/**
	 * Get all stats
	 *
	 * @since 1.4.5
	 *
	 * @param bool $bypass_cache Optional. If true, data is recalculated even if cached data is present. Default is false.
	 *
	 * @return array|mixed
	 */
	public function get_stats( $bypass_cache = false ){
		if ( false == $this->ready || $bypass_cache ) {
			$this->prepare_stats();
		}

		return $this->stats;
	}

	/**
	 * Prepare stats array with new queries
	 *
	 * @since 1.4.5
	 */
	protected function prepare_stats() {
		if ( empty( $this->stats[ 'sent' ] ) ) {
			$this->success();
		}
		if ( empty( $this->stats[ 'failed' ] ) ) {
			$this->failed();
		}

		$this->stats[ 'success_rate' ] = 100;

		if ( 0 != $this->stats[ 'failed' ] ) {
			$total = $this->stats[ 'total' ] = $this->stats[ 'sent' ] + $this->stats[ 'failed' ];
			if ( 0 != $total && 0 != $this->stats[ 'sent' ] ) {
				$this->stats[ 'success_rate' ] = round( $this->stats[ 'sent' ] / $total, 2 );
			}
		}

		set_transient( __CLASS__, $this->stats, 599 );

		$this->ready = true;
	}

}