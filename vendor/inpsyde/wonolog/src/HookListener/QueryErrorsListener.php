<?php # -*- coding: utf-8 -*-
/*
 * This file is part of the Wonolog package.
 *
 * (c) Inpsyde GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Inpsyde\Wonolog\HookListener;

use Inpsyde\Wonolog\Channels;
use Inpsyde\Wonolog\Data\Debug;
use Inpsyde\Wonolog\Data\LogDataInterface;
use Inpsyde\Wonolog\Data\NullLog;

/**
 * Looks at fronted requests and to find and log any errors.
 *
 * @package wonolog
 */
final class QueryErrorsListener implements ActionListenerInterface {

	use ListenerIdByClassNameTrait;

	/**
	 * @inheritdoc
	 */
	public function listen_to() {

		return 'wp';
	}

	/**
	 * Checks frontend request for any errors and log them.
	 *
	 * @param $args
	 *
	 * @return LogDataInterface
	 *
	 * @wp-hook wp
	 */
	public function update( array $args ) {

		$wp = $args ? reset( $args ) : NULL;

		if ( ! $wp instanceof \WP ) {
			return new NullLog();
		}

		$error = [];
		isset( $wp->query_vars[ 'error' ] ) and $error[] = $wp->query_vars[ 'error' ];
		is_404() and $error[] = '404 Page not found';

		if ( empty( $error ) ) {
			return new NullLog();
		}

		$url     = filter_var( add_query_arg( [] ), FILTER_SANITIZE_URL );
		$message = "Error on frontend request for url {$url}.";
		$context = [
			'error'        => $error,
			'query_vars'   => $wp->query_vars,
			'matched_rule' => $wp->matched_rule,
		];

		return new Debug( $message, Channels::HTTP, $context );
	}
}
