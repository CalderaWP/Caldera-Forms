<?php

/**
 * Class Caldera_Forms_Query_Pii
 *
 * Queries for saved values of a form that are PII for a given email.
 * Decorates Caldera_Forms_Query_Paginated and reduces results to PII fields only.
 */
class Caldera_Forms_Query_Pii
{
    /**
     * The form configuration
     *
     * @since 1.7.0
     *
     * @var array $form
     */
    protected $form;

    /**
     * The email address to search by
     *
     * @since 1.7.0
     *
     * @var string
     */
    protected $email;

    /**
     * Paginated query
     *
     * @since 1.7.0
     *
     * @var Caldera_Forms_Query_Paginated
     */
    protected $paginated;

    /**
     *
     * @since 1.7.0
     *
     * @var int
     */
    protected $limit;

    /**
     * Caldera_Forms_Query_Pii constructor.
     *
     * @since 1.7.0
     *
     * @param array $form The form configuration
     * @param string $email Email address to search PII by
     * @param Caldera_Forms_Query_Paginates $paginated
     * @param int $limit Optional. Total entries per page  of results. Default is 25.
     */
    public function __construct(array $form, $email, Caldera_Forms_Query_Paginates $paginated, $limit = 25)
    {
        $this->form = $form;
        $this->email = sanitize_email($email);
        $this->paginated = $paginated;
        $this->limit = caldera_forms_validate_number( $limit, 25, 100 );
    }

    /**
     * Get one page of results
     *
     * @since 1.7.0
     *
     * @param int $page Which page of results?
     * @return array
     */
    public function get_page($page)
    {
        $entries = $this->find_entries($page);
        $ids = wp_list_pluck( $entries, 'id' );
        $results = $this->paginated->select_by_entry_ids($ids);
        return $this->reduce_results_to_pii( $results );
    }

    /**
     * Given a results set, reduce to PII fields only.
     *
     * @since 1.7.0
     *
     * @param array $results
     * @return array
     */
    public function reduce_results_to_pii(array $results)
    {
        /**
         * @var int $result_index
         * @var array $result
         */
        foreach ( $results as $result_index => $result ){
            /**
             * @var int $value_index
             * @var Caldera_Forms_Entry_Field $field_value
             */
            foreach ( $result[ 'values' ] as $value_index => $field_value ){

                if( Caldera_Forms_Field_Util::is_personally_identifying( $field_value->field_id, $this->form )
                    ||Caldera_Forms_Field_Util::is_email_identifying_field( $field_value->field_id, $this->form)
                ){
                    continue;
                }
                unset( $results[ $result_index ][ 'values' ][ $value_index ] );
            }
        }
        return $results;
    }

    /**
     * Finds entries belonging to this
     *
     * @since 1.7.0
     *
     * @param int $page Which page of results?
     * @return array
     */
    private function find_entries($page)
    {
        $featureContainer = \calderawp\CalderaFormsQueries\CalderaFormsQueries();
        $entryValueSelect = $featureContainer
            ->getQueries()
            ->entryValuesSelect();
        foreach (Caldera_Forms_Forms::email_identifying_fields($this->form) as $field) {
            $entryValueSelect->is($field['slug'], $this->email);
        }

        return $this
            ->paginated
            ->set_page($page)
            ->set_limit($this->limit)
            ->select_values_for_form($entryValueSelect);
    }

}