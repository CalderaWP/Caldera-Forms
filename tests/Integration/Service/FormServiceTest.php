<?php


namespace calderawp\calderaforms\Tests\Integration\Service;


use calderawp\calderaforms\cf2\Forms\FormCollection;
use calderawp\calderaforms\cf2\Services\FormsService;
use calderawp\calderaforms\Tests\Integration\TestCase;
use SaturdayDrive\EmailCRM\CfBridge\Database\Forms;

class FormServiceTest extends TestCase
{
    /**
     * @inheritDoc
     */
    public function tearDown()
    {
        //Delete forms after each test
        $forms = \Caldera_Forms_Forms::get_forms(false, true);
        if (!empty($forms)) {
            foreach ($forms as $formId) {
                \Caldera_Forms_Forms::delete_form($formId);
            }
        }
        parent::tearDown();
    }

    /**
     * @since 1.8.10
     * @covers \calderawp\calderaforms\cf2\Services\FormsService::register()
     * @covers \calderawp\calderaforms\cf2\Forms\Collection::getAll()
     * @covers \calderawp\calderaforms\cf2\Forms\Collection::addForm()
     */
    public function testGetForms()
    {
        $formId = \Caldera_Forms_Forms::save_form([
            'ID' => \Caldera_Forms_Forms::create_unique_form_id(),
            'name' => 'Salad Tables'
        ]);
        $form = \Caldera_Forms_Forms::get_form($formId);
        $this->assertArrayHasKey('ID', $form);

        $form2Id = \Caldera_Forms_Forms::save_form([
            'ID' => \Caldera_Forms_Forms::create_unique_form_id(),
            'name' => 'Dessert Forks'
        ]);
        $container = $this->getContainer();
        $container->registerService(new FormsService(), true);
        /** @var FormCollection $formService */
        $formService = $container->getService(FormsService::class);
        $this->assertCount(2, $formService->getAll());
        $this->assertSame('Dessert Forks', $formService->getAll()[$form2Id]['name']);
        $this->assertSame('Salad Tables', $formService->getAll()[$formId]['name']);
    }

    /**
     * @since 1.8.10
     *
     * @covers \calderawp\calderaforms\cf2\Services\FormsService::register()
     * @covers \calderawp\calderaforms\cf2\Forms\Collection::getForm()
     * @covers \calderawp\calderaforms\cf2\Forms\Collection::addForm()
     */
    public function testGetForm()
    {
        $formId = \Caldera_Forms_Forms::save_form([
            'ID' => \Caldera_Forms_Forms::create_unique_form_id(),
            'name' => 'Salad Tables'
        ]);
        $form = \Caldera_Forms_Forms::get_form($formId);
        $this->assertArrayHasKey('ID', $form);

        $form2Id = \Caldera_Forms_Forms::save_form([
            'ID' => \Caldera_Forms_Forms::create_unique_form_id(),
            'name' => 'Dessert Forks'
        ]);
        $container = $this->getContainer();
        $container->registerService(new FormsService(), true);
        /** @var FormCollection $formService */
        $formService = $container->getService(FormsService::class);
        $this->assertCount(2, $formService->getAll());
        $this->assertSame('Dessert Forks', $formService->getForm($form2Id)['name']);
        $this->assertSame('Salad Tables', $formService->getForm($formId)['name']);
    }
}