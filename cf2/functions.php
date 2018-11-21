<?php

/**
 * @return \calderawp\calderaforms\cf2\CalderaFormsV2Contract
 */
function caldera_forms_get_v2_container(){

	static $container;
	if( ! $container ){
		$container = new \calderawp\calderaforms\cf2\CalderaFormsV2();
		do_action( 'caldera_forms_v2_init', $container );
	}

	return $container;
}