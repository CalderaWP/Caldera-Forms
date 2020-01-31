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

/**
 * Interface PrioritizedHookListenerInterface
 *
 * @package Inpsyde\Wonolog\HookListeners
 */
interface HookPriorityInterface {

	const FILTER_PRIORITY = 'wonolog.hook-listener-priority';

	/**
	 * Returns the priority of the hook callback
	 *
	 * @return int
	 */
	public function priority();
}
