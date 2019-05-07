<?php

class RunCalculationTest extends Caldera_Forms_Test_Case
{

  /**
   * Declare protected names for data and the base data in the $form variable
   *
   * @since 1.8.5
   */
  protected $fieldOne;
  protected $fieldTwo ;
  protected $fieldThree;
  protected $calculationField;
  protected $fieldOneValue;
  protected $fieldTwoValue;
  protected $fieldThreeValue;

  protected $form = array(
      '_last_updated' => 'Mon, 29 Apr 2019 16:26:35 +0000',
      'ID' => 'calculation',
      'cf_version' => '1.8.4',
      'name' => 'Calculation',
      'scroll_top' => 0,
      'success' => 'Form has been successfully submitted. Thank you.												',
      'db_support' => 1,
      'pinned' => 0,
      'hide_form' => 1,
      'avatar_field' => '',
      'form_ajax' => 1,
      'custom_callback' => '',
      'layout_grid' =>
      array(
        'fields' =>
        array(
          'fld_1892141' => '1:1',
          'fld_93143' => '1:1',
          'fld_7465068' => '1:1',
          'fld_8568604' => '1:1',
          'fld_3665187' => '1:1',
        ),
        'structure' => '12',
      ),
      'fields' =>
      array(
        'fld_1892141' =>
        array(
          'ID' => 'fld_1892141',
          'type' => 'number',
          'label' => 'field1',
          'slug' => 'field1',
          'conditions' =>
          array(
            'type' => '',
          ),
          'caption' => '',
          'config' =>
          array(
            'custom_class' => '',
            'placeholder' => '',
            'default' => '',
            'min' => '',
            'max' => '',
            'step' => '',
            'email_identifier' => 0,
            'personally_identifying' => 0,
          ),
        ),
        'fld_93143' =>
        array(
          'ID' => 'fld_93143',
          'type' => 'number',
          'label' => 'field2',
          'slug' => 'field2',
          'conditions' =>
          array(
            'type' => '',
          ),
          'caption' => '',
          'config' =>
          array(
            'custom_class' => '',
            'placeholder' => '',
            'default' => '',
            'min' => '',
            'max' => '',
            'step' => '',
            'email_identifier' => 0,
            'personally_identifying' => 0,
          ),
        ),
        'fld_7465068' =>
        array(
          'ID' => 'fld_7465068',
          'type' => 'number',
          'label' => 'field3',
          'slug' => 'field3',
          'conditions' =>
          array(
            'type' => '',
          ),
          'caption' => '',
          'config' =>
          array(
            'custom_class' => '',
            'placeholder' => '',
            'default' => '',
            'min' => '',
            'max' => '',
            'step' => '',
            'email_identifier' => 0,
            'personally_identifying' => 0,
          ),
        ),
        'fld_8568604' => array(
              'ID' => 'fld_8568604',
              'type' => 'calculation',
              'label' => 'calc',
              'slug' => 'calc',
              'conditions' => array(
                  'type' => '',
              ),
              'caption' => '',
              'config' => array(
                  'custom_class' => '',
                  'element' => '',
                  'classes' => '',
                  'before' => 'Total:',
                  'after' => '',
                  'thousand_separator' => ',',
                  'decimal_separator' => '.',
                  'formular' => ' ( fld_1892141+fld_93143 ) /fld_7465068',
                  'config' =>  array(
                      'group' =>
                          array(
                          0 =>
                          array(
                              'lines' =>
                              array(
                              0 =>
                              array(
                                  'operator' => '+',
                                  'field' => 'fld_1892141',
                              ),
                              1 =>
                              array(
                                  'operator' => '+',
                                  'field' => 'fld_93143',
                              ),
                              ),
                          ),
                          1 =>
                          array(
                              'operator' => '/',
                          ),
                          2 =>
                          array(
                              'lines' =>
                              array(
                              0 =>
                                  array(
                                      'operator' => '+',
                                      'field' => 'fld_7465068',
                                  ),
                              ),
                          ),
                      ),
                  ),
                  'manual_formula' => '(%field1%+%field2%)/%field3%',
                  'email_identifier' => 0,
                  'personally_identifying' => 0,
              ),
          ),
        'fld_3665187' =>
        array(
          'ID' => 'fld_3665187',
          'type' => 'button',
          'label' => 'Save',
          'slug' => 'save',
          'conditions' =>
          array(
            'type' => '',
          ),
          'caption' => '',
          'config' =>
          array(
            'custom_class' => '',
            'type' => 'submit',
            'class' => 'btn btn-default',
            'target' => '',
          ),
        ),
      ),
      'page_names' =>
      array(
        0 => 'Page 1',
      ),
      'mailer' =>
      array(
        'on_insert' => 1,
        'sender_name' => 'Caldera Forms Notification',
        'sender_email' => 'nico@calderawp.com',
        'reply_to' => '',
        'email_type' => 'html',
        'recipients' => '',
        'bcc_to' => '',
        'email_subject' => 'Calculations',
        'email_message' => '{summary}
    <div style="display: none;"></div>
    <div style="display: none;"></div>
    <div style="display: none;"></div>',
      ),
      'check_honey' => 1,
      'antispam' =>
      array(
        'sender_name' => '',
        'sender_email' => '',
      ),
      'conditional_groups' =>
      array(
        '_open_condition' => '',
      ),
      'settings' =>
      array(
        'responsive' =>
        array(
          'break_point' => 'sm',
        ),
      ),
      'privacy_exporter_enabled' => false,
      'version' => '1.8.4',
      'db_id' => '45',
      'type' => 'primary',
      '_external_form' => 1,
  );

  /**
   * Set values
   *
   * @since 1.8.5
   */
  public function __construct()
  {
    $this->fieldOne = $this->form['fields']['fld_1892141'];
    $this->fieldTwo = $this->form['fields']['fld_93143'];
    $this->fieldThree = $this->form['fields']['fld_7465068'];
    $this->calculationField = $this->form['fields']['fld_8568604'];
    $this->fieldOneValue = 6.55;
    $this->fieldTwoValue = 4.2;
    $this->fieldThreeValue = 22.7;
  }


  /**
   * @since 1.8.5
   *
   * @covers \Caldera_Forms::run_calculation()
   */
  public function testRunCalculation()
  {
    Caldera_Forms::set_field_data( $this->fieldOne['ID'], $this->fieldOneValue, $this->form );
    Caldera_Forms::set_field_data( $this->fieldTwo['ID'], $this->fieldTwoValue, $this->form );
    Caldera_Forms::set_field_data( $this->fieldThree['ID'], $this->fieldThreeValue, $this->form );

    $expectedResult = ($this->fieldOneValue + $this->fieldTwoValue) / $this->fieldThreeValue;

    //Test Calculation by fields
    $actualResult = Caldera_Forms::run_calculation( null, $this->calculationField, $this->form );
    $this->assertSame( floatval($expectedResult ), floatval($actualResult) );
  }

  /**
   * @since 1.8.5
   *
   * @covers \Caldera_Forms::run_calculation()
   */
  public function testRunCalculationManualFormula()
  {
    Caldera_Forms::set_field_data( $this->fieldOne['ID'], $this->fieldOneValue, $this->form );
    Caldera_Forms::set_field_data( $this->fieldTwo['ID'], $this->fieldTwoValue, $this->form );
    Caldera_Forms::set_field_data( $this->fieldThree['ID'], $this->fieldThreeValue, $this->form );

    $expectedResult = ($this->fieldOneValue + $this->fieldTwoValue) / $this->fieldThreeValue;

    //Test manual formula
    $form = $this->form;
    $form['fields']['fld_8568604']['config']['manual'] = true;
    $calculationField = $form['fields']['fld_8568604'];
    $actualResult = Caldera_Forms::run_calculation( null, $calculationField, $form );
    $this->assertSame( floatval($expectedResult), floatval($actualResult) );
  }

  /**
   * @since 1.8.5
   *
   * @covers \Caldera_Forms::run_calculation()
   */
  public function testRunCalculationPowMathFunction()
  {
    Caldera_Forms::set_field_data( $this->fieldOne['ID'], $this->fieldOneValue, $this->form );
    Caldera_Forms::set_field_data( $this->fieldTwo['ID'], $this->fieldTwoValue, $this->form );
    Caldera_Forms::set_field_data( $this->fieldThree['ID'], $this->fieldThreeValue, $this->form );

    $expectedResult = pow($this->fieldOneValue , $this->fieldTwoValue) + $this->fieldThreeValue;

    //Test manual formula
    $form = $this->form;
    $form['fields']['fld_8568604']['config']['manual'] = true;
    $form['fields']['fld_8568604']['config']['manual_formula'] = 'pow(%field1%,%field2%)+%field3%';
    $calculationField = $form['fields']['fld_8568604'];
    $actualResult = Caldera_Forms::run_calculation( null, $calculationField, $form );
    $this->assertSame( floatval($expectedResult), floatval($actualResult) );
  }

  /**
   * @since 1.8.5
   *
   * @covers \Caldera_Forms::run_calculation()
   */
  public function testRunCalculationPowMathSignFunction()
  {
    Caldera_Forms::set_field_data( $this->fieldOne['ID'], $this->fieldOneValue, $this->form );
    Caldera_Forms::set_field_data( $this->fieldTwo['ID'], $this->fieldTwoValue, $this->form );
    Caldera_Forms::set_field_data( $this->fieldThree['ID'], $this->fieldThreeValue, $this->form );

    $expectedResult = $this->fieldOneValue ** $this->fieldTwoValue + $this->fieldThreeValue;

    //Test manual formula
    $form = $this->form;
    $form['fields']['fld_8568604']['config']['manual'] = true;
    $form['fields']['fld_8568604']['config']['manual_formula'] = '%field1%**%field2%+%field3%';
    $calculationField = $form['fields']['fld_8568604'];
    $actualResult = Caldera_Forms::run_calculation( null, $calculationField, $form );
    $this->assertSame( floatval($expectedResult), floatval($actualResult) );
  }

  /**
   * @since 1.8.5
   *
   * @covers \Caldera_Forms::run_calculation()
   */
  public function testRunCalculationExpMathFunction()
  {
    Caldera_Forms::set_field_data( $this->fieldOne['ID'], $this->fieldOneValue, $this->form );
    Caldera_Forms::set_field_data( $this->fieldTwo['ID'], $this->fieldTwoValue, $this->form );
    Caldera_Forms::set_field_data( $this->fieldThree['ID'], $this->fieldThreeValue, $this->form );

    $expectedResult = exp($this->fieldOneValue) + $this->fieldThreeValue * $this->fieldTwoValue;

    //Test manual formula
    $form = $this->form;
    $form['fields']['fld_8568604']['config']['manual'] = true;
    $form['fields']['fld_8568604']['config']['manual_formula'] = 'exp(%field1%)+%field3%*%field2%';
    $calculationField = $form['fields']['fld_8568604'];
    $actualResult = Caldera_Forms::run_calculation( null, $calculationField, $form );
    $this->assertSame( floatval($expectedResult), floatval($actualResult) );
  }

  /**
   * @since 1.8.5
   *
   * @covers \Caldera_Forms::run_calculation()
   */
  public function testRunCalculationCeilMathFunction()
  {
    Caldera_Forms::set_field_data( $this->fieldOne['ID'], $this->fieldOneValue, $this->form );
    Caldera_Forms::set_field_data( $this->fieldTwo['ID'], $this->fieldTwoValue, $this->form );
    Caldera_Forms::set_field_data( $this->fieldThree['ID'], $this->fieldThreeValue, $this->form );

    $expectedResult = ceil($this->fieldOneValue) * $this->fieldThreeValue / $this->fieldTwoValue;

    //Test manual formula
    $form = $this->form;
    $form['fields']['fld_8568604']['config']['manual'] = true;
    $form['fields']['fld_8568604']['config']['manual_formula'] = 'ceil(%field1%)*%field3%/%field2%';
    $calculationField = $form['fields']['fld_8568604'];
    $actualResult = Caldera_Forms::run_calculation( null, $calculationField, $form );
    $this->assertSame( floatval($expectedResult), floatval($actualResult) );
  }

  /**
   * @since 1.8.5
   *
   * @covers \Caldera_Forms::run_calculation()
   */
  public function testRunCalculationFloorLogMathFunctions()
  {
    Caldera_Forms::set_field_data( $this->fieldOne['ID'], $this->fieldOneValue, $this->form );
    Caldera_Forms::set_field_data( $this->fieldTwo['ID'], $this->fieldTwoValue, $this->form );

    $expectedResult = floor( log( $this->fieldOneValue, $this->fieldTwoValue ) );

    //Test manual formula
    $form = $this->form;
    $form['fields']['fld_8568604']['config']['manual'] = true;
    $form['fields']['fld_8568604']['config']['manual_formula'] = 'floor(log(%field1%,%field2%))';
    $calculationField = $form['fields']['fld_8568604'];
    $actualResult = Caldera_Forms::run_calculation( null, $calculationField, $form );
    $this->assertSame( floatval($expectedResult), floatval($actualResult) );
  }

  /**
   * @since 1.8.5
   *
   * @covers \Caldera_Forms::run_calculation()
   */
  public function testRunCalculationRoundSqrtMathFunctions()
  {
    Caldera_Forms::set_field_data( $this->fieldOne['ID'], $this->fieldOneValue, $this->form );
    Caldera_Forms::set_field_data( $this->fieldTwo['ID'], $this->fieldTwoValue, $this->form );

    $expectedResult = round( sqrt( $this->fieldOneValue ) + sqrt( $this->fieldTwoValue ) );

    //Test manual formula
    $form = $this->form;
    $form['fields']['fld_8568604']['config']['manual'] = true;
    $form['fields']['fld_8568604']['config']['manual_formula'] = 'round(sqrt(%field1%)+sqrt(%field2%))';
    $calculationField = $form['fields']['fld_8568604'];
    $actualResult = Caldera_Forms::run_calculation( null, $calculationField, $form );
    $this->assertSame( floatval($expectedResult), floatval($actualResult) );
  }

  /**
   * @since 1.8.5
   *
   * @covers \Caldera_Forms::run_calculation()
   */
  public function testRunCalculationCosSinTanTrigFunctions()
  {
    Caldera_Forms::set_field_data( $this->fieldOne['ID'], 6, $this->form );
    Caldera_Forms::set_field_data( $this->fieldTwo['ID'], 4, $this->form );
    Caldera_Forms::set_field_data( $this->fieldThree['ID'], 20, $this->form );

    $expectedResult = cos( 6 ) + sin( 4 ) + tan( 20 );

    //Test manual formula
    $form = $this->form;
    $form['fields']['fld_8568604']['config']['manual'] = true;
    $form['fields']['fld_8568604']['config']['manual_formula'] = 'cos(%field1%)+sin(%field2%)+tan(%field3%)';
    $calculationField = $form['fields']['fld_8568604'];
    $actualResult = Caldera_Forms::run_calculation( null, $calculationField, $form );
    $this->assertSame( floatval($expectedResult), floatval($actualResult) );
  }

   /**
   * @since 1.8.5
   *
   * @covers \Caldera_Forms::run_calculation()
   */
  public function testRunCalculationAntiTrigFunctions()
  {
    Caldera_Forms::set_field_data( $this->fieldOne['ID'], -0.5, $this->form );
    Caldera_Forms::set_field_data( $this->fieldTwo['ID'], 0.2, $this->form );
    Caldera_Forms::set_field_data( $this->fieldThree['ID'], -0.045, $this->form );

    $expectedResult = acos( -0.5 ) + asin( 0.2 ) + atan( -0.045 );

    //Test manual formula
    $form = $this->form;
    $form['fields']['fld_8568604']['config']['manual'] = true;
    $form['fields']['fld_8568604']['config']['manual_formula'] = 'acos(%field1%)+asin(%field2%)+atan(%field3%)';
    $calculationField = $form['fields']['fld_8568604'];
    $actualResult = Caldera_Forms::run_calculation( null, $calculationField, $form );
    $this->assertSame( floatval($expectedResult), floatval($actualResult) );
  }

}