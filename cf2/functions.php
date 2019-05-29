<?php

/**
 * Get the cf2 container
 *
 * @since 1.8.0
 *
 * @return \calderawp\calderaforms\cf2\CalderaFormsV2Contract
 */
function caldera_forms_get_v2_container()
{

	static $container;
	if ( !$container ) {
		$container = new \calderawp\calderaforms\cf2\CalderaFormsV2();
		do_action('caldera_forms_v2_init', $container);
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
 * Schedule delete with job manager
 *
 * @since 1.8.0
 *
 * @param \calderawp\calderaforms\cf2\Jobs\Job $job Job to schedule
 * @param int $delay Optional. Minimum delay before job is run. Default is 0.
 */
function caldera_forms_schedule_job(\calderawp\calderaforms\cf2\Jobs\Job $job, $delay = 0)
{

	caldera_forms_get_v2_container()
		->getService(\calderawp\calderaforms\cf2\Services\QueueSchedulerService::class)
		->schedule($job, $delay);
}




/**
 * Attempt to load a file at the specified path and parse its contents as JSON.
 *
 * @param string $path The path to the JSON file to load.
 * @return array|null;
 */
function caldera_forms_load_asset_file( $path ) {
    if ( ! file_exists( $path ) ) {
        return null;
    }
    $contents = file_get_contents( $path );
    if ( empty( $contents ) ) {
        return null;
    }
    return json_decode( $contents, true );
}

/**
 * Check a directory for a root or build asset manifest file, and attempt to
 * decode and return the asset list JSON if found.
 *
 * @param string $directory Root directory containing `src` and `build` directory.
 * @return array|null;
 */
function caldera_forms_get_assets_list(  $manifest_path ) {
    $dev_assets = caldera_forms_load_asset_file($manifest_path);
    var_dump($dev_assets);exit;
    if ( ! empty( $dev_assets ) ) {
        return array_values( $dev_assets );
    }

    return null;
}


function caldera_forms_enqueue_assets( $manifest_path, $opts = [] ) {
    $defaults = [
        'handle'  => basename( plugin_dir_path( $manifest_path ) ),
        'filter'  => '__return_true',
        'scripts' => [],
        'styles'  => [],
    ];

    $opts = wp_parse_args( $opts, $defaults );

    $assets =caldera_forms_get_assets_list($manifest_path);
var_dump($assets);exit;
    if ( empty( $assets ) ) {
        // Trust the theme or pluign to handle its own asset loading.
        return false;
    }

    // Keep track of whether a CSS file has been encountered.
    $has_css = false;

    // There should only be one JS and one CSS file emitted per plugin or theme.
    foreach ( $assets as $asset_uri ) {
        if ( $opts['filter'] && ! $opts['filter']( $asset_uri ) ) {
            // Ignore file paths which do not pass the provided filter test.
            continue;
        }

        $is_js    = preg_match( '/\.js$/', $asset_uri );
        $is_css   = preg_match( '/\.css$/', $asset_uri );
        $is_chunk = preg_match( '/\.chunk\./', $asset_uri );

        if ( ( ! $is_js && ! $is_css ) || $is_chunk ) {
            // Assets such as source maps and images are also listed; ignore these.
            continue;
        }

        //Upgrade to HTTPS
        if( is_ssl() ){
            $asset_uri = preg_replace("/^http:/i", "https:", $asset_uri);
        }

        $split = explode( '/', $asset_uri);
        $name = end($split);
        if ( $is_js && 'editor.js' === $name) {
            wp_enqueue_script(
                $opts['handle'],
                $asset_uri,
                $opts['scripts'],
                filemtime( $manifest_path ),
                true
            );
        } elseif ( $is_css ) {
            $has_css = true;
            wp_enqueue_style(
                $opts['handle'],
                $asset_uri,
                $opts['styles'],
                filemtime( $manifest_path )
            );
        }
    }

    // Ensure CSS dependencies are always loaded, even when using CSS-in-JS in
    // development.
    if ( ! $has_css && ! empty( $opts['styles'] ) ) {
        wp_register_style(
            $opts['handle'],
            null,
            $opts['styles']
        );
        wp_enqueue_style( $opts['handle'] );
    }

    // Signal that auto-loading occurred.
    return true;
}
