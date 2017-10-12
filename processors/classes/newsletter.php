<?php

/**
 * Base class that newsletter add-ons should use
 *
 * @package   Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
abstract class Caldera_Forms_Processor_Newsletter extends Caldera_Forms_Processor_Processor implements Caldera_Forms_Processor_Interface_Newsletter  {

	/**
	 * API client for newsletter
	 *
	 * @since 1.3.5.3
	 *
	 * @var object
	 */
	protected $client;


	/**
	 * Set the API client
	 *
	 * @since 1.3.5.3
	 */
	public function set_client(){
		//should be declared abstract but PHP5.2
	}


	/**
	 * Validate the process if possible, and if not return errors.
	 *
	 * @since 1.3.5.3
	 *
	 * @param array $config Processor config
	 * @param array $form Form config
	 * @param string $proccesid Unique ID for this instance of the processor
	 *
	 * @return array Return if errors, do not return if not
	 */
	public function pre_processor( array $config, array $form, $proccesid ){
		//should be declared abstract but PHP5.2
	}

	/**
	 * If validate do processing
	 *
	 * @since 1.3.5.3
	 *
	 * @param array $config Processor config
	 * @param array $form Form config
	 * @param string $proccesid Process ID
	 *
	 * @return array Return meta data to save in entry
	 */
	public function processor( array $config, array $form, $proccesid ){
		//should be declared abstract but PHP5.2
	}


}
