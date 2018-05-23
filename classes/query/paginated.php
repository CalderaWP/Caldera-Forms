<?php
use \calderawp\CalderaFormsQuery\Features\FeatureContainer as Queries;
use \calderawp\CalderaFormsQuery\Select\SelectQueryBuilder;
use \calderawp\CalderaFormsQuery\Select\EntryValues as EntryValueSelect;

/**
 * Class Caldera_Forms_Query_Paginated
 *
 * Does paginated queries. Shows all values.
 */
class Caldera_Forms_Query_Paginated implements Caldera_Forms_Query_Paginates
{

    /**
     * CalderaFormsQueries FeatureContainer
     *
     * @since 1.7.0
     *
     * @var Queries|null
     */
    private $queries;

    /**
     * Current page of results
     *
     * @since 1.7.0
     *
     * @var int
     */
    private $page;

    /**
     * Current limit/per page value
     *
     * @since 1.7.0
     *
     * @var int
     */
    private $limit;

    /**
     * Form configuration
     *
     * @since 1.7.0
     *
     *
     * @var array
     */
    private $form;
    /**
     * Entry IDs of this form
     *
     * @since 1.7.0
     *
     * @var array
     */
    private $entry_ids;

    /**
     * Caldera_Forms_Query_Paginated constructor.
     *
     * @since 1.7.0
     *
     * @param array $form The form configuration
     * @param Queries|null $queries Optional. Caldera Forms Query Tool feature container. Default is null.
     * @param int $page Optional. Page of results to get. Default is 1.
     * @param int $limit Optional. Total entries per page  of results. Default is 25.
     */
    public function __construct(array $form, Queries $queries = null, $page = 1, $limit = 25 )
    {
        $this->queries = is_null($queries) ? \calderawp\CalderaFormsQueries\CalderaFormsQueries() : $queries;
        $this->form = $form;
        $this->page = $this->set_page( $page );
        $this->set_limit( $limit );
    }


    /**
     * Select by entry IDs
     *
     * @since 1.7.0
     *
     * @param array $ids List of entry ids to select by
     *
     * @return array
     */
    public function select_by_entry_ids( array $ids )
    {

        $entries = $this
            ->get_queries_container()
            ->collectResults(
                $this
                    ->select(
                        $this
                            ->queries
                            ->getQueries()
                            ->entrySelect()
                            ->in( $ids )
                    )
            );


        return $entries;

    }

    /** @inheritdoc */
    public function select_values_for_form( EntryValueSelect $entry_value_select )
    {

        if (! empty( $this->get_entry_ids_of_form() )) {
            $entry_value_select
                ->in($this->get_entry_ids_of_form(), 'entry_id')
                ->addPagination($this->get_page(), $this->get_limit());
            $results = $this->select($entry_value_select);
        }else{
            $results = [];
        }
        $fields =  new Caldera_Forms_Entry_Fields( $this->form, $results );
        return $fields;

    }

    /** @inheritdoc */
    public function get_queries_container(){
        return $this
            ->queries;
    }
    /** @inheritdoc */
    public function get_page()
    {
        return ! is_numeric( $this->page) ? 1 :$this->page;
    }

    /** @inheritdoc */
    public function get_limit()
    {
        return ! is_numeric( $this->limit) ? 25 :$this->limit;
    }

    /** @inheritdoc */
    public function set_page($page)
    {

        $this->page = caldera_forms_validate_number( $page, 1, 20000 );
        return $this;
    }

    /** @inheritdoc */
    public function set_limit($limit)
    {
        $this->limit = caldera_forms_validate_number( $limit, 25, 100 );
        return $this;
    }


    /**
     * Get entry IDs of all entries of this form
     *
     * Acts as lazy-setter for entry_ids property
     *
     * @since 1.7.0
     *
     * @return array
     */
   protected function get_entry_ids_of_form(){
        if( ! $this->entry_ids ){
           $this->find_entry_ids_of_form();
        }
        return $this->entry_ids;

   }

    /**
     * Finds the entry IDs of all entries of this form by querying the database
     *
     * Acts as setter for entry_ids property
     *
     * @since 1.7.0
     *
     * @return array
     */
    protected function find_entry_ids_of_form()
    {
        $real_page = $this->page;
        $real_limit = $this->limit;
        $this->page = 1;
        $this->limit = $this->find_count();
        $results = $this->select_all();
        $this->page = $real_page;
        $this->limit = $real_limit;
        if( empty( $results ) ){
            $this->entry_ids = [];
        }else{
            foreach ( $results as $result ){
                $this->entry_ids[] = intval($result->id);
            }
            sort($this->entry_ids );
        }
        return $this->entry_ids;
    }

    /**
     * Find total number of entries for this form
     *
     * @since 1.7.0
     *
     * @return int
     */
    protected function find_count()
    {
        return Caldera_Forms_Entry_Bulk::count($this->form[ 'ID' ] );
    }


    /**
     * Get results of select query
     *
     * @since 1.7.0
     *
     * @param SelectQueryBuilder $query
     *
     * @return stdClass[]
     */
    protected function select(SelectQueryBuilder $query )
    {
        return $this->queries->getQueries()->select( $query );
    }

    /**
     * Queries for all entries of this form, given current pagination
     *
     * @since 1.7.0
     *
     * @return stdClass[]
     */
    public function select_all()
    {
        $entry_select = $this->queries
            ->getQueries()
            ->entrySelect()
            ->is('form_id', $this->form['ID'])
            ->addPagination($this->get_page(), $this->get_limit());

        return $this->select($entry_select);

    }

}