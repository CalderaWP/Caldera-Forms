<?php


namespace calderawp\calderaforms\Tests\Integration\Features;

use calderawp\calderaforms\Tests\Integration\TestCase;
use calderawp\calderaforms\Tests\Util\SubmissionHelpers;

/**
 * Class EntryApiV1Test
 *
 * Tests for CF1 functions for saved entries in Caldera_Forms class.
 */
class EntryApiV1Test extends TestCase
{

    /**
     * When getting saved field data, does conditional logic apply SHOW conditional that SHOULD pass.
     *
     * @since 1.8.10
     *
     * @covers Caldera_Forms::get_field_data()
     * @covers Caldera_Forms::check_condition()
     * @covers Caldera_Forms::apply_conditional_groups()
     */
    public function testGetFieldDataConditionallyShown()
    {
        $form = $this->getForm();
        $saved = SubmissionHelpers::createEntry($form, ['name' => 'Darth Vader', 'show_name' => 'yes']);

        //Get field values without checking conditionals
        $this->assertSame('yes', \Caldera_Forms::get_field_data('show_name', $form, $saved['id'], false));
        $this->assertSame('Darth Vader', \Caldera_Forms::get_field_data('name', $form, $saved['id'], false));

        //Get field values with check of  conditionals
        $this->assertSame('yes', \Caldera_Forms::get_field_data('show_name', $form, $saved['id'], true));
        $this->assertSame('Darth Vader', \Caldera_Forms::get_field_data('name', $form, $saved['id'], true));

    }

    /**
     * When getting saved field data, does conditional logic apply SHOW conditional that SHOULD NOT pass.
     *
     * @since 1.8.10
     *
     * @covers Caldera_Forms::get_field_data()
     * @covers Caldera_Forms::check_condition()
     * @covers Caldera_Forms::apply_conditional_groups()
     */
    public function testGetFieldDataConditionallyNotShown()
    {
        $form = $this->getForm();
        $saved = SubmissionHelpers::createEntry($form, ['name' => 'Darth Vader', 'show_name' => 'no']);

        $this->assertSame('no', \Caldera_Forms::get_field_data('show_name', $form, $saved['id'], false));
        $this->assertSame('Darth Vader', \Caldera_Forms::get_field_data('name', $form, $saved['id'], false));

        $this->assertSame('no', \Caldera_Forms::get_field_data('show_name', $form, $saved['id'], true));
        $this->assertSame(null, \Caldera_Forms::get_field_data('name', $form, $saved['id'], true));
    }

    /**
     * When getting saved submission data, does conditional logic apply SHOW conditional that SHOULD pass.
     *
     * @since 1.8.10
     *
     * @covers Caldera_Forms::get_submission_data()
     * @covers Caldera_Forms::check_condition()
     * @covers Caldera_Forms::apply_conditional_groups()
     */
    public function testGetSubmissionDataConditionallyShown()
    {
        $form = $this->getForm();
        $saved = SubmissionHelpers::createEntry($form, ['name' => 'Darth Vader', 'show_name' => 'yes']);
        $submissionData = \Caldera_Forms::get_submission_data($form, $saved['id'], true);
        $this->assertSame('yes', $submissionData['show_name']);
        $this->assertTrue(isset($submissionData['name']));
        $this->assertSame('Darth Vader', $submissionData['name']);

        //Also check without conditional logic checked.
        $submissionData = \Caldera_Forms::get_submission_data($form, $saved['id'], false);
        $this->assertSame('yes', $submissionData['show_name']);
        $this->assertSame('Darth Vader', $submissionData['name']);

    }

    /**
     * When getting saved submission data, does conditional logic apply SHOW NOT conditional that SHOULD NOT pass.
     *
     * @since 1.8.10
     *
     * @covers Caldera_Forms::get_submission_data()
     * @covers Caldera_Forms_Save_Final::create_entry()
     * @covers Caldera_Forms::check_condition()
     * @covers Caldera_Forms::apply_conditional_groups()
     */
    public function testGetSubmissionDataConditionallyNotShown()
    {
        $form = $this->getForm();
        $saved = SubmissionHelpers::createEntry($form, ['name' => 'Darth Vader', 'show_name' => 'no']);
        $submissionData = \Caldera_Forms::get_submission_data($form, $saved['id'], true);
        $this->assertSame('no', $submissionData['show_name']);
        $this->assertFalse(isset($submissionData['name']));

        //Also check without conditional logic checked.
        $submissionData = \Caldera_Forms::get_submission_data($form, $saved['id'], false);
        $this->assertSame('no', $submissionData['show_name']);
        //Should not have saved.
        $this->assertFalse(isset( $submissionData['name']) );
    }


    /**
     * Ensure that when we check conditional for saved field value, that should show, value is returned.
     *
     * @since 1.8.10
     *
     * @covers \Caldera_Forms::check_condition()
     */
    public function testConditionalSavedField()
    {
        $form = $this->getForm();
        $saved = SubmissionHelpers::createEntry($form, ['name' => 'Darth Vader', 'show_name' => 'yes']);
        $field = \Caldera_Forms_Field_Util::get_field('name', $form);
        
        //This is the same logic as used in `Caldera_Forms::apply_conditional_groups()`
        $conditional = $field['conditions'];
        if (!empty($form['conditional_groups']['conditions'][$field['conditions']['type']])) {
            $conditional = $form['conditional_groups']['conditions'][$field['conditions']['type']];
        }

        $this->assertTrue(\Caldera_Forms::check_condition($conditional, $form, $saved['id']));
    }


    /**
     * Ensure that when we check conditional for saved field value, that should NOT show, value is NOT returned.
     *
     * @since 1.8.10
     *
     * @covers \Caldera_Forms::check_condition()
     */
    public function testConditionallyHiddenSavedField()
    {
        $form = $this->getForm();
        $saved = SubmissionHelpers::createEntry($form, ['name' => 'Darth Vader', 'show_name' => 'no']);
        $field = \Caldera_Forms_Field_Util::get_field('name', $form);
        $conditional = $field['conditions'];
        if (!empty($form['conditional_groups']['conditions'][$field['conditions']['type']])) {
            $conditional = $form['conditional_groups']['conditions'][$field['conditions']['type']];
        }

        $this->assertFalse(\Caldera_Forms::check_condition($conditional, $form, $saved['id']));
    }

    /**
     * Make sure test form loads
     *
     * @since 1.8.10
     */
    public function testGetForm()
    {
        $this->assertTrue(is_array($this->getForm()));
    }

    protected function getForm()
    {
        return include dirname(__FILE__, 3) . '/includes/forms/conditional-name-field.php';

    }
}