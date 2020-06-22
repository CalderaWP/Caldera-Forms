<?php

namespace calderawp\calderaforms\Tests\Integration\Factories;

use calderawp\calderaforms\cf2\Factories\Processor;
use calderawp\calderaforms\Tests\Integration\TestCase;
use calderawp\calderaforms\Tests\Util\SubmissionHelpers;

class ProcessorTest extends TestCase
{
    /** @inheritDoc */
    public function tearDown()
    {
        //Delete forms after each test
        $forms = \Caldera_Forms_Forms::get_forms(false, true);
        if (!empty($forms)) {
            foreach ($forms as $formId) {
                \Caldera_Forms_Forms::delete_form($formId);
            }
        }
        parent::tearDown();
    }


    /**
     * Make sure that by setting field $_POST we can mock field data as other tests expect
     *
     * @since 1.8.10
     *
     * @group now
     */
    public function testTheTestAssumptions()
    {
        //Import form
        $formId = $this->importFormWithAutoResponder();
        //Set as global form
        global $form;
        $form = \Caldera_Forms_Forms::get_form($formId);

        //Set two field values
        $_POST = SubmissionHelpers::submission_data($formId, [
            "fld_8768091" => "Roy",
            "fld_9970286" => 'Sivan',
        ]);

        //Check both ways they can be accessed by \Caldera_Forms_Get_Data
        $this->assertEquals(
            'Roy',
            \Caldera_Forms::do_magic_tags('%first_name%', null, $form)
        );

        $this->assertEquals(
            'Roy',
            \Caldera_Forms::get_field_data('fld_8768091', $form)
        );

        $this->assertEquals(
            'Sivan',
            \Caldera_Forms::do_magic_tags('%last_name%', null, $form)
        );

        $this->assertEquals(
            'Sivan',
            \Caldera_Forms::get_field_data('fld_9970286', $form)
        );

        $data = new \Caldera_Forms_Processor_Get_Data([
            "one" => '%first_name%',
            "two" => 'fld_9970286',
            'three' => 'hard coded value'
        ], $form, [
            [
                'id' => 'one',
                'label' => 'one',
                'magic' => true,
            ],
            [
                'id' => 'two',
                'label' => 'two',
                'magic' => false,
            ],
            [
                'id' => 'three',
                'label' => 'three',
                'magic' => true,
            ],
        ]);
        $this->assertEquals('Roy', $data->get_value('one'));
        $this->assertEquals('Sivan', $data->get_value('two'));
        $this->assertEquals('hard coded value', $data->get_value('three'));

    }

    /**
     * Test processor data factory
     *
     * @since 1.8.10
     *
     * @group now
     */
    public function testFactory()
    {
        $formId = $this->importFormWithAutoResponder();
        global $form;
        $form = \Caldera_Forms_Forms::get_form($formId);
        $_POST = SubmissionHelpers::submission_data($formId, [
            "fld_8768091" => "Roy",
            "fld_9970286" => 'Sivan',
        ]);

        $factory = new Processor();
        $data = $factory->dataFactory([
            "one" => '%first_name%',
            "two" => 'fld_9970286',
            'three' => 'hard coded value'
        ], $form, [
            [
                'id' => 'one',
                'label' => 'one',
                'magic' => true,
            ],
            [
                'id' => 'two',
                'label' => 'two',
                'magic' => false,
            ],
            [
                'id' => 'three',
                'label' => 'three',
                'magic' => true,
            ],
        ]);

        $this->assertEquals('Roy', $data->get_value('one'));
        $this->assertEquals('Sivan', $data->get_value('two'));
        $this->assertEquals('hard coded value', $data->get_value('three'));

    }




}
