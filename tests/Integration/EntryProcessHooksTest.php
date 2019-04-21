<?php

namespace calderawp\calderaforms\Tests\Integration;

use calderawp\calderaforms\cf2\Process\EntryProcessHooks;

class EntryProcessHooksTest extends TestCase
{


	/**
	 * @since 1.9.0
	 * @group cf2
	 * @covers \calderawp\calderaforms\cf2\Process\EntryProcessHooks::validateField()
	 */
	public function testValidateField()
	{
		$container = $this->getContainer();
		$hooks = new EntryProcessHooks($container);
		$formId = $this->importAutoresponderForm();
		$form = \Caldera_Forms_Forms::get_form($formId);
		$entry = new \Caldera_Forms_Entry($form);

		$entryField = new \Caldera_Forms_Entry_Field();
		$fieldConfig = [
			'type' => 'cf2_something',
			'ID' => 'fld_9970286'
		];

		/**
		 * Field specific filters
		 */
		add_filter( 'caldera_forms_validate_field_fld_9970286', function(){
			return 'VALUE!';
		});

		/**
		 * Field specific filters
		 */
		add_filter( 'caldera_forms_validate_field_fld_email', function(){
			return 'EMAIL!';
		});

		$entryField = $hooks->validateField(
			$entryField,
			$entry,
			$fieldConfig

		);
		$this->assertSame( 'VALUE!', $entryField->get_value() );


		$fieldConfig = [
			'type' => 'email',
			'ID' => 'fld_1'
		];


		$entryField = $hooks->validateField(
			$entryField,
			$entry,
			$fieldConfig

		);
		$this->assertSame( 'EMAIL!', $entryField->get_value() );
	}

	/**
	 * @since 1.9.0
	 * @group cf2
	 *
	 * @covers \calderawp\calderaforms\cf2\Process\EntryProcessHooks::validateField()
	 */
	public function test__construct()
	{
		$container = $this->getContainer();
		$hooks = new EntryProcessHooks($container);
		$this->assertAttributeEquals( $container, 'container', $hooks );
	}

	/**
	 * @since 1.9.0
	 * @group cf2
	 *
	 * @covers \calderawp\calderaforms\cf2\Process\EntryProcessHooks::subscribeToPreSave()
	 */
	public function testPreSaveSubscriptions()
	{
		$container = $this->getContainer();
		$hooks = new EntryProcessHooks($container);
		$formId = $this->importAutoresponderForm();
		$form = \Caldera_Forms_Forms::get_form($formId);
		$entry = new \Caldera_Forms_Entry($form);
		$hooks->subscribeToPreSave($entry,'sa');
		$this->assertTrue( did_action( 'calderaForms/submit/preProcess/start' ) );
		$this->assertTrue( did_action( 'calderaForms/submit/preProcess/end' ) );
	}

	/**
	 * @since 1.9.0
	 * @group cf2
	 *
	 * @covers \calderawp\calderaforms\cf2\Process\EntryProcessHooks::subscribeToPostSave()
	 */
	public function testPostSaveSubscriptions()
	{
		$container = $this->getContainer();
		$hooks = new EntryProcessHooks($container);
		$formId = $this->importAutoresponderForm();
		$form = \Caldera_Forms_Forms::get_form($formId);
		$entry = new \Caldera_Forms_Entry($form);
		$hooks->subscribeToPreSave($entry,'sa');
		$this->assertTrue( did_action( 'calderaForms/submit/postProcess/start' ) );
		$this->assertTrue( did_action( 'calderaForms/submit/postProcess/end' ) );
		$this->assertTrue( did_action( 'calderaForms/submit/complete' ) );
	}

}
