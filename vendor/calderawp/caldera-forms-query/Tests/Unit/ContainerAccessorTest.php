<?php


namespace calderawp\CalderaFormsQuery\Tests\Unit;


use function calderawp\CalderaFormsQueries\CalderaFormsQueries;

class ContainerAccessorTest extends TestCase
{


    public function setUp()
    {
        global $wpdb;
        $wpdb = $this->getWPDB();
        parent::setUp();
    }

    /**
     * Test static accessor returns container
     *
     * @covers  \calderawp\CalderaFormsQueries\CalderaFormsQueries()
     */
    public function testIsObject()
    {

        $this->assertTrue(is_object( CalderaFormsQueries() ) );
        $this->assertTrue(is_object( \calderawp\CalderaFormsQueries\CalderaFormsQueries() ) );
    }

    /**
     * Test static accessor returns act like a container
     *
     * @covers  \calderawp\CalderaFormsQueries\CalderaFormsQueries()
     */
    public function testActsAsContainer()
    {

        $this->assertTrue(is_object( CalderaFormsQueries()->getBuilder() ) );
        $this->assertTrue(is_object( \calderawp\CalderaFormsQueries\CalderaFormsQueries()->getBuilder() ) );
    }

}