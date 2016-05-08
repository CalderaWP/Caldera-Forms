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
	 * @since 1.3.6
	 *
	 * @var object
	 */
	protected $client;


}
