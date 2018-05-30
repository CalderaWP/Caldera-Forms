<?php


class Test_Caldera_Forms_Entry_Factories extends Caldera_Forms_Test_Case
{

    /**
     * Test Caldera_Forms_Entry_Field when provided an array
     *
     * @since 1.7.0
     *
     * @covers Caldera_Forms_Entry_Factory::entry_field()
     * @covers Caldera_Forms_Entry_Field::set_form_object()
     * @covers Caldera_Forms_Entry_Field::__construct()
     */
    public function testEntryFieldFromArray(){

        $field_one_array = [
            'id' => 1,
            'field_id' => 'fldOne',
            'value' =>'HiRoy',
            'slug' => 'fldOne',
            'entry_id' => 1,
        ];

        $entry_field = Caldera_Forms_Entry_Factory::entry_field($field_one_array);
        $this->assertTrue( is_a( $entry_field, Caldera_Forms_Entry_Field::class ) );
        $this->assertSame( $field_one_array['id'], $entry_field->id );
        $this->assertSame( $field_one_array['field_id'], $entry_field->field_id );
        $this->assertSame( $field_one_array['value'], $entry_field->value );
        $this->assertSame( $field_one_array['slug'], $entry_field->slug );
        $this->assertSame( $field_one_array['entry_id'], $entry_field->entry_id );

    }

    /**
     * Test Caldera_Forms_Entry_Field factory when provided a stdClass
     *
     * @since 1.7.0
     *
     * @covers Caldera_Forms_Entry_Factory::entry_field()
     * @covers Caldera_Forms_Entry_Field::set_form_object()
     * @covers Caldera_Forms_Entry_Field::__construct()
     */
    public function testEntryFieldFromStdClass(){

        $field_one_array = [
            'id' => 1,
            'field_id' => 'fldOne',
            'value' =>'HiRoy',
            'slug' => 'fldOne',
            'entry_id' => 1,
        ];

        $entry_field = Caldera_Forms_Entry_Factory::entry_field((object)$field_one_array);
        $this->assertTrue( is_a( $entry_field, Caldera_Forms_Entry_Field::class ) );
        $this->assertSame( $field_one_array['id'], $entry_field->id );
        $this->assertSame( $field_one_array['field_id'], $entry_field->field_id );
        $this->assertSame( $field_one_array['value'], $entry_field->value );
        $this->assertSame( $field_one_array['slug'], $entry_field->slug );
        $this->assertSame( $field_one_array['entry_id'], $entry_field->entry_id );

    }

    /**
     * Test Caldera_Forms_Entry_Field factory when provided a Caldera_Forms_Entry_Field
     *
     * @since 1.7.0
     *
     * @covers Caldera_Forms_Entry_Factory::entry_field()
     * @covers Caldera_Forms_Entry_Field::set_form_object()
     * @covers Caldera_Forms_Entry_Field::__construct()
     */
    public function testEntryFieldFromCorrectClass(){

        $field_one_array = [
            'id' => 1,
            'field_id' => 'fldOne',
            'value' =>'HiRoy',
            'slug' => 'fldOne',
            'entry_id' => 1,
        ];
        $object = new Caldera_Forms_Entry_Field((object)$field_one_array);
        $factory_result = Caldera_Forms_Entry_Factory::entry_field($object);
        $this->assertSame( $object, $factory_result );

    }


}