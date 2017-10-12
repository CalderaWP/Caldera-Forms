<?php

/**
 * Interface that payment processor add-ons should implement
 *
 * @package   Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
interface Caldera_Forms_Processor_Interface_Payment {
	/**
	 * Do Payment
	 *
	 * @since 1.3.5.3
	 *
	 * @param array $config Processor config
	 * @param array $form Form config
	 * @param string $proccesid Unique ID for this instance of the processor
	 * @param Caldera_Forms_Processor_Get_Data $data_object Processor data
	 *
	 * @return Caldera_Forms_Processor_Get_Data
	 */
	public function do_payment( array $config, array $form, $proccesid, Caldera_Forms_Processor_Get_Data $data_object );
}
