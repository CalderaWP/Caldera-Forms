<?php

namespace calderawp\calderaforms\Tests\Unit\Database;


use calderawp\calderaforms\Tests\Integration\TestCase;

/**
 * Class Caldera_Forms_DB_Form_CacheTest
 *
 * Tests the form cache and its integration with Caldera_Forms_Forms class
 */
class Caldera_Forms_DB_Form_CacheTest extends TestCase
{
    /**
     * Tests something, unclear what
     */
    public function testMockSystem()
    {
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

        //This is a db result so expect form_id not ID.
        $this->assertSame($form1Id, $formFromQuery1['form_id']);
        $this->assertSame($form1Id, $formFromQuery2['form_id']);

        $wpdb->last_query = null;
        $form2fromQuery = $cache->get_by_form_id($form2Id);
        $this->assertFalse(is_null($wpdb->last_query));
        $this->assertSame($form2Id, $form2fromQuery['form_id']);

        $this->deleteAllForms();

    }

    /**
     * @group now
     *
     * @covers Caldera_Forms_DB_Form_Cache::delete()
     * @covers Caldera_Forms_DB_Form_Cache::get_by_form_id()
     * @covers Caldera_Forms_DB_Form_Cache::create()
     */
    public function testCreateGetAll()
    {
        $this->deleteAllForms();

        $dbApi = \Caldera_Forms_DB_Form::get_instance();
        $cache = new \Caldera_Forms_DB_Form_Cache($dbApi);
        $this->assertFalse(\Caldera_Forms_DB_Form::get_instance()->get_all());
        $this->assertFalse($cache->get_all());
        $this->assertCount(0, \Caldera_Forms_Forms::get_forms());
        $this->importFormWithAutoResponder();

        $this->assertCount(1, \Caldera_Forms_DB_Form::get_instance()->get_all());
        $this->assertCount(1, $cache->get_all());
        $this->assertCount(1, \Caldera_Forms_Forms::get_forms());
        $this->importFormWithAutoResponder();
        $this->assertCount(2, \Caldera_Forms_DB_Form::get_instance()->get_all());
        $this->assertCount(2, \Caldera_Forms_Forms::get_forms());

        $this->deleteAllForms();

    }

    /**
     * @covers Caldera_Forms_DB_Form_Cache::get_by_form_id()
     * @covers Caldera_Forms_DB_Form_Cache::update()
     * @covers Caldera_Forms_DB_Form_Cache::create()
     */
    public function testUpdate()
    {
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
        //This is a db result so expect form_id not ID and config is a key
        $this->assertSame($form2Id, $formFromQuery2['form_id']);
        $this->assertSame($form2Id, $formFromQuery2['config']['ID']);
        $this->assertSame($save['name'], $formFromQuery2['config']['name']);
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
        $dbApi = \Caldera_Forms_DB_Form::get_instance();
        $form1Id = $this->importFormWithAutoResponder();
        $form2Id = $this->importFormWithAutoResponder();
        $this->assertCount(2, $dbApi->get_all());

        \Caldera_Forms_Forms::delete_form($form1Id);

        global $wpdb;
        $cache = new \Caldera_Forms_DB_Form_Cache($dbApi);

        $this->assertFalse($cache->get_by_form_id($form1Id));
        $this->assertFalse(\Caldera_Forms_Forms::get_form($form1Id));

        $this->deleteAllForms();

    }


}
