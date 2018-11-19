<?php


class Test_Caldera_Forms_Entry_Fields_Collection extends Caldera_Forms_Test_Case
{

    protected  $form_id;

    /**
     * Testing adding a field and finding it by entry ID
     *
     * @since 1.7.0
     *
     * @covers Caldera_Forms_Entry_Fields::add_field()
     * @covers Caldera_Forms_Entry_Fields::has_field()
     * @covers Caldera_Forms_Entry_Fields::get_field()
     * @covers Caldera_Forms_Entry_Fields::count()
     */
    public function testAddField(){
        $collection = new Caldera_Forms_Entry_Fields($this->form() );
        $field =  Caldera_Forms_Entry_Factory::entry_field([
            'field_id' => 'fldOne',
            'entry_id' => 12,
            'value' => rand(),
            'slug' => 'fldOne'
        ]);
        $collection->add_field( $field );
        $this->assertEquals(1, $collection->count() );
        $this->assertTrue( $collection->has_field( 12 ) );
        $this->assertEquals( $field, $collection->get_field( 12 ) );


    }

    /**
     * Test setting entry field values through contructors
     *
     * @since 1.7.0
     *
     * @covers Caldera_Forms_Entry_Fields::__construct()
     * @covers Caldera_Forms_Entry_Fields::set_fields_form_array()
     * @covers Caldera_Forms_Entry_Fields::count()
     */
    public function testSetThroughConstructor(){
        $form = $this->form( Caldera_Forms_Forms::create_unique_form_id() );
        $field_one = Caldera_Forms_Entry_Factory::entry_field([
            'field_id' => 'fldOne',
            'entry_id' => 12,
            'value' => rand(),
            'slug' => 'fldOne'
        ]);
        $field_two = Caldera_Forms_Entry_Factory::entry_field([
            'field_id' => 'fldTwo',
            'entry_id' => 13,
            'value' => rand(),
            'slug' => 'fldTwo'
        ]);
        $collection = new Caldera_Forms_Entry_Fields($form, [$field_one,$field_two]);
        $this->assertSame( 2, $collection->count() );
    }

    /**
     *
     * @since 1.7.0
     *
     * @covers Caldera_Forms_Entry_Fields::__construct()
     * @covers Caldera_Forms_Entry_Fields::set_fields_form_array()
     * @covers Caldera_Forms_Entry_Fields::count()
     */
    public function testSetThroughConstructorFromArrays(){
        $form = $this->form( Caldera_Forms_Forms::create_unique_form_id() );
        $field_one_array = [
            'id' => 1,
            'field_id' => 'fldOne',
            'value' => rand(),
            'slug' => 'fldOne',
            'entry_id' => 1,
        ];
        $field_two_array = [
            'id'=> 2,
            'field_id' => 'fldTwo',
            'value' => rand(),
            'slug' => 'fldTwo',
            'entry_id' => 2,
        ];
        $collection = new Caldera_Forms_Entry_Fields($form, [$field_one_array,$field_two_array]);
        $this->assertSame( 2, $collection->count() );
    }

    /**
     * Test array conversion for collection is recursive
     *
     * @since 1.7.0
     *
     * @covers Caldera_Forms_Entry_Fields::toArray()
     * @covers Caldera_Forms_Entry_Field::to_array()
     */
    public function testToArray(){
        $form_id = Caldera_Forms_Forms::create_unique_form_id();
        $form = $this->form( $form_id );
        $field_one_array = [
            'id' => 1,
            'field_id' => 'fldOne',
            'value' => rand(),
            'slug' => 'fldOne',
            'entry_id' => 1,
        ];
        $field_two_array = [
            'id'=> 2,
            'field_id' => 'fldTwo',
            'value' => rand(),
            'slug' => 'fldTwo',
            'entry_id' => 2,
        ];
        $field_one = new Caldera_Forms_Entry_Field((object)$field_one_array);
        $field_two = new Caldera_Forms_Entry_Field((object)$field_two_array);
        $collection = new Caldera_Forms_Entry_Fields($form, [$field_one,$field_two]);
        $this->assertEquals(2, count( $collection->toArray() ) );
        $this->assertArrayHasKey( 'fldOne', $collection->toArray() );
        $this->assertArrayHasKey( 'fldTwo', $collection->toArray() );
    }


    /**
     * Create testing from configuration
     *
     * @since 1.7.0
     *
     * @param null|string $form_id
     * @return array
     */
    protected function form($form_id = null ){
        $this->form_id = is_null($form_id ) ?Caldera_Forms_Forms::create_unique_form_id() : $form_id ;
        return [
            'ID' => $this->form_id,
            'fields' => [
                'fldOne' => [],
                'fldTwo' => []
            ]
        ];
    }
}