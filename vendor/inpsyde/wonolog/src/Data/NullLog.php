<?php # -*- coding: utf-8 -*-
/*
 * This file is part of the Wonolog package.
 *
 * (c) Inpsyde GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Inpsyde\Wonolog\Data;

/**
 * Implements the interface doing nothing.
 *
 * @package wonolog
 * @license http://opensource.org/licenses/MIT MIT
 *
 * @codeCoverageIgnore
 */
final class NullLog implements LogDataInterface {

	const LOG_LEVEL = -1;

	/**
	 * @return int
	 */
	public function level() {

		return self::LOG_LEVEL;
	}

	/**
	 * @return string
	 */
	public function message() {

		return '';
	}

	/**
	 * @return string
	 */
	public function channel() {

		return '';
	}

	/**
	 * @return array
	 */
	public function context() {

		return [];
	}
}
