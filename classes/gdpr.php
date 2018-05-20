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
        if ( ! empty( static::enabled_forms() )) {

            add_filter('wp_privacy_personal_data_erasers', function ($erasers) {

                foreach (static::enabled_forms() as $form) {
                    $exporters[] = [
                        'eraser_friendly_name' => $form['name'],
                        'callback' => [__CLASS__, 'eraser'],
                    ];
                }
                return $erasers;
            });

            add_filter('wp_privacy_personal_data_exporters', function ($exporters) {

                foreach (static::enabled_forms() as $form) {
                    $exporters[] = [
                        'exporter_friendly_name' => $form['name'],
                        'callback' => [__CLASS__, 'exporter'],
                    ];
                }

                return $exporters;
            });
        }

    }

    /**
     * Get an array of forms that have a data exporter enabled
     *
     * @since 1.7.0
     *
     * @return array
     */
    public static function enabled_forms()
    {
        if( ! empty( static::$enabled_forms ) ){
            return static::$enabled_forms;
        }

        foreach ( Caldera_Forms_Forms::get_forms() as $form ){
            if( Caldera_Forms_Forms::is_privacy_export_enabled($form)){
                static::$enabled_forms = $form;
            }
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
        if( 0 === strpos($name, 'exporter' ) ){
            $form_id = str_replace( 'exporter', '', $name );
            $form = Caldera_Forms_Forms::get_form($form_id );
            return static::get_export_data($arguments[0], $form, $arguments[2]);
        }


        if( 0 === strpos($name, 'eraser' ) ){
            $form_id = str_replace( 'eraser', '', $name );
            $form = Caldera_Forms_Forms::get_form($form_id );
            return static::get_export_data($arguments[0], $form, $arguments[2]);
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
    public static function group_id( array  $form )
    {
        return 'caldera-forms-' . sanitize_title_with_dashes($form[ 'name' ]);
    }

    /**
     * Get the group label
     *
     * @since 1.7.0
     *
     * @param array $form The form configuration
     * @return string
     */
    public static function group_label( array $form )
    {
        return 'Caldera Forms: ' . $form[ 'name' ];
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
    public static function get_export_data($email_address, array $form, $page = 1 )
    {
        $results = self::get_results($email_address, $form, $page);
        $export_items = [];
        if( ! empty( $results ) ){
            foreach ( $results as $result ){
                $export_items[] = array(
                    'group_id'    => static::group_id( $form ),
                    'group_label' => static::group_label( $form ),
                    'item_id'     => self::get_entry_id_from_result($result),
                    'data'        => $result,
                );
            }

        }
        
        return array(
            'data' => $export_items,
            'done' => empty( $results ),
        );
    }

    /**
     * Process one page of deletes
     *
     * @since 1.7.0
     *
     * @param string $email_address Email address to search PII by
     * @param array $form The form configuration
     * @param int $page Optional. Page of results to get. Default is 1.
     *
     * @return array
     */
    public static function perform_erase(  $email_address, array $form, $page = 1 )
    {
        $results = self::get_results($email_address, $form, $page);
        $messages = array();
        $items_removed  = false;
        $items_retained = false;
        if ( ! empty( $results )) {
            $ids = [];
            foreach ($results as $result) {
               $ids[] = static::get_entry_id_from_result($result);
            }
            $items_removed = Caldera_Forms_Entry_Bulk::delete_entries($ids);
        }

        return array(
            'items_removed'  => $items_removed,
            'items_retained' => $items_retained,
            'messages'       => $messages,
            'done'           => true,
        );

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
     * @return array
     */
    private static function get_results($email_address, array $form, $page)
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
     * @param array $result Result set
     * @return string
     */
    private static function get_entry_id_from_result($result)
    {
        return $result['entry']->id;
    }

}