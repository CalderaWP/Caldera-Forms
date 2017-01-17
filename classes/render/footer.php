<?php
/**
 * System for printing extra scripts, etc in footer, per form
 *
 * Should be used through Caldera_Forms_Render_Util::add_inline_data() which acts as factory, container for these objects, by form ID and a way to add to tracked objects.
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_Render_Footer {

	/**
	 * Form config
	 *
	 * @since 1.5.0
	 *
	 * @var array
	 */
	protected $form;

	/**
	 *
	 * @since 1.5.0
	 *
	 * @var array
	 */
	protected $data;

	/**
	 * Tracks if output has occurred yet.
	 *
	 * @since 1.5.0
	 *
	 * @var bool
	 */
	protected $printed;

	/**
	 * Caldera_Forms_Render_Footer constructor.
	 *
	 * @since 1.5.0
	 *
	 * @param array $form
	 */
	public function __construct( array $form ) {
		$this->form = $form;
		$this->data = array();
		$this->printed = false;
		if ( ! is_admin() ) {
			add_action( 'wp_footer', array( $this, 'print_data' ), 1000 );
		} else {
			add_action( 'admin_footer', array(  $this, 'print_data' ), 1000 );
		}

	}

	/**
	 * Remove hooks for this object
	 *
	 * @since 1.5.0
	 */
	public function remove_hooks(){
		if ( ! is_admin() ) {
			remove_action( 'wp_footer', array( $this, 'print_data' ), 1000 );
		} else {
			remove_action( 'admin_footer', array(  $this, 'print_data' ), 1000 );
		}
	}

	/**
	 * Add a string to be printed
	 *
	 * @since 1.5.0
	 *
	 * @param string $data Content to print
	 *
	 * @return  bool True if added, false if not
	 */
	public function add_data( $data ){
		if( false == $this->printed && is_string( $data ) ){
			$this->data[] = $data;
			return true;
		}

		return false;
	}

	/**
	 * Print data
	 *
	 * @uses "wp_footer" or "admin_footer" action
	 *
	 * @since 1.5.0
	 */
	public function print_data(){
		if( ! empty( $this->data ) )  {
			echo implode( "\n", $this->data );
		}
		$this->printed = true;
	}
}