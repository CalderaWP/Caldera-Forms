<?php

/**
 * Interface that all CDN integrations must impliment
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2017 CalderaWP LLC
 */
interface Caldera_Forms_CDN_Contract {

	/**
	 * The URL for CDN to replace site URL with
	 *
	 * NOTE: Do NOT add protocol. start with //
	 *
	 * @since 1.5.3
	 *
	 * @return string
	 */
	public function cdn_url();

}