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
     * @group now
     */
    public function testGetFieldData(){
        $form = $this->getForm();
        $saved = SubmissionHelpers::createEntry($form, ['name' => 'Darth Vader', 'show_name' => 'yes'] );

        //Get field values without checking conditionals
        $this->assertSame( 'yes',\Caldera_Forms::get_field_data('show_name', $form, $saved['id'],false) );
        $this->assertSame( 'Darth Vader',\Caldera_Forms::get_field_data('name', $form, $saved['id'],false) );

        //Get field values with check of  conditionals
        $this->assertSame( 'yes',\Caldera_Forms::get_field_data('show_name', $form, $saved['id'],true) );
        $this->assertSame( 'Darth Vader',\Caldera_Forms::get_field_data('name', $form, $saved['id'],true) );
    }

    /**
     * Ensure that when we check conditional for saved field value, that should show, value is returned.
     *
     * @since 1.8.10
     * @group now
     *
     * @covers \Caldera_Forms::check_condition()
     */
    public function testConditionalSavedField(){
        $form = $this->getForm();
        $saved = SubmissionHelpers::createEntry($form, ['name' => 'Darth Vader', 'show_name' => 'yes'] );
        $field = \Caldera_Forms_Field_Util::get_field('name', $form);
        $conditional = $field[ 'conditions' ];
        if ( ! empty( $form[ 'conditional_groups' ][ 'conditions' ][ $field[ 'conditions' ][ 'type' ] ] ) ) {
            $conditional = $form[ 'conditional_groups' ][ 'conditions' ][ $field[ 'conditions' ][ 'type' ] ];
        }

        $this->assertTrue( \Caldera_Forms::check_condition( $conditional, $form, $saved['id'] ) );
    }

    /**
     * Ensure that when we check conditional for saved field value, that should NOT show, value is NOT returned.
     *
     * @since 1.8.10
     *
     * @group now
     * @covers \Caldera_Forms::check_condition()
     */
    public function testConditionallyHiddenSavedField(){
        $form = $this->getForm();
        $saved = SubmissionHelpers::createEntry($form, ['name' => 'Darth Vader', 'show_name' => 'no'] );
        $field = \Caldera_Forms_Field_Util::get_field('name', $form);
        $conditional = $field[ 'conditions' ];
        if ( ! empty( $form[ 'conditional_groups' ][ 'conditions' ][ $field[ 'conditions' ][ 'type' ] ] ) ) {
            $conditional = $form[ 'conditional_groups' ][ 'conditions' ][ $field[ 'conditions' ][ 'type' ] ];
        }

        $this->assertFalse( \Caldera_Forms::check_condition( $conditional, $form, $saved['id'] ) );
    }

    /**
     * Make sure test form loads
     *
     * @since 1.8.10
     */
    public function testGetForm(){
        $this->assertTrue(is_array($this->getForm()));
    }
    protected function getForm() {
        return include  dirname(__FILE__, 3 ) . '/includes/forms/conditional-name-field.php';

    }
}