<?php

/**
 * Base class that form processor add-ons should use
 *
 * @package   Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
abstract class Caldera_Forms_Processor_Processor implements Caldera_Forms_Processor_Interface_Process {

	/**
	 * Processor slug
	 *
	 * @since 1.3.5.3
	 *
	 * @var string
	 */
	protected $slug;

	/**
	 * Processor configuration
	 *
	 * @since 1.3.5.3
	 *
	 * @var string
	 */
	protected $processor_config;

	/**
	 * Process data object
	 *
	 * @since 1.3.5.3
	 *
	 * @var Caldera_Forms_Processor_Get_Data
	 */
	protected $data_object;

	/**
	 * Processor field config
	 *
	 * @since 1.3.5.3
	 *
	 * @var array
	 */
	protected $fields;


	/**
	 * Construct object for processing object
	 *
	 * @since 1.3.5.3
	 *
	 * @param array $processor_config Processor configuration
	 * @param  array $fields Field config
	 * @param string $slug Processor slug
	 */
	public function __construct( array $processor_config, array $fields, $slug ){
		$this->slug = $slug;
		$this->fields = $fields;
		$this->set_processor_config( $processor_config );
		
		add_filter( 'caldera_forms_get_form_processors', array( $this, 'register_processor' ) );
	}

	/**
	 * Get configuration for processor fields.
	 *
	 * @since 1.3.5.3
	 *
	 * @return array
	 */
	public function fields() {
		/**
		 * Filter configuration for processor fields.
		 *
		 * @since 1.3.5.3
		 *
		 * @param array $fields The fields
		 * @param string $slug The slug
		 */
		return apply_filters(  'caldera_forms_' . $this->slug . '_fields',  $this->fields, $this->slug );
	}

	/**
	 * Set processor_config property
	 *
	 * @since 1.3.5.3
	 *
	 * @param array $processor_config Processor configuration
	 */
	protected function set_processor_config( $processor_config ){
		$this->processor_config = wp_parse_args( $processor_config, array(
			'pre_processor' => array( $this, 'pre_processor' ),
			'processor' => array( $this, 'processor' )
		));

		if( isset( $processor_config[ 'post_processor' ] ) ){
			if( method_exists( $this, $processor_config[ 'post_processor' ] ) ){
				$this->processor_config[ 'post_processor' ] = array( $this, $processor_config[ 'post_processor' ] );
			}
		}
	}

	/**
	 * Registers the Caldera Forms Processor
	 *
	 * @uses "caldera_forms_get_form_processors" filter
	 *
	 * @since 1.3.5.3
	 *
	 * @param array		$processors		Array of current registered processors
	 *
	 * @return array	Array of registered processors
	 */
	public function register_processor( $processors ){
		$processors[ $this->slug ] = $this->processor_config;
		return $processors;
	}

	/**
	 * Set data_object property the first time
	 *
	 * Use during pre_process
	 *
	 * @since 1.3.5.3
	 *
	 * @param array $config Processor config
	 * @param array $form Form config
	 */
	protected function set_data_object_initial( array $config, array $form ){
		$this->data_object = new Caldera_Forms_Processor_Get_Data( $config, $form, $this->fields() );

	}

	/**
	 * Set data_object property from transdata global
	 *
	 * Use at process or later
	 *
	 * @since 1.3.5.3
	 *
	 * @param  string $process_id Process ID
	 */
	protected function set_data_object_from_transdata( $process_id ){
		global $transdata;
		if( isset( $transdata[ $process_id ] ) && isset( $transdata[ $process_id ][ 'object' ] ) ){
			$this->data_object = $transdata[ $process_id ][ 'object' ];
		}
	}

	/**
	 * Put processor values and object into transdata global for later use
	 *
	 * @since 1.3.5.3
	 *
	 * @param  string $process_id Process ID
	 */
	protected function setup_transata( $process_id ){
		global $transdata;
		$transdata[ $process_id ] = $this->data_object->get_values();
		$transdata[ $process_id ][ 'object' ] = $this->data_object;
	}


}
