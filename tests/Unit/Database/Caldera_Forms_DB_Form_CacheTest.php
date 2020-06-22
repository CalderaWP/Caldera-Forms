<?php

namespace calderawp\calderaforms\Tests\Unit\Database;

use Caldera_Forms_DB_Form_Cache;
use Caldera_Forms_DB_Form_Interface;
use calderawp\calderaforms\Tests\Unit\TestCase;

class Db_Mock implements  Caldera_Forms_DB_Form_Interface
{

    protected $nextResult;
    public function setNextResult($nextResult)
    {

        $this->nextResult = $nextResult;
    }
    public function get_all($primary = true)
    {
       return $this->nextResult;
    }


    public function get_by_form_id($form_id, $primary_only = true)
    {
        return $this->nextResult;
    }


    public function create(array $data)
    {
        return $this->nextResult;
    }


    public function update(array $data)
    {
        return $this->nextResult;
    }


    public function delete_by_form_id($form_id)
    {
        return $this->nextResult;
    }


    public function delete($ids)
    {
        return $this->nextResult;
    }
}
class Caldera_Forms_DB_Form_CacheTest extends TestCase
{

    /**
     * Shows how this mock works.
     *
     * @covers \calderawp\calderaforms\Tests\Unit\Database\Db_Mock::setNextResult()
     */
    public function testMockSystem(){
        $dbApi = new Db_Mock();
        $cache = new Caldera_Forms_DB_Form_Cache($dbApi);
        $form1 = [
            'ID' => 'cf_tacos',
            'name' => 'Tacos'
        ];
        //Every method will return whatever is set here.
        $dbApi->setNextResult(42);
        $this->assertSame( 42, $cache->create($form1) );
    }

    /**
     * @covers Caldera_Forms_DB_Form_Cache::get_by_form_id()
     * @covers Caldera_Forms_DB_Form_Cache::create()
     */
    public function testGet_by_form_id()
    {
        $dbApi = new Db_Mock();
        $cache = new Caldera_Forms_DB_Form_Cache($dbApi);
        $form1 = [
            'ID' => 'cf_tacos',
            'name' => 'Tacos'
        ];
        $cache->create($form1);
        $form2 = [
            'ID' => 'cf_sandwiches',
            'name' => 'nachos'
        ];
        $cache->create($form2);
        //If Cache is NOT used, this will return
        $dbApi->setNextResult(false );
        $this->assertSame($form2,$cache->get_by_form_id($form2['ID']));

    }

    /**
     * @covers Caldera_Forms_DB_Form_Cache::get_by_form_id()
     * @covers Caldera_Forms_DB_Form_Cache::create()
     */
    public function testGet_all()
    {
        $dbApi = new Db_Mock();
        $cache = new Caldera_Forms_DB_Form_Cache($dbApi);
        $form1 = [
            'ID' => 'cf_tacos',
            'name' => 'Tacos'
        ];
        $cache->create($form1);
        $form2 = [
            'ID' => 'cf_sandwiches',
            'name' => 'nachos'
        ];
        $cache->create($form2);
        //If Cache is NOT used, this will return
        $dbApi->setNextResult(false );
        $forms = $cache->get_all();
        $this->assertTrue(is_array($forms));
    }

    /**
     * @covers Caldera_Forms_DB_Form_Cache::update()
     * @covers Caldera_Forms_DB_Form_Cache::create()
     */
    public function testUpdate()
    {
        $dbApi = new Db_Mock();
        $cache = new Caldera_Forms_DB_Form_Cache($dbApi);
        //If Cache is NOT used, this value will return false and break test
        $dbApi->setNextResult(false );

        $form1 = [
            'ID' => 'cf_tacos',
            'name' => 'Tacos'
        ];
        $cache->create($form1);
        $form2 = [
            'ID' => 'cf_sandwiches',
            'name' => 'nachos'
        ];
        $cache->create($form2);

        $cache->update([
            'ID' => 'cf_sandwiches',
            'name' => 'spatulas'
        ]);

        $this->assertSame([
            'ID' => 'cf_sandwiches',
            'name' => 'spatulas'
        ],$cache->get_by_form_id($form2['ID']));

        $this->assertSame($form2,$cache->get_by_form_id($form2['ID']));
    }

    /**
     * @covers Caldera_Forms_DB_Form_Cache::delete()
     * @covers Caldera_Forms_DB_Form_Cache::delete_by_form_id()
     * @covers Caldera_Forms_DB_Form_Cache::get_all()
     */
    public function testDelete()
    {
        $dbApi = new Db_Mock();
        $cache = new Caldera_Forms_DB_Form_Cache($dbApi);
        //If Cache is NOT used, this value will return false and break test
        $dbApi->setNextResult(false );

        $form1 = [
            'ID' => 'cf_tacos',
            'name' => 'Tacos'
        ];
        $cache->create($form1);
        $form2 = [
            'ID' => 'cf_sandwiches',
            'name' => 'nachos'
        ];
        $cache->create($form2);
        $form3 = $cache->create( [
            'ID' => 'cf_face_palms',
            'name' => 'salads'
        ]);
        $cache->create( [
            'ID' => 'cf_face_palms',
            'name' => 'salads'
        ]);
        $this->assertCount(3, $cache->get_all());

        $cache->delete([$form1['ID'],$form3['ID']));

        $this->assertCount(1, $cache->get_all());
    }

    /**
     * @covers Caldera_Forms_DB_Form_Cache::delete_by_form_id()
     */
    public function testDelete_by_form_id()
    {
        $dbApi = new Db_Mock();
        $cache = new Caldera_Forms_DB_Form_Cache($dbApi);
        //If Cache is NOT used, this value will return false and break test
        $dbApi->setNextResult(false );

        $form1 = [
            'ID' => 'cf_tacos',
            'name' => 'Tacos'
        ];
        $cache->create($form1);
        $form2 = [
            'ID' => 'cf_sandwiches',
            'name' => 'nachos'
        ];
        $cache->create($form2);
        $cache->create( [
            'ID' => 'cf_face_palms',
            'name' => 'salads'
        ]);
        $this->assertCount(3, $cache->get_all());

        $cache->delete_by_form_id($form1['ID');

        $this->assertSame($form2,$cache->get_by_form_id($form2['ID']));
        $this->assertCount(2, $cache->get_all());
    }

}
