<?php

namespace calderawp\calderaforms\Tests\Unit\Database;


use calderawp\calderaforms\Tests\Integration\TestCase;

class Db_Mock implements \Caldera_Forms_DB_Form_Interface
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
     * Tests something, unclear what
     */
    public function testMockSystem()
    {
        $this->deleteAllForms();
        $dbApi = \Caldera_Forms_DB_Form::get_instance();
        $form1Id = $this->importFormWithAutoResponder();
        $form2Id = $this->importFormWithAutoResponder();
        $this->assertCount(2, $dbApi->get_all());
        $this->deleteAllForms();

    }

    /**
     * @covers Caldera_Forms_DB_Form_Cache::get_by_form_id()
     * @covers Caldera_Forms_DB_Form_Cache::create()
     */
    public function testGet_by_form_id()
    {
        $this->deleteAllForms();
        $dbApi = \Caldera_Forms_DB_Form::get_instance();
        $form1Id = $this->importFormWithAutoResponder();
        $form2Id = $this->importFormWithAutoResponder();
        $this->assertCount(2, $dbApi->get_all());


        global $wpdb;
        $cache = new \Caldera_Forms_DB_Form_Cache($dbApi);
        //Search with empty cache
        $formFromQuery1 = $cache->get_by_form_id($form1Id);
        //Ran query to get from db, so query was ran
        $this->assertFalse(is_null($wpdb->last_query));

        //Clear wpdb then query for the same form again
        $wpdb->last_query = null;
        $formFromQuery2 = $cache->get_by_form_id($form1Id);
        //No query ran
        $this->assertTrue(is_null($wpdb->last_query));
        $this->assertSame($form1Id, $formFromQuery1['ID']);
        $this->assertSame($form1Id, $formFromQuery2['ID']);
        $this->assertSame($formFromQuery1['name'], $formFromQuery2['name']);

        $wpdb->last_query = null;
        $form2fromQuery = $cache->get_by_form_id($form2Id);
        $this->assertFalse(is_null($wpdb->last_query));
        $this->assertSame($form2Id, $form2fromQuery['ID']);

        $this->deleteAllForms();

    }

    /**
     * @covers Caldera_Forms_DB_Form_Cache::get_by_form_id()
     * @covers Caldera_Forms_DB_Form_Cache::update()
     * @covers Caldera_Forms_DB_Form_Cache::create()
     */
    public function testUpdate()
    {
        $this->deleteAllForms();
        $dbApi = \Caldera_Forms_DB_Form::get_instance();
        $form1Id = $this->importFormWithAutoResponder();
        $form2Id = $this->importFormWithAutoResponder();
        $this->assertCount(2, $dbApi->get_all());

        $form2 = \Caldera_Forms_Forms::get_form($form2Id);
        $save = $form2;
        $save['name'] = 'Aftermath';
        \Caldera_Forms_Forms::save_form($save);

        global $wpdb;
        $cache = new \Caldera_Forms_DB_Form_Cache($dbApi);
        //Search with empty cache
        $cache->get_by_form_id($form2Id);
        //Ran query to get from db, so query was ran
        $this->assertFalse(is_null($wpdb->last_query));

        //Clear wpdb then query for the same form again
        $wpdb->last_query = null;
        $formFromQuery2 = $cache->get_by_form_id($form2Id);
        //No query ran
        $this->assertTrue(is_null($wpdb->last_query));
        $this->assertSame($form2Id, $formFromQuery2['ID']);
        $this->assertSame($save['name'], $formFromQuery2['name']);
        $this->deleteAllForms();

    }


    /**
     * @covers Caldera_Forms_DB_Form_Cache::get_by_form_id()
     * @covers Caldera_Forms_DB_Form_Cache::update()
     * @covers Caldera_Forms_DB_Form_Cache::create()
     * @covers Caldera_Forms_DB_Form_Cache::delete()
     * @covers Caldera_Forms_DB_Form_Cache::delete_by_form_id()
     */
    public function testDelete()
    {
        $this->deleteAllForms();
        $dbApi = \Caldera_Forms_DB_Form::get_instance();
        $form1Id = $this->importFormWithAutoResponder();
        $form2Id = $this->importFormWithAutoResponder();
        $this->assertCount(2, $dbApi->get_all());

        \Caldera_Forms_Forms::delete_form($form1Id);

        global $wpdb;
        $cache = new \Caldera_Forms_DB_Form_Cache($dbApi);

        $this->assertFalse( $cache->get_by_form_id($form1Id) );
        $this->assertFalse( \Caldera_Forms_Forms::get_form($form1Id) );

        $this->deleteAllForms();

    }



}
