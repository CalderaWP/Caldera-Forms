<?php
/*
 * This file is part of the Wonolog package.
 *
 * (c) Inpsyde GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Inpsyde\Wonolog\Data;

use Monolog\Logger;

/**
 * A log event with predefined level set to ALERT.
 *
 * @package wonolog
 * @license http://opensource.org/licenses/MIT MIT
 */
final class Alert implements LogDataInterface {

	use LogDataTrait;

	/**
	 * @inheritdoc
	 */
	public function level() {

		return Logger::ALERT;
	}
}
