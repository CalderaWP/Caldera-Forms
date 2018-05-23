<?php


class Test_Caldera_Forms_Calculations extends Caldera_Forms_Test_Case
{

    /**
     * Simulated calculation of visual mode calculation field
     *
     * @since 1.7.0
     *
     * @covers Caldera_Forms::run_calculation()
     */
    public function testVisualCreatedCalculation(){
        $form = $this->import_calculations_field();
        $number_one = 25.12;
        $number_two = 37.75;
        Caldera_Forms::set_field_data( 'number_one', $number_one, $form );
        Caldera_Forms::set_field_data( 'number_two', $number_two, $form );

        $calc_field = Caldera_Forms_Field_Util::get_field( 'calc_visual', $form );
        $calculated_total = Caldera_Forms::run_calculation( rand(), $calc_field, $form );
        $expected_total = ( $number_one + $number_two ) * $number_one;
        $this->assertSame( $expected_total, $calculated_total );

    }

    /**
     * Simulated calculation of manual mode calculation field
     *
     * @since 1.7.0
     *
     * @covers Caldera_Forms::run_calculation()
     */
    public function testManualCalculation(){
        $form = $this->import_calculations_field();
        $number_one = 21.12;
        $number_two = 42.42;
        Caldera_Forms::set_field_data( 'number_one', $number_one, $form );
        Caldera_Forms::set_field_data( 'number_two', $number_two, $form );

        $calc_field = Caldera_Forms_Field_Util::get_field( 'calc_manual', $form );
        $calculated_total = Caldera_Forms::run_calculation( rand(), $calc_field, $form );
        $expected_total =  $number_one  * sin($number_two);
        $this->assertSame( (float)$expected_total, (float)$calculated_total );

    }

    /**
     * Test formatting of calculation field results
     *
     * @since 1.7.0
     *
     * @covers Caldera_Forms_Field_Util::format_calc_field()
     */
    public function testFormatting()
    {
        $calc_field = Caldera_Forms_Field_Util::get_field('calc_manual', $this->import_calculations_field());
        $value = 42.78987;
        $this->assertSame($value, (float)Caldera_Forms_Field_Util::format_calc_field($calc_field, $value));

        $calc_field = Caldera_Forms_Field_Util::get_field('calc_money', $this->import_calculations_field());
        $this->assertSame(42.78, (float)Caldera_Forms_Field_Util::format_calc_field($calc_field, $value));
    }

    /**
     * Simulated calculation of money mode calculation field
     *
     * @since 1.7.0
     *
     * @covers Caldera_Forms::run_calculation()
     * @covers Caldera_Forms_Field_Util::format_calc_field()
     */
    public function testMoneyModeCalculation(){
        $form = $this->import_calculations_field();
        $number_one = 21.12;
        $number_two = 42.42;
        Caldera_Forms::set_field_data( 'number_one', $number_one, $form );
        Caldera_Forms::set_field_data( 'number_two', $number_two, $form );

        $calc_field = Caldera_Forms_Field_Util::get_field( 'calc_money', $form );
        $calculated_total = Caldera_Forms::run_calculation( rand(), $calc_field, $form );
        $expected_total =  money_format( '%i', $number_one + $number_two );
        $this->assertSame( (float)$expected_total, (float)$calculated_total );
    }

    /**
     * Import test form for calculations
     *
     * @since 1.7.0
     *
     * @return array
     */
    protected function import_calculations_field(){
        $form_id = $this->import_form(  dirname(__FILE__) . '/includes/forms/calculations-form.json' );
        return Caldera_Forms_Forms::get_form( $form_id );
    }

}