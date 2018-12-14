<?php

/**
 * Get the cf2 container
 *
 * @since 1.8.0
 *
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

/**
 * Setup Cf2 container
 *
 * @since 1.8.0
 *
 * @uses "caldera_forms_v2_init" action
 *
 * @param \calderawp\calderaforms\cf2\CalderaFormsV2Contract $container
 */
function caldera_forms_v2_container_setup(\calderawp\calderaforms\cf2\CalderaFormsV2Contract $container)
{
	$container
		//Set paths
		->setCoreDir(CFCORE_PATH)
		->setCoreUrl(CFCORE_URL)
		//Setup field types
		->getFieldTypeFactory()
		->add(new \calderawp\calderaforms\cf2\Fields\FieldTypes\FileFieldType());

	//Add hooks
	$container->getHooks()->subscribe();

	//Register other services
	$container
		->registerService(new \calderawp\calderaforms\cf2\Services\QueueService(), true)
		->registerService(new \calderawp\calderaforms\cf2\Services\QueueSchedulerService(), true);

	//Run the scheduler with CRON
	/** @var \calderawp\calderaforms\cf2\Jobs\Scheduler $scheduler */
	$scheduler = $container->getService(\calderawp\calderaforms\cf2\Services\QueueSchedulerService::class);
	$running = $scheduler->runWithCron();
}

/**
 * Convert Advanced File v1 fields to v2
 *
 * @since 1.8.0
 *
 * @uses "caldera_forms_render_get_field" filter
 *
 * @param array $field
 * @param array $form
 *
 * @return array
 */
function caldera_forms_v2_field_upgrades($field, $form){
	if ( 'advanced_file' === Caldera_Forms_Field_Util::get_type($field, $form) ) {
		$field[ 'type' ] = \calderawp\calderaforms\cf2\Fields\FieldTypes\FileFieldType::getCf1Identifier();
	}
	return $field;
}
