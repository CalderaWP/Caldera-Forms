<?php


namespace calderawp\calderaforms\Tests\Util\Mocks;

use calderawp\calderaforms\cf2\Fields\Handlers\FieldHandler;

class MockFieldHandler extends FieldHandler
{

    /** @inheritdoc */
    public function processField($entry, $field, $form)
    {
        return $entry;
    }

    /** @inheritdoc */
    public function saveField($entry, $field, $form, $entry_id)
    {
        return $entry;
    }
}