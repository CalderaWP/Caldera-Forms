<?php


namespace calderawp\calderaforms\Tests\Integration\RestApi;


use calderawp\calderaforms\Tests\Integration\TestCase;

abstract class RestApiTestCase extends TestCase
{

    /**
     * Copied from \WP_Test_REST_Controller_Testcase
     *
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();
        add_filter('rest_url', array( $this, 'filter_rest_url_for_leading_slash' ), 10, 2);
        /** @var \WP_REST_Server $wp_rest_server */
        global $wp_rest_server;
        $wp_rest_server = new \WP_REST_Server;
        do_action('rest_api_init', $wp_rest_server);
    }

    /**
     * Copied from \WP_Test_REST_Controller_Testcase
     *
     * @inheritdoc
     */
    public function tearDown()
    {
        parent::tearDown();
        remove_filter('rest_url', array( $this, 'test_rest_url_for_leading_slash' ), 10, 2);
        /** @var \WP_REST_Server $wp_rest_server */
        global $wp_rest_server;
        $wp_rest_server = null;
    }

    public function filter_rest_url_for_leading_slash($url, $path)
    {
        if (is_multisite()) {
            return $url;
        }

        // Make sure path for rest_url has a leading slash for proper resolution.
        $this->assertTrue(0 === strpos($path, '/'), 'REST API URL should have a leading slash.');

        return $url;
    }
}
// phpcs:enable
