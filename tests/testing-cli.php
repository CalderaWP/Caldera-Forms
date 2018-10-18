<?php

if( class_exists( 'WP_CLI' ) ){

    /**
     * Import forms command
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
     * @param $args
     */
    function calderaFormsCreatePagesCommand( $args ) {
        $filePath = file_exists($args[0]) ? $args[0]: dirname(__FILE__, 2) . '/cypress/tests.json';
        $importer = new \calderawp\calderaforms\Tests\Util\CreatePages($filePath);
        WP_CLI::success( sprintf( 'Pages added: %d', $importer->import() ) );
    }
    WP_CLI::add_command( 'cf create-test-pages', 'calderaFormsCreatePagesCommand' );
}
