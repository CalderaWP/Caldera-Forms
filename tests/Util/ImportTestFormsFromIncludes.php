<?php


namespace calderawp\calderaforms\Tests\Util;

/**
 * Trait ImportTestFormsFromIncludes
 * @package calderawp\calderaforms\Tests\Util
 *
 * Shared trait for importing forms from the tests/includes/forms directory
 */
trait ImportTestFormsFromIncludes
{

	/**
	 * Import contact form without auto-responder
	 *
	 * @since 1.9.0
	 *
	 * @param bool $mainMailer Optional. If true, the default, contact form for main mailer is imported. If false, contact form for auti-responder is imported.
	 *
	 * @return string
	 */
	protected function importContactForm($mainMailer = true ){
		if ($mainMailer) {
			$file = $this->getPathForMainMailerImport();
		} else {
			$file = $this->getPathForAutoResponderContactForm();

		}

		return $this->importForm($file);
	}



	/**
	 * Cast array or object, like a form import, to array
	 *
	 * @since 1.5.9
	 *
	 * @param $arrayOrObject
	 *
	 * @return array
	 */
	protected function recursiveCastArray($arrayOrObject ){
		$arrayOrObject = (array)$arrayOrObject;
		foreach ($arrayOrObject as $key => $value ){
			if( is_array( $value ) || is_object( $value ) ){
				$arrayOrObject[ $key ] = $this->recursiveCastArray( $value );
			}

		}
		return $arrayOrObject;
	}


	/**
	 * Import form by file path
	 *
	 * @since 1.5.9
	 *
	 * @param string $file Path to form config
	 * @return string
	 */
	protected function importForm($file) {
		$json = file_get_contents($file);
		$config = $this->recursiveCastArray(json_decode($json));
		$form_id = \Caldera_Forms_Forms::import_form($config);
		return $form_id;
	}

	/**
	 * Import form for autoresponder tests to file system
	 *
	 * @since 1.8.0
	 *
	 * @return string
	 */
	protected function importAutoresponderForm(){
		return $this->importForm($this->getPathForAutoResponderContactForm());
	}

	/**
	 * Get file path for JSON export we import for contact form main mailer tests
	 *
	 * @since 1.9.0
	 *
	 * @return string
	 */
	protected function getPathForMainMailerImport(){
		return $file = dirname(__FILE__, 2) . '/includes/forms/contact-forms-no-auto-responder.json';
	}

	/**
	 * Get file path for JSON export we import for contact form with form_draft "1"
	 *
	 * @since 1.9.0
	 *
	 * @return string
	 */
	protected function getPathForContactFormDraft(){
		return $file = dirname(__FILE__, 2) . '/includes/forms/contact-form-form-draft.json';
	}

	/**
	 * Get file path for JSON export we import for contact form auto-responder tests
	 *
	 * @since 1.9.0
	 *
	 * @return string
	 */
	protected function getPathForAutoResponderContactForm(){
		return dirname(__FILE__, 2) . '/includes/forms/contact-form-autoresponder.json';
	}

	/**
	 * Assert a value is numeric
	 *
	 * @since 1.9.0
	 *
	 * @param mixed $maybeNumeric
	 */
	protected function assertIsNumeric( $maybeNumeric, $message = '' ){
		$this->assertTrue( is_numeric( $maybeNumeric ), $message );
	}
}
