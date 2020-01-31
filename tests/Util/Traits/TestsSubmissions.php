<?php


namespace calderawp\calderaforms\Tests\Util\Traits;

/**
 * Trait TestsSubmissions
use calderawp\calderaforms\Tests\Util\Traits\TestsSubmissions;

class WhateverTest extends TestCase
{

    use TestsSubmissions;

    public function setUp()
    {
        add_action('caldera_forms_entry_saved', [$this, 'entrySaved'] );
        parent::setUp();
    }

    public function tearDown(){
        $this->entryId = null;
        remove_action('caldera_forms_entry_saved', [$this, 'entrySaved'] );
        parent::tearDown();
    }
 }

*/

trait TestsSubmissions
{


    /**
     * Entry ID for current submission
     *
     * @since 1.8.0
     *
     * @var int
     */
    protected $entryId;

    /**
     * Capture entry ID when it is saved
     *
     * @since 1.8.0
     *
     * @uses "caldera_forms_entry_saved" hook
     *
     * @param string $entryId
     */
    public function entrySaved($entryId)
    {
        $this->entryId = $entryId;
    }

}