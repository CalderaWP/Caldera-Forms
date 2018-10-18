<?php

namespace calderawp\CalderaFormsQueries;

use calderawp\CalderaContainers\Service\Container;
use calderawp\CalderaFormsQuery\Features\FeatureContainer;

/**
 * The CalderaFormsQueries
 *
 * Acts as static accessor for feature container
 *
 * @return FeatureContainer
 */
function CalderaFormsQueries()
{
	static $CalderaFormsQueries;
	if (! $CalderaFormsQueries) {
		global $wpdb;
		$CalderaFormsQueries = new FeatureContainer(
			new Container(),
			$wpdb
		);
	}

	return $CalderaFormsQueries;
}
