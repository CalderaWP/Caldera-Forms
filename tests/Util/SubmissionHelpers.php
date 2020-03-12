<?php


namespace calderawp\calderaforms\Tests\Util;


class SubmissionHelpers
{


    /**
     * Create submission data.
     *
     * USE: Fake POST data when testing form submissions
     *
     * @since 1.8.0
     *
     * @param string $formId The form Id
     * @param array $data Optional. Array of field data
     * @return array
     */
    public static function submission_data($formId, array $data = array())
    {

        $nonce = \Caldera_Forms_Render_Nonce::create_verify_nonce($formId);

        $data = array_merge($data, array(
            '_cf_verify' => $nonce,
            '_wp_http_referer' => '/?page_id=4&preview=1&cf_preview=' . $formId,
            '_cf_frm_id' => $formId,
            '_cf_frm_ct' => '1',
            'cfajax' => $formId,
            '_cf_cr_pst' => '4',
            'email' => '',
            'formId' => $formId,
            'instance' => '1',
            'request' => site_url("/cf-api/$formId"),
            'postDisable' => '0',
            'target' => '#caldera_notices_1',
            'loadClass' => 'cf_processing',
            'loadElement' => '_parent',
            'hiderows' => 'true',
            'action' => 'cf_process_ajax_submit',
            'template' => "#cfajax_$formId-tmpl",
        ));

        return $data;
    }


    /**
     * Add hooks to limit side effects of submission
     *
     * Prevents email and redirects
     *
     * NOTE: is called by self::fakeFormSubmit()
     *
     * @since 1.8.0
     *
     * @param bool $blockEmail Optional. Should email be blocked form sending. Default is true.
     * @param bool $blockAutoResponses Optional. Should auto-responders be blocked form sending. Default is true.
     */
    public static function preventEmailAndRedirect($blockEmail = true, $blockAutoResponses = true){
        //prevent emails
        if ($blockEmail) {
            add_filter('caldera_forms_send_email', '__return_false', 10000);
        }
        if ($blockAutoResponses) {
            add_filter('caldera_forms_autoresponse_mail', '__return_false', 10000);
        }
        //prevent ajax redirect
        remove_action('caldera_forms_redirect', 'cf_ajax_redirect', 10);
        //prevent Caldera Forms from exiting PHP session
        add_filter('caldera_forms_redirect_url_complete', '__return_null', 1000);

    }

    /**
     * Fake a form submission
     *
     * @since 1.8.0
     *
     * @param string $formId The form Id
     * @param array $data Array of field data
     * @param bool $blockEmail Optional. Should email be blocked form sending. Default is true.
     * @param bool $blockAutoResponses Optional. Should auto-responders be blocked form sending. Default is true.
     */
    public static function fakeFormSubmit($formId,array $data,$blockEmail = true, $blockAutoResponses = true){
        static::preventEmailAndRedirect($blockEmail,$blockAutoResponses);
        /**
         * @var \WP_Query
         */
        global $wp_query;
        $wp_query->query_vars[ 'cf_api' ] = $formId;
        $_SERVER['HTTP_REFERER'] = $data['_wp_http_referer'];
        $_POST = $data;
        \Caldera_Forms::process_submission();
    }

    /**
     * Create a saved entry for a form with random field data or supplied field data.
     *
     * @since 1.8.10
     *
     * @param array $form Form config
     * @param array $data Optional. Data to save. If empty, all fields' values'set to random strings.
     * @return array
     */
    public static function createEntry(array $form, array $data = [])
    {


        if (empty($data)) {
            $data = array();
            $i = 0;
            foreach ($form['fields'] as $field_id => $field_config) {
                if (1 == $i) {
                    $data[$field_id] = $field_id . '_' . rand();
                } else {
                    $data[$field_id] = array(
                        rand(),
                        5 => rand(),
                        rand(),
                        'batman'
                    );
                }
                if (0 == $i) {
                    $i = 1;
                } else {
                    $i = 0;
                }
            }
        }

        $entry_id = \Caldera_Forms_Save_Final::create_entry($form, $data);
        return [
            'id' => $entry_id,
            'field_data' => $data,
            'form_id' => $form['ID'],
       ];
    }
}
