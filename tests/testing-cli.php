<?php

if( class_exists( 'WP_CLI' ) ){

    /**
     * Import forms command
     *
     * @since 1.8.0
     *
     * @param $args
     */
    function calderaFormsImportTestFormsCommand( $args ) {
        $filePath = file_exists($args[0]) ? $args[0]: dirname(__FILE__, 2) . '/cypress/forms';
        $importer = new \calderawp\calderaforms\Tests\Util\ImportForms($filePath);
        WP_CLI::success( sprintf( 'Forms imported: %d', $importer->import() ) );
    }
    WP_CLI::add_command( 'cf import-test-forms', 'calderaFormsImportTestFormsCommand' );

    /**
     * Create pages command
     *
     * @since 1.8.0
     *
     * @param $args
     */
    function calderaFormsCreatePagesCommand( $args ) {
        $filePath = file_exists($args[0]) ? $args[0]: dirname(__FILE__, 2) . '/cypress/tests.json';
        $importer = new \calderawp\calderaforms\Tests\Util\CreatePages($filePath);
        WP_CLI::success( sprintf( 'Pages added: %d', $importer->import() ) );
    }
    WP_CLI::add_command( 'cf create-test-pages', 'calderaFormsCreatePagesCommand' );

    /**
     * Delete test forms command
     *
     * @since 1.8.0
     *
     * @param $args
     */
    function calderaFormsDeleteTestFormsCommand( $args ) {
        $filePath = file_exists($args[0]) ? $args[0]: dirname(__FILE__, 2) . '/cypress/forms';
        $deleted = [];
        foreach (\calderawp\calderaforms\Tests\Util\TestForms::getTestFormIds($filePath) as $formId) {
            $deleted[] = Caldera_Forms_Forms::delete_form( $formId );
        }

        WP_CLI::success( sprintf( 'Forms deleted: %d', count($deleted) ) );
    }
    WP_CLI::add_command( 'cf delete-test-forms', 'calderaFormsDeleteTestFormsCommand' );


    /**
     * Delete test pages command
     *
     * @since 1.8.0
     *
     * @param $args
     */
    function calderaFormsDeleteTestPagesCommand( $args ) {
        $filePath = file_exists($args[0]) ? $args[0]: dirname(__FILE__, 2) . '/cypress/tests.json';
        $deleted = [];
        foreach (\calderawp\calderaforms\Tests\Util\TestForms::getTestPages($filePath) as $formId => $pageSlug) {
            $page = get_page_by_path($pageSlug, OBJECT);
            if( ! isset( $page ) ){
                continue;
            }
            $deleted[]  = wp_delete_post( $page->ID, true );

        }

        WP_CLI::success( sprintf( 'Pages deleted: %d', count($deleted) ) );
    }




    WP_CLI::add_command( 'cf delete-test-pages', 'calderaFormsDeleteTestPagesCommand' );
}
