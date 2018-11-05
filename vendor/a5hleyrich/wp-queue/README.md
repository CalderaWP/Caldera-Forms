[![Build Status](https://travis-ci.org/A5hleyRich/wp-queue.svg?branch=master)](https://travis-ci.org/A5hleyRich/wp-queue)
[![Code Coverage](https://scrutinizer-ci.com/g/A5hleyRich/wp-queue/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/A5hleyRich/wp-queue/?branch=master)
[![Total Downloads](https://poser.pugx.org/a5hleyrich/wp-queue/downloads)](https://packagist.org/packages/a5hleyrich/wp-queue)
[![Latest Stable Version](https://poser.pugx.org/a5hleyrich/wp-queue/v/stable)](https://packagist.org/packages/a5hleyrich/wp-queue)
[![License](https://poser.pugx.org/a5hleyrich/wp-queue/license)](https://packagist.org/packages/a5hleyrich/wp-queue)

## Prerequisites

WP_Queue requires PHP __5.3+__.

The following database tables need to be created:

```
CREATE TABLE {$wpdb->prefix}queue_jobs (
id bigint(20) NOT NULL AUTO_INCREMENT,
job longtext NOT NULL,
attempts tinyint(3) NOT NULL DEFAULT 0,
reserved_at datetime DEFAULT NULL,
available_at datetime NOT NULL,
created_at datetime NOT NULL,
PRIMARY KEY  (id)
```

```
CREATE TABLE {$wpdb->prefix}queue_failures (
id bigint(20) NOT NULL AUTO_INCREMENT,
job longtext NOT NULL,
error text DEFAULT NULL,
failed_at datetime NOT NULL,
PRIMARY KEY  (id)
```

Alternatively, you can call the `wp_queue_install_tables()` helper function to install the tables. If using WP_Queue in a plugin you may opt to call the helper from within your `register_activation_hook`.

## Jobs

Job classes should extend the `WP_Queue\Job` class and normally only contain a `handle` method which is called when the job is processed by the queue worker. Any data required by the job should be passed to the constructor and assigned to a public property. This data will remain available once the job is retrieved from the queue. Let's look at an example job class:

```
<?php

use WP_Queue\Job;

class Subscribe_User_Job extends Job {

	/**
	 * @var int
	 */
	public $user_id;

	/**
	 * Subscribe_User_Job constructor.
	 *
	 * @param int $user_id
	 */
	public function __construct( $user_id ) {
		$this->user_id = $user_id;
	}

	/**
	 * Handle job logic.
	 */
	public function handle() {
		$user = get_user_by( 'ID', $this->user_id );
		
		// Process the user...
	}

}
```

## Dispatching Jobs

Jobs can be pushed to the queue like so:

```
wp_queue()->push( new Subscribe_User_Job( 12345 ) );
```

You can create delayed jobs by passing an optional second parameter to the `push` method. This job will be delayed by 60 minutes:

```
wp_queue()->push( new Subscribe_User_Job( 12345 ), 3600 );
```

## Cron Worker

Jobs need to be processed by a queue worker. You can start a cron worker like so, which piggy backs onto WP cron:

```
wp_queue()->cron();
```

You can also specify the number of times a job should be attempted before being marked as a failure.

```
wp_queue()->cron( 3 );
```

## Local Development

When developing locally you may want jobs processed instantly, instead of them being pushed to the queue. This can be useful for debugging jobs via Xdebug. Add the following filter to use the `sync` connection:

```
add_filter( ‘wp_queue_default_connection’, function() {
	return ‘sync’;
} );
```

## License

WP Queue is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).