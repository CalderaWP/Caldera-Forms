<?php

/**
 * Test the basics of the DB Abstractions with a mock class
 *
 * @package   Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Test_Caldera_Forms_DB_Base_Abstraction extends Caldera_Forms_Test_Case
{

    /**
     * @var Caldera_Forms_Fake_DB
     */
    protected $db_instance;

    public function setUp()
    {
        parent::setUp();
        $this->db_instance = Caldera_Forms_Fake_DB::get_instance();
    }

    /**
     * Test table name
     *
     * @since 1.3.5
     *
     * @covers Caldera_Forms_DB_Base::get_table_name()
     *
     * @group db
     * @group db_abstraction
     */
    public function test_table_name()
    {
        global $wpdb;
        $this->assertSame($wpdb->prefix . 'cf_db_abstraction_test', $this->db_instance->get_table_name(false));
    }

    /**
     * Test meta table name
     *
     * @since 1.3.5
     *
     * @covers Caldera_Forms_DB_Base::get_table_name()
     *
     * @group db
     * @group db_abstraction
     */
    public function test_meta_table_name()
    {
        global $wpdb;
        $this->assertSame($wpdb->prefix . 'cf_db_abstraction_test_meta', $this->db_instance->get_table_name(true));
    }

    /**
     * Test writing primary fields
     *
     * @since 1.3.5
     *
     * @covers Caldera_Forms_DB_Base::create()
     * @covers Caldera_Forms_DB_Base::save()
     *
     * @group db
     * @group db_abstraction
     */
    public function test_write_primary()
    {
        $data = array(
            'a_id' => 1,
            'b_id' => 'brooms'
        );
        $id = $this->db_instance->create($data);
        $this->assertTrue(is_numeric($id));
        global $wpdb;
        $table_name = $this->db_instance->get_table_name(false);
        $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE `ID` = %d", $id);
        $results = $wpdb->get_results($sql, ARRAY_A);
        $expected = $data;
        $expected['ID'] = $id;
        $this->assertEquals($expected, $results[0]);
    }

    /**
     * Test writing primary and meta fields
     *
     * @since 1.3.5
     *
     * @covers Caldera_Forms_DB_Base::create()
     * @covers Caldera_Forms_DB_Base::save()
     *
     * @group db
     * @group db_abstraction
     */
    public function test_write_primary_and_meta()
    {
        $data = array(
            'a_id' => 2,
            'b_id' => 'shirts',
            'string' => 'hats',
            'integer' => '99'
        );
        $id = $this->db_instance->create($data);
        $this->assertTrue(is_numeric($id));

        //check primary
        global $wpdb;
        $table_name = $this->db_instance->get_table_name(false);
        $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE `ID` = %d", $id);
        $results = $wpdb->get_results($sql, ARRAY_A);
        $expected = $data;
        $expected['ID'] = $id;
        unset($expected['string']);
        unset($expected['integer']);
        $this->assertEquals($expected, $results[0]);

        //check meta
        $table_name = $this->db_instance->get_table_name(true);
        $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE `a_id` = %d", $id);
        $results = $wpdb->get_results($sql, ARRAY_A);
        $this->assertSame(2, count($results));
        foreach ($results as $result) {
            $this->assertArrayHasKey('meta_id', $result);
            $this->assertArrayHasKey('a_id', $result);
            $this->assertArrayHasKey('meta_key', $result);
            $this->assertArrayHasKey('meta_value', $result);
            if ('string' == $result['meta_key']) {
                $this->assertSame('hats', $result['meta_value']);
            }

            if ('integer' == $result['meta_key']) {
                $this->assertSame('99', $result['meta_value']);
            }
        }

    }

    /**
     * Test reading primary fields
     *
     * @since 1.3.5
     *
     * @covers Caldera_Forms_DB_Base::get_primary()
     *
     * @group db
     * @group db_abstraction
     */
    public function test_read_primary()
    {
        $data = array(
            'a_id' => 555,
            'b_id' => '1',
            'string' => 'sdfsdfdsf',
            'integer' => '96669'
        );
        $id = $this->db_instance->create($data);
        $read = $this->db_instance->get_primary($id);
        $this->assertInternalType('array', $read);
        $this->assertArrayHasKey('a_id', $read);
        $this->assertArrayHasKey('b_id', $read);
        $this->assertFalse(array_key_exists('string', $read));
        $this->assertFalse(array_key_exists('integer', $read));
        $this->assertEquals($data['a_id'], $read['a_id']);
        $this->assertEquals($data['b_id'], $read['b_id']);
    }

    /**
     * Test reading meta fields
     *
     * @since 1.3.5
     *
     * @covers Caldera_Forms_DB_Base::get_meta()
     *
     * @group db
     * @group db_abstraction
     */
    public function test_read_meta()
    {
        $data = array(
            'a_id' => 555,
            'b_id' => '1',
            'string' => 'tophat',
            'integer' => '11'
        );
        $id = $this->db_instance->create($data);
        $metas = $this->db_instance->get_meta($id);
        $fields = $this->db_instance->get_fields();
        foreach ($metas as $meta) {
            foreach ($fields['meta_fields'] as $meta_field) {
                $this->assertArrayHasKey($meta_field, $meta);
            }
            $this->assertTrue(in_array($meta['meta_key'], $fields['meta_keys']));
            if ('string' == $meta['meta_key']) {
                $this->assertSame('tophat', $meta['meta_value']);
            }

            if ('integer' == $meta['meta_key']) {
                $this->assertSame('11', $meta['meta_value']);
            }
        }

    }

    /**
     * Test deleting meta
     *
     * @since 1.3.5
     *
     * @covers Caldera_Forms_DB_Base::delete()
     *
     * @group db
     * @group db_abstraction
     */
    public function test_delete()
    {
        $data = array(
            'a_id' => 555,
            'b_id' => '1',
            'string' => 'tophat',
            'integer' => '11'
        );
        $id = $this->db_instance->create($data);
        $metas = $this->db_instance->get_meta($id);
        $deleted = $this->db_instance->delete($id);
        $this->assertTrue($deleted);

        global $wpdb;
        $table_name = $this->db_instance->get_table_name(false);
        $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE `ID` = %d", $id);
        $results = $wpdb->get_results($sql, ARRAY_A);
        $this->assertEmpty($results);

        $table_name = $this->db_instance->get_table_name(true);
        foreach ($metas as $meta) {
            $meta_id = $meta['meta_id'];
            $this->db_instance->delete($meta_id);
            $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE `meta_id` = %d", $id);
            $results = $wpdb->get_results($sql, ARRAY_A);
            $this->assertEmpty($results);
        }

    }

    /**
     * Test that invalid meta keys can not be saved
     *
     * @since 1.3.5
     *
     * @covers Caldera_Forms_DB_Base::valid_field()
     * @covers Caldera_Forms_DB_Base::save()
     *
     * @group db
     * @group db_abstraction
     */
    public function test_invalid_meta()
    {
        $data = array(
            'a_id' => 2,
            'b_id' => 'shirts',
            'string' => 'hat',
            'integer' => '991',
            'bad' => 'not good',
            'faces' => 'not allowed'
        );
        $id = $this->db_instance->create($data);
        $this->assertTrue(is_numeric($id));


        $metas = $this->db_instance->get_meta($id);
        $fields = $this->db_instance->get_fields();
        foreach ($metas as $meta) {
            foreach ($fields['meta_fields'] as $meta_field) {
                $this->assertArrayHasKey($meta_field, $meta);
            }
            $this->assertTrue(in_array($meta['meta_key'], $fields['meta_keys']));
            if ('string' == $meta['meta_key']) {
                $this->assertSame('hat', $meta['meta_value']);
            }

            if ('integer' == $meta['meta_key']) {
                $this->assertSame('991', $meta['meta_value']);
            }
        }

    }

    /**
     * Test reading multiple primary fields
     *
     * @since 1.3.5
     *
     * @covers Caldera_Forms_DB_Base::get_primary()
     *
     * @group db
     * @group db_abstraction
     */
    public function test_bulk_primary()
    {
        $not_queried_for = array(
            'a_id' => 155,
            'b_id' => 617,
            'string' => 7118,
            'integer' => 11111,
        );
        $this->db_instance->create($not_queried_for);

        $first = array(
            'a_id' => 1111,
            'b_id' => 1111,
            'string' => 444,
            'integer' => 222,
        );

        $second = array(
            'a_id' => 55,
            'b_id' => 67,
            'string' => 78,
            'integer' => 11,
        );

        $first_id = $this->db_instance->create($first);
        $this->db_instance->create($not_queried_for);
        $second_id = $this->db_instance->create($second);
        $this->db_instance->create($not_queried_for);

        $first_primary = array(
            'ID' => $first_id,
            'a_id' => $first['a_id'],
            'b_id' => $first['b_id'],
        );

        $second_primary = array(
            'ID' => $second_id,
            'a_id' => $second['a_id'],
            'b_id' => $second['b_id'],
        );

        $ids = array($first_id, $second_id);
        $results = $this->db_instance->get_primary($ids);

        $this->assertSame(2, count($results));
        $this->assertEquals($first_primary, $results[0]);
        $this->assertEquals($second_primary, $results[1]);
    }

    /**
     * Test reading multiple meta fields
     *
     * @since 1.3.5
     *
     * @covers Caldera_Forms_DB_Base::get_meta()
     *
     * @group db
     * @group db_abstraction
     */
    public function test_multiple_meta()
    {

        $not_queried_for = array(
            'a_id' => rand(),
            'b_id' => 617,
            'string' => 7118,
            'integer' => 11111,
        );

        $first = array(
            'a_id' => 1111,
            'b_id' => 1111,
            'string' => 444,
            'integer' => 222,
        );

        $second = array(
            'a_id' => 55,
            'b_id' => 67,
            'string' => 78,
            'integer' => 11,
        );
        $this->db_instance->create($not_queried_for);
        $first_id = $this->db_instance->create($first);
        $this->db_instance->create($not_queried_for);
        $second_id = $this->db_instance->create($second);
        $this->db_instance->create($not_queried_for);

        $ids = array($first_id, $second_id);
        $results = $this->db_instance->get_primary($ids);

        $this->assertSame(2, count($results));

        $metas = $this->db_instance->get_meta($ids);
        $this->assertSame(4, count($metas));

    }

    /**
     * Test getting specific meta key
     *
     * @since 1.3.5
     *
     * @covers Caldera_Forms_DB_Base::get_meta()
     * @covers Caldera_Forms_DB_Base::reduce_meta()
     *
     * @group db
     * @group db_abstraction
     */
    public function test_get_meta_key()
    {
        $data = array(
            'a_id' => 1111,
            'b_id' => 1111,
            'string' => 'farsdfdfa',
            'integer' => 222,
        );

        $id = $this->db_instance->create($data);
        $key = $this->db_instance->get_meta($id, 'string');
        $this->assertSame($data['string'], $key);
        $key = $this->db_instance->get_meta($id, 'integer');
        $this->assertEquals($data['integer'], $key);
    }

    /**
     * Test getting a complete record
     *
     * @since 1.3.5
     *
     * @covers Caldera_Forms_DB_Base::get_row()
     * @covers Caldera_Forms_DB_Base::get_primary()
     * @covers Caldera_Forms_DB_Base::get_meta()
     * @covers Caldera_Forms_DB_Base::reduce_meta()
     *
     * @group db
     * @group db_abstraction
     */
    public function test_full_record()
    {
        $not_queried_for = array(
            'a_id' => 11111111111,
            'b_id' => 617,
            'string' => 7118,
            'integer' => rand(),
        );
        $this->db_instance->create($not_queried_for);

        $data = array(
            'a_id' => 1111,
            'b_id' => 1111,
            'string' => 444,
            'integer' => 222,
        );

        $id = $this->db_instance->create($data);
        $data['ID'] = $id;
        $record = $this->db_instance->get_record($id);
        $this->assertEquals($data, $record);


    }

    /**
     * Test getting multiple complete records
     *
     * @since 1.3.5
     *
     * @covers Caldera_Forms_DB_Base::get_row()
     * @covers Caldera_Forms_DB_Base::get_primary()
     * @covers Caldera_Forms_DB_Base::get_meta()
     * @covers Caldera_Forms_DB_Base::reduce_meta()
     *
     * @group db
     * @group db_abstraction
     */
    public function test_full_records()
    {

        $not_queried_for = array(
            'a_id' => 11111111111,
            'b_id' => 617,
            'string' => 7118,
            'integer' => rand(),
        );
        $this->db_instance->create($not_queried_for);

        $first = array(
            'a_id' => 1111,
            'b_id' => 1111,
            'string' => 444,
            'integer' => 222,
        );

        $second = array(
            'a_id' => 55,
            'b_id' => 67,
            'string' => 78,
            'integer' => 11,
        );
        $this->db_instance->create($not_queried_for);
        $first_id = $this->db_instance->create($first);
        $this->db_instance->create($not_queried_for);
        $second_id = $this->db_instance->create($second);
        $this->db_instance->create($not_queried_for);

        $ids = array($first_id, $second_id);

        $records = $this->db_instance->get_record($ids);
        $this->assertSame(2, count($records));

    }

    /**
     * Test getting highest ID
     *
     * @since 1.3.5
     *
     * @covers Caldera_Forms_DB_Base::highest_id()
     *
     * @group db
     * @group db_abstraction
     */
    public function test_highest_id()
    {
        for ($i = 0; $i <= rand(7, 12); $i++) {
            $data = array(
                'a_id' => rand(),
                'b_id' => rand(),
                'string' => rand(),
                'integer' => rand(),
            );
            $this->db_instance->create($data);
        }
        $id = $this->db_instance->create($data);

        $this->assertEquals($id, $this->db_instance->highest_id());

    }


}

include_once CFCORE_PATH . 'classes/db/base.php';

class Caldera_Forms_Fake_DB extends Caldera_Forms_DB_Base
{


    /**
     * Primary fields
     *
     * @since 1.3.5
     *
     * @var array
     */
    protected $primary_fields = array(
        'a_id' => array(
            '%s',
            'absint'
        ),
        'b_id' => array(
            '%s',
            'strip_tags'
        )

    );

    /**
     * Meta fields
     *
     * @since 1.3.5
     *
     * @var array
     */
    protected $meta_fields = array(
        'a_id' => array(
            '%d',
            'absint',
        ),
        'meta_key' => array(
            '%s',
            'strip_tags',
        ),
        'meta_value' => array(
            '%s',
            'strip_tags',
        ),
    );

    /**
     * Meta keys
     *
     * @since 1.3.5
     *
     * @var array
     */
    protected $meta_keys = array(
        'string' => array(
            '%s',
            'strip_tags',
        ),
        'integer' => array(
            '%d',
            'absint',
        )
    );

    /**
     * Name of primary index
     *
     * @since 1.3.5
     *
     * @var string
     */
    protected $index = 'a_id';

    /**
     * Name of table
     *
     * @since 1.3.5
     *
     * @var string
     */
    protected $table_name = 'cf_db_abstraction_test';

    /**
     * Class instance
     *
     * @since 1.3.5
     *
     * @var Caldera_Forms_Fake_DB
     */
    private static $instance;

    /**
     *
     *
     * @since 1.3.5
     */
    protected function __construct()
    {

    }

    /**
     * Get class instance
     *
     * @since 1.3.5
     *
     * @return \Caldera_Forms_Fake_DB
     */
    public static function get_instance()
    {
        if (null == self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;

    }

}
