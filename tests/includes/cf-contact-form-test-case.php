<?php

/**
 * Class Caldera_Forms_Contact_Form_Test_Case
 *
 * Test case for Contact Form tests
 */
class Caldera_Forms_Contact_Form_Test_Case extends Caldera_Forms_Test_Case
{

    /** @inheritdoc */
    public function tearDown(){
        $this->reset();
        parent::tearDown();
    }

    /** @inheritdoc */
    public function setUp(){
        $this->reset();
        parent::setUp();
    }

    /**
     * ID of last form submitted
     *
     * @since 1.5.9
     *
     * @var string
     */
    protected $form_id;

    /**
     * ID of last entry submitted
     *
     * @since 1.5.9
     *
     * @var integer
     */
    protected $entry_id;

    /**
     * Configuration of last form submitted
     *
     * @since 1.5.9
     *
     * @var array
     */
    protected $form;

    /**
     * Submission data of last submission
     *
     * @since 1.5.9
     *
     * @var array
     */
    protected $submission_data;

    /**
     * Reset test setup
     *
     * @since 1.6.0
     *
     * Nulls all properties and resets mock phpmailer
     */
    protected function reset(){
        $this->entry_id = null;
        $this->form_id = null;
        $this->form = null;
        $this->submission_data = null;
        reset_phpmailer_instance();
    }
}