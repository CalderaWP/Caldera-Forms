<?php


namespace calderawp\calderaforms\Tests\Integration;


class EnvironmentTest extends TestCase
{

    /**
     * Make sure WordPress database is operable
     *
     * @since 1.8.0
     */
    public function testCanInsertPost()
    {
        $this->assertTrue(is_numeric(wp_insert_post(['post_title' => 'LOl', 'post_content' => 'LOL' ] ) ) );
    }


}
