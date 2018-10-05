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

use Inpsyde\Wonolog\Data\LogDataInterface;

/**
 * @package wonolog
 * @license http://opensource.org/licenses/MIT MIT
 */
interface ActionListenerInterface extends HookListenerInterface {

	/**
	 * @param array $args
	 *
	 * @return LogDataInterface
	 */
	public function update( array $args );
}
