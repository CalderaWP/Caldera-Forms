<?php

/**
 * Class Caldera_Forms_GDPR
 *
 * Connects WordPress GDPR compliance tools to Caldera Forms APIs.
 */
class Caldera_Forms_GDPR
{

    /**
     * Forms with a GDPR exporter enabled
     *
     * @since 1.7.0
     *
     * @var array
     */
    protected static $enabled_forms;

    /**
     * Register GDPR compliance tools
     *
     * @since 1.7.0
     */
    public static function register_gdpr()
    {

        if (!empty(static::enabled_forms())) {

            add_filter('wp_privacy_personal_data_erasers', function ($erasers) {

                foreach (static::enabled_forms() as $form_id) {
                    $form = Caldera_Forms_Forms::get_form($form_id);
                    if (is_array($form)) {
                        $erasers[self::group_id($form)] = [
                            'eraser_friendly_name' => $form['name'],
                            'callback' => self::callback($form_id,'eraser'),
                        ];
                    }
                }
                return $erasers;
            });

            add_filter('wp_privacy_personal_data_exporters', function ($exporters) {

                foreach (static::enabled_forms() as $form_id) {
                    $form = Caldera_Forms_Forms::get_form($form_id);
                    if (is_array($form)) {
                        $exporters[self::group_id($form)] = [
                            'exporter_friendly_name' => $form['name'],
                            'callback' => self::callback($form_id,'exporter'),
                        ];
                    }
                }

                return $exporters;
            });
        }

    }

    /**
     * Get the name of the callback function for registered exporter or eraser
     *
     * @since 1.7.0
     *
     * @param string $form_id
     * @param string $type Optional. Type of process. Default is 'exporter', could be 'eraser'.
     * @return callable|array|string
     */
    protected static function callback($form_id, $type = 'exporter')
    {
        /**
         * Modify callback function for data exporter or eraser
         *
         * @since 1.7.0
         *
         * @param callable|array|string $callback Callbale to use for export/erase
         * @param string $type Type of process. exporter|eraser
         */
        return apply_filters( 'caldera_forms_gdpr_callback', [__CLASS__, static::callback_name($form_id, $type )], $type, $form_id );
    }

    /**
     * Get the name of the magic callback function used to process export/erases
     *
     * @since 1.7.0
     *
     * @param string $form_id The ID of the form to process.
     * @param string $type Optional. Type of callback. Default is "exporter", options: exporter|eraser
     * @return string
     */
    public static function callback_name($form_id, $type = 'exporter')
    {
        switch ($type) {
            case 'eraser':
                $name = $type . $form_id;
                break;
            case 'exporter':
            case 'default':
                $name = 'exporter' . $form_id;
                break;
        }
        return $name;
    }

    /**
     * Get an array of forms that have a data exporter enabled
     *
     * @since 1.7.0
     *
     * @param bool $reset Optional. If true, database will be queried for results. If false, the default, database will only be queried on first call of function/
     * @return array
     */
    public static function enabled_forms($reset = false)
    {
        if (false === $reset && !empty(static::$enabled_forms)) {
            return static::$enabled_forms;
        }

        $forms = Caldera_Forms_Forms::get_forms(false);
        if ( ! empty($forms)) {
            foreach ($forms as $form_id) {
                $form = Caldera_Forms_Forms::get_form($form_id);
                if (is_array($form) && Caldera_Forms_Forms::is_privacy_export_enabled($form)) {
                    static::$enabled_forms[] = $form_id;
                }
            }
        } else {
            static::$enabled_forms = [];
        }

        return static::$enabled_forms;
    }

    /**
     * Routes exporter and eraser callbacks to the right functions with the right form
     *
     * @inheritdoc
     *
     * @since 1.7.0
     */
    public static function __callStatic($name, $arguments)
    {
        if (0 === strpos($name, 'exporter')) {
            $form_id = str_replace('exporter', '', $name);
            $form = Caldera_Forms_Forms::get_form($form_id);
            return static::get_export_data($arguments[0], $form, $arguments[1]);
        }


        if (0 === strpos($name, 'eraser')) {
            $form_id = str_replace('eraser', '', $name);
            $form = Caldera_Forms_Forms::get_form($form_id);
            return static::perform_erase($arguments[0], $form);
        }

    }

    /**
     * Get group ID for data set
     *
     * @since 1.7.0
     *
     * @param array $form The form configuration
     * @return string
     */
    public static function group_id(array $form)
    {
        return 'caldera-forms-' . sanitize_title_with_dashes($form['name']);
    }

    /**
     * Get the group label
     *
     * @since 1.7.0
     *
     * @param array $form The form configuration
     * @return string
     */
    public static function group_label(array $form)
    {
        return $form['name'];
    }


    /**
     * Process one page of export data
     *
     * @since 1.7.0
     *
     * @param string $email_address Email address to search PII by
     * @param array $form The form configuration
     * @param int $page Optional. Page of results to get. Default is 1.
     * @return array
     */
    public static function get_export_data($email_address, array $form, $page = 1)
    {
        if (!Caldera_Forms_Forms::is_privacy_export_enabled($form)) {
            return [];
        }
        $results = self::get_results($email_address, $form, $page);
        $export_items = [];
        if (!static::done($results)) {
            $pii_fields = Caldera_Forms_Forms::personally_identifying_fields($form, true);
            /** @var Caldera_Forms_Entry_Field $field_value */
            foreach ($results->get_fields() as $field_value) {
                $entry_id = $field_value->entry_id;
                $entry = Caldera_Forms::get_entry($entry_id, $form);
                $data = [
                    [
                        'name' => self::find_field_name($form, $field_value->field_id),
                        'value' => $field_value->get_value()
                    ],
                    [
                        'name' => __('Date', 'caldera-forms'),
                        'value' => $entry['date']
                    ]
                ];

                if (!empty($entry['user'])) {
                    if (!empty($entry['user']['name'])) {
                        $data[] = [
                            'name' => __('WordPress User Name', 'caldera-forms'),
                            'value' => $entry['user']['name']
                        ];
                    }
                    if (!empty($entry['user']['email'])) {
                        $data[] = [
                            'name' => __('WordPress User Email', 'caldera-forms'),
                            'value' => $entry['user']['email']
                        ];
                    }
                }

                if (!empty($pii_fields)) {

                    foreach ($pii_fields as $field_id) {
                        if (array_key_exists($field_id, $entry['data'])) {
                            $data[] = [
                                'name' => self::find_field_name($form, $field_id),
                                'value' => $entry['data'][$field_id]['value']
                            ];
                        }

                    }
                }

                $export_items[] =[
                    'group_id' => static::group_id($form),
                    'group_label' => static::group_label($form, $field_value),
                    'item_id' => self::get_entry_id_from_result($field_value),
                    'data' => $data
                ];
            }

        }

        return [
            'data' => $export_items,
            'done' => self::done($results),
        ];
    }

    /**
     * Process one page of deletes
     *
     * @since 1.7.0
     *
     * @param string $email_address Email address to search PII by
     * @param array $form The form configuration
     *
     * @return array
     */
    public static function perform_erase($email_address, array $form)
    {
        if (!Caldera_Forms_Forms::is_privacy_export_enabled($form)) {
            return [];
        }

        //always query for first page, because if this is page 2 of deletes, first page is already deleted
        $results = self::get_results($email_address, $form, 1);
        $messages = array();
        $items_removed = false;
        $items_retained = false;
        if (!static::done($results)) {
            $ids = [];
            /** @var Caldera_Forms_Entry_Field $field_value */
            foreach ($results->get_fields() as $field_value) {
                $ids[] = static::get_entry_id_from_result($field_value);

            }
            Caldera_Forms_Entry_Bulk::delete_entries($ids);
            $items_removed = true;
        }

        return [
            'items_removed' => $items_removed,
            'items_retained' => $items_retained,
            'messages' => $messages,
            'done' => static::done($results),
        ];

    }

    /**
     * Get one page of results
     *
     * @since 1.7.0
     *
     * @param string $email_address Email address to search PII by.
     * @param array $form The form configuration.
     * @param int $page Page of results to get.
     *
     * @return Caldera_Forms_Entry_Fields
     */
    public static function get_results($email_address, array $form, $page)
    {
        $pii_query = new Caldera_Forms_Query_Pii(
            $form,
            $email_address,
            new Caldera_Forms_Query_Paginated($form, \calderawp\CalderaFormsQueries\CalderaFormsQueries())
        );
        $results = $pii_query->get_page($page);
        return $results;
    }

    /**
     * Given one result array, return entry ID
     *
     * @since 1.7.0
     *
     * @param Caldera_Forms_Entry_Field $result Result set
     * @return string
     */
    private static function get_entry_id_from_result($result)
    {
        return $result->entry_id;
    }

    /**
     * Check if results are empty and therefore process is done
     *
     * @since 1.7.0
     *
     * @param $results
     * @return bool
     */
    protected static function done($results)
    {
        return 0 === $results->count();
    }

    /**
     * Finds the name of a form field
     *
     * @since 1.7.0
     *
     * @param array $form
     * @param $field_id
     * @return string
     */
    protected static function find_field_name(array $form, $field_id)
    {
        $field = Caldera_Forms_Field_Util::get_field($field_id, $form);
        return is_array($field) && !empty($field['label']) ? $field['label'] : $field_id->slug;
    }

}