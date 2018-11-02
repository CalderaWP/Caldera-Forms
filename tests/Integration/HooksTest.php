<?php

namespace calderawp\calderaforms\Tests\Integration;

use calderawp\calderaforms\cf2\Fields\FieldTypes\FileFieldType;
use calderawp\calderaforms\cf2\Fields\Handlers\FileFieldHandler;
use calderawp\calderaforms\cf2\Hooks;
use calderawp\calderaforms\cf2\RestApi\File\File;

class HooksTest extends TestCase
{

    /**
     * Test hooks are added
     *
     * @since 1.8.0
	 *
     * @covers \calderawp\calderaforms\cf2\Hooks::subscribe()
     * @covers \calderawp\calderaforms\cf2\Hooks::addFieldHandlers()
     */
    public function testSubscribe()
    {
        $hooks = new Hooks($this->getContainer() );
        $hooks->subscribe();
        $this->assertTrue( has_filter( "caldera_forms_process_field_cf2_file" ) );//meh?

        $fieldTypes = \Caldera_Forms_Fields::get_all();
        $this->assertArrayHasKey( FileFieldType::getCf1Identifier(), $fieldTypes );
    }


}
