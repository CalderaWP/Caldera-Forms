<?php

namespace calderawp\calderaforms\Tests\Unit;

use calderawp\calderaforms\cf2\Fields\RenderField;
use Brain\Monkey;

class RenderFieldTest extends TestCase
{

    /**
     * @covers \calderawp\calderaforms\cf2\Fields\RenderField::__construct()
     * @covers \calderawp\calderaforms\cf2\Fields\RenderField::$field
     * @covers \calderawp\calderaforms\cf2\Fields\RenderField::$formIdAttr
     */
    public function test__construct()
    {
        $field = $this->fieldFactory('email' );
        $formIdAttr = 'cf1';
        $renderer = new RenderField($formIdAttr,$field );
        $this->assertAttributeEquals($formIdAttr, 'formIdAttr', $renderer );
        $this->assertAttributeEquals($field, 'field', $renderer );
    }

    /**
     * @covers \calderawp\calderaforms\cf2\Fields\RenderField::getFieldIdAttr();
     */
    public function testGetFieldIdAttr()
    {
        $field = $this->fieldForRenderFactory();
        $formIdAttr = 'cf1';
        $renderer = new RenderField($formIdAttr,$field );
        $this->assertEquals($field['fieldIdAttr'], $renderer->getFieldIdAttr() );
    }

    /**
     * @covers \calderawp\calderaforms\cf2\Fields\RenderField::getFormIdAttr();
     */
    public function testGetFormIdAttr()
    {
        $field = $this->fieldForRenderFactory();
        $formIdAttr = 'cf1_1';
        $renderer = new RenderField($formIdAttr,$field );
        $this->assertEquals($formIdAttr, $renderer->getFormIdAttr() );
    }

    /**
     * @covers \calderawp\calderaforms\cf2\Fields\RenderField::data();
     */
    public function testData()
    {
        $fieldId = 'fld_1';
        $field = $this->fieldForRenderFactory($fieldId);
        $formIdAttr = 'cf1_1';
        $renderer = new RenderField($formIdAttr,$field );
        $data = $renderer->data();
        $this->assertEquals([
            'type' => 'text',
            'outterIdAttr' => 'cf2-fld_1',
            'fieldId' => 'fld_1',
            'fieldLabel' => 'Email',
            'fieldCaption' => 'Make emails',
            'fieldPlaceHolder' => '',
            'required' => 1,
            'fieldDefault' => '',
            'fieldValue' => '',
            'fieldIdAttr' => 'fld_1',
        ],$data);
    }



    /**
     * @covers \calderawp\calderaforms\cf2\Fields\RenderField::getType();
     */
    public function testGetType()
    {
        $fieldId = 'fld_1';
        $field = $this->fieldForRenderFactory($fieldId);
        $field[ 'type' ] = 'cf2_file';
        $formIdAttr = 'cf1_1';
        $renderer = new RenderField($formIdAttr,$field );
        $data = $renderer->data();
        $this->assertEquals('file',$data['type']);
    }




    /**
     * @covers \calderawp\calderaforms\cf2\Fields\RenderField::render();
     */
    public function testRender()
    {

        $field = $this->fieldForRenderFactory();
        $formIdAttr = 'cf1_1';
        $renderer = new RenderField($formIdAttr,$field );
        $markup = $renderer->render();
        $this->assertNotFalse(
            strpos( $markup,'class="cf2-field-wrapper"')
        );
        $this->assertNotFalse(
            strpos( $markup,$renderer->getOuterIdAttr() )
        );
        $this->assertNotFalse(
            strpos( $markup,'data-field-id=')
        );
    }

    /**
     * @covers \calderawp\calderaforms\cf2\Fields\RenderField::getOuterIdAttr();
     */
    public function testGetOuterIdAttr()
    {
        $fieldId = 'fld_1';
        $field = $this->fieldForRenderFactory($fieldId);
        $formIdAttr = 'cf1_1';
        $renderer = new RenderField($formIdAttr,$field );
        $this->assertEquals("cf2-$fieldId", $renderer->getOuterIdAttr() );
    }

    /**
     * @return array
     */
    protected function fieldForRenderFactory($fieldId = null )
    {
        $field = $this->fieldFactory('email', $fieldId);
        $field = array_merge($field, ['fieldIdAttr' => $field['ID'] ]);
        return $field;
    }
}
