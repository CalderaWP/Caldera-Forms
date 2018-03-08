<?php
if( ! class_exists( 'Test_File_Mailer' ) ){
    include_once  __DIR__ . '/test-file-mailer.php';
}
/**
 * Class Test_Entry_ID_On_Hooks
 *
 * This test submits the contact form and makes sure the entry ID is correct on all of the hooks it should be correct on.
 */

/**
 * Class Test_Entry_ID_On_Hooks_File
 *
 * This test submits the form with a file field and makes sure the entry ID is correct on all of the hooks it should be correct on.
 */
class Test_Entry_ID_On_Hooks_File extends Test_File_Mailer
{

    /**
     * Tracks number of checks that have run
     *
     * @var int
     */
    protected $checks;

    /** @inheritdoc */
    public function tearDown(){
        $this->checks = 0;
        global $transdata;
        $transdata = null;
        parent::tearDown();
    }

    /**
     * Submit form with file field and make sure the _entry_id field value is set
     *
     * @since 1.6.0
     *
     * @covers Caldera_Forms::process_submission()
     * @covers Caldera_Forms::get_field_data()
     *
     * @group entry
     * @group entry_id
     * @group file
     */
    public function test_get_id(){
        $this->submit_form();
        $this->assertEquals( $this->get_entry_id(), Caldera_Forms::get_field_data( '_entry_id',  $this->form ) );
    }

    /**
     * Submit form with file field and make sure all of the hooks have the
     *
     * @covers Caldera_Forms::process_submission()
     * @covers Caldera_Forms::get_field_data()
     *
     * @group email
     * @group hooks
     * @group entry_id
     * @group file
     *
     * @requires PHP 5.4
     */
    public function test_entry_id_during_submission(){
        //Track number of tests so we can make sure all the hooks fired
        $expected_checks = 1;

        //Capture entry ID
        add_action( 'caldera_forms_entry_saved', function( $entryid ){
           $this->entry_id = $entryid;
        });

        add_action('caldera_forms_submit_process_end',
            function ($form, $referrer, $process_id, $entryid) {
                $this->assertIsNumeric( $entryid );
                $this->check_entry_id( $entryid, 'caldera_forms_submit_process_end' );
                $this->checks++;
                return $form;
            },
            10, 4);

        $expected_checks++;
        add_action('caldera_forms_submit_post_process',
            function ($form, $referrer, $process_id, $entryid) {
                $this->assertIsNumeric( $entryid );
                $this->check_entry_id( $entryid, 'caldera_forms_submit_post_process' );
                $this->checks++;
                return $form;
            },
            10, 4);

        $expected_checks++;
        add_action('caldera_forms_submit_post_process',
            function ($form, $referrer, $process_id, $entryid) {
                $this->assertIsNumeric( $entryid );
                $this->check_entry_id( $entryid, 'caldera_forms_submit_post_process' );
                $this->checks++;
                return $form;
            },
            10, 4);

        $expected_checks++;
        add_action('caldera_forms_submit_post_process_end',
            function ($form, $referrer, $process_id, $entryid) {
                $this->assertIsNumeric( $entryid );
                $this->check_entry_id( $entryid, 'caldera_forms_submit_post_process_end' );
                $this->checks++;
                return $form;
            },
            10, 4);

        $expected_checks++;
        add_action('caldera_forms_submit_complete',
            function ($form, $referrer, $process_id, $entryid) {
                $this->assertIsNumeric( $entryid );
                $this->check_entry_id( $entryid, 'caldera_forms_submit_complete' );
                $this->checks++;
                return $form;
            },
            10, 4);

        $expected_checks++;
        add_action('caldera_forms_mailer',
            function ($mail, $data, $form, $entryid) {
                $this->assertIsNumeric( $entryid );
                $this->check_entry_id( $entryid, 'caldera_forms_mailer' );
                $this->checks++;
                return $mail;
            },
            10, 4);

        $this->submit_form();
        //Make sure all tests ran
        $this->assertSame($expected_checks, $this->checks);

    }

    /**
     * Test that entry ID is correct
     *
     * @since 1.6.0
     *
     * @covers Caldera_Forms::get_field_data()
     *
     * @param int|null|bool $entry_id_from_hook
     *
     * @requires PHP 5.4
     */
    public function check_entry_id( $entry_id_from_hook = null, $hook_name ){
        $_id = $this->get_entry_id();
        $this->assertIsNumeric($_id, $hook_name);
        $this->assertSame($_id, $entry_id_from_hook);
        $this->assertEquals( $_id, $this->entry_id );
        $this->assertIsNumeric($entry_id_from_hook);
        $this->assertEquals( $_id, Caldera_Forms::get_field_data( '_entry_id',  $this->form ) );
    }

    /**
     * Get the right entry ID from transadata
     *
     * @since 1.6.0
     *
     * @return int|null
     */
    protected function get_entry_id(){
        global $transdata;
        if (isset($transdata['entry_id'])) {
            return $transdata['entry_id'];
        } elseif (isset($transdata['data'], $transdata['data']['_entry_id'])) {
            return $transdata['data']['_entry_id'];
        }
        return null;
    }

}