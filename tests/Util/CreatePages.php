<?php


namespace calderawp\calderaforms\Tests\Util;


use calderawp\calderaforms\pro\exceptions\Exception;

class CreatePages
{


    /** @var string */
    protected $filePath;

    /**
     * AddPages constructor.
     *
     * @param string $filePath Optional. Path to the JSON file with the forms and pages
     */
    public function __construct( $filePath = '')
    {
        if (empty($filePath)) {
            $filePath = dirname(__FILE__, 2) . '/cypress/tests.json';
        }
        $this->filePath = $filePath;
    }


    /**
     * Create pages for test forms
     *
     * @return int
     */
    public function import()
    {

        $data = json_decode(file_get_contents($this->filePath), true);
        $testForms = $data['forms'];

        $created = [];
        $contentPattern =  '[caldera_forms id="%s"]';

        foreach ($testForms as $testForm) {
            $formId = $testForm['formId'];
            $pageSlug = $testForm['pageSlug'];
            $page = get_page_by_path($pageSlug, OBJECT);
            if( isset( $page ) ){
                continue;
            }
            $form = \Caldera_Forms_Forms::get_form($formId);
            if( isset( $form[ 'name' ]) ){

                if ( ! isset($page) ){
                    $created_page = wp_insert_post(
                        [
                            'post_name' => $pageSlug,
                            'post_type' => 'page',
                            'post_title' => $form[ 'name' ],
                            'post_content' => sprintf($contentPattern,$formId),
                            'post_status' => 'publish'
                        ]

                    );
                    if( ! is_wp_error( $page ) ){
                        $created[] = $created_page;
                    }
                }
            }

        }

        return count($created);

    }


}