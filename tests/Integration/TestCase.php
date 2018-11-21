<?php


namespace calderawp\calderaforms\Tests\Integration;


use calderawp\calderaforms\Tests\Util\ImportForms;
use calderawp\calderaforms\Tests\Util\Traits\SharedFactories;

class TestCase extends \WP_UnitTestCase
{

    use SharedFactories;
    public function setUp()
    {
        parent::setUp();
    }

    public function tearDown(){
        $this->resetCfGlobals();
        parent::tearDown();
    }

    /**
     * Rest all of the globals that Caldera Forms v1 sets
     *
     * @since 1.8.0
     */
    protected function resetCfGlobals(){
        global $processed_data;
        $processed_data = null;
        unset($GLOBALS['processed_data']);
        global $transdata;
        $transdata = null;
        unset($GLOBALS['transdata']);
        global $form;
        $form = null;
        unset($GLOBALS['form']);
    }
}