<?php

namespace calderawp\calderaforms\Tests\Unit\Forms;

use calderawp\calderaforms\cf2\Exception;
use calderawp\calderaforms\cf2\Forms\Collection;
use calderawp\calderaforms\Tests\Unit\TestCase;

class CollectionTest extends TestCase
{

    /**
     * @since 1.8.10
     *
     * @covers \calderawp\calderaforms\cf2\Forms\Collection::addForm()
     * @covers \calderawp\calderaforms\cf2\Forms\Collection::getAll()
     */
    public function testGetAll()
    {
        $forms = new Collection();
        $forms->addForm([
            'ID' => 'cf1',
            'name' => 'Taco Pants'
        ]);
        $forms->addForm([
            'ID' => 'cf2',
            'name' => 'Taco Shirts'
        ]);
        $this->assertCount(2, $forms->getAll());
        $this->assertEquals('Taco Shirts', $forms->getAll()['cf2']['name']);
    }

    /**
     * @since 1.8.10
     *
     * @covers \calderawp\calderaforms\cf2\Forms\Collection::hasForm()
     * @covers \calderawp\calderaforms\cf2\Forms\Collection::addForm()
     */
    public function testHasForm()
    {
        $forms = new Collection();
        $forms->addForm([
            'ID' => 'cf1',
            'name' => 'Taco Pants'
        ]);
        $this->assertFalse($forms->hasForm('cf2'));
        $this->assertTrue($forms->hasForm('cf1'));
    }

    /**
     * @since 1.8.10
     *
     * @covers \calderawp\calderaforms\cf2\Forms\Collection::getForm()
     * @covers \calderawp\calderaforms\cf2\Forms\Collection::addForm()
     */
    public function testAddForm()
    {
        $forms = new Collection();
        $forms->addForm([
            'ID' => 'cf1',
            'name' => 'Taco Pants'
        ]);
        $this->assertSame('Taco Pants', $forms->getForm('cf1')['name']);
    }

    /**
     * @since 1.8.10
     *
     * @covers \calderawp\calderaforms\cf2\Forms\Collection::hasForm()
     * @covers \calderawp\calderaforms\cf2\Forms\Collection::getForm()
     */
    public function testGetFormThrows()
    {
        $forms = new Collection();
        $forms->addForm([
            'ID' => 'cf1',
            'name' => 'Taco Pants'
        ]);
        $this->expectException(Exception::class);
        $forms->getForm('cf2');
    }

    /**
     * @since 1.8.10
     *
     * @covers \calderawp\calderaforms\cf2\Forms\Collection::removeForm()
     * @covers \calderawp\calderaforms\cf2\Forms\Collection::getAll()
     */
    public function testRemoveForm()
    {
        $forms = new Collection();
        $forms->addForm([
            'ID' => 'cf1',
            'name' => 'Taco Pants'
        ]);
        $forms->addForm([
            'ID' => 'cf2',
            'name' => 'Taco Shirts'
        ]);
        $forms->removeForm('cf1');
        $this->assertTrue($forms->hasForm('cf2'));
        $this->assertFalse($forms->hasForm('cf1'));
        $this->assertCount(1, $forms->getAll());
        $this->expectException(Exception::class);
        $forms->removeForm('cf1');
    }

}
