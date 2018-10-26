<?php

namespace calderawp\calderaforms\Tests\Integration\Transients;

use calderawp\calderaforms\cf2\Transients\Cf1TransientsApi;
use calderawp\calderaforms\Tests\Integration\TestCase;

class Cf1TransientsApiTest extends TestCase
{

    /**
     * Test that new Transients API wrapper is interoperable with old API's delete method
     *
     * @since 1.8.0
     *
     * @group transients
     *
     * @covers \calderawp\calderaforms\cf2\Transients\Cf1TransientsApi::deleteTransient()
     */
    public function testDeleteTransient()
    {

        $id = rand() . '1';
        $data = [
            'foo' => 'barts'
        ];
        \Caldera_Forms_Transient::set_transient($id, $data, 699 );
        $api = new Cf1TransientsApi();
        $api->deleteTransient($id);
        $this->assertFalse( $api->getTransient($id) );
    }

    /**
     * Test that new Transients API wrapper is interoperable with old API's set method
     *
     * @since 1.8.0
     *
     * @group transients
     *
     * @covers \calderawp\calderaforms\cf2\Transients\Cf1TransientsApi::getTransient()
     */
    public function testGetTransient()
    {
        $id = rand() . '2';
        $data = [
            'foo' => 'barts'
        ];
        \Caldera_Forms_Transient::set_transient($id, $data, 699 );
        $api = new Cf1TransientsApi();
        $this->assertEquals( $data, $api->getTransient($id) );
    }

    /**
     * Test that new Transients API wrapper is interoperable with old API's get method
     *
     * @since 1.8.0
     *
     * @group transients
     *
     * @covers \calderawp\calderaforms\cf2\Transients\Cf1TransientsApi::setTransient()
     */
    public function testSetTransient()
    {
        $id = rand() . '3';
        $data = [
            'foo' => 'barts'
        ];
        $api = new Cf1TransientsApi();
        $api->setTransient($id, $data, 699);
        $this->assertEquals(  $data, \Caldera_Forms_Transient::get_transient ($id) );
    }


    /**
     * Test that new Transients API wrapper is interoperable with old API's get method
     *
     * @since 1.8.0
     *
     * @group transients
     * @covers \calderawp\calderaforms\cf2\Transients\Cf1TransientsApi::setTransient()
     */
    public function testSetTransientArray()
    {
        $id = rand() . '3';
        $data = [
            'foo' => 'barts',
            'bars' => ['x' => 1]
        ];
        $api = new Cf1TransientsApi();
        $api->setTransient($id, $data, 699);
        $this->assertEquals(  $data, \Caldera_Forms_Transient::get_transient ($id) );
    }

    /**
     * Test that new Transients API wrapper can have mutliple values
     *
     * @since 1.8.0
     *
     * @group transients
     * @covers \calderawp\calderaforms\cf2\Transients\Cf1TransientsApi::setTransient()
     */
    public function testUniquness()
    {
        $api = new Cf1TransientsApi();
        $id1 = uniqid(rand(5,7));
        $id2 = uniqid(rand(1,3));
        $data1 = ['foo' => 'narr' ];
        $data2 = ['aaafoo' => 'narr' ];
        $api->setTransient($id1, $data1, 699);
        $api->setTransient($id2, $data2, 699);

        $this->assertEquals(
            $data1,
            $api->getTransient($id1)
        );
        $this->assertEquals(
            $data2,
            $api->getTransient($id2)
        );

    }

}
