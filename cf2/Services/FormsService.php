<?php


namespace calderawp\calderaforms\cf2\Services;


use calderawp\calderaforms\cf2\CalderaFormsV2Contract;
use calderawp\calderaforms\cf2\Forms\Collection;

/**
 * Class FormsService
 *
 * Service provider for forms. Abstracts over v1 forms API.
 */
class FormsService extends Service
{


    /** @inheritDoc */
    public function isSingleton()
    {
        return true;
    }

    /** @inheritDoc */
    public function register(CalderaFormsV2Contract $container)
    {
        $collection = new Collection();
        $forms = \Caldera_Forms_Forms::get_forms(true, false);
        if (!empty($forms)) {
            foreach ($forms as $form) {
                if (isset($form['ID'])) {
                    $collection->addForm($form);
                }
            }
        }
        return $collection;

    }

}