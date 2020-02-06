<?php

/**
 * Test the do_redirect function
 *
 * @package   Caldera_Forms
 * @author    Nico Figueira <nico@saturdaydrive.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Test_Caldera_Forms_Do_Redirect extends Caldera_Forms_Test_Case
{
    /**
     * Test that query variable are returned URL encoded when using a redirection processor
     *
     * @since 1.8.10
     *
     */
    public function test_query_variable_return_encoded(){
        $form =  $this->mock_form;
        //Add the redirection processor
        $form["processors"]["fp_49138757"] = [
            "ID" => "fp_49138757",
            "runtimes" => [
                "insert"    =>    1
            ],
            "type"  =>   "form_redirect",
            "config"    =>   [
                "url"   =>  "/caldera-forms-test-2",
                "message"  => "go"
            ],
            "conditions"   => []
        ];
        //Set the $referrer as it would come in with a passback variable named first_name using data from a text field that was input with "Tamekah { + ? = ; : ù % > < { + kù" as value
        $referrer = "/caldera-forms-test/?cf_su=1&cf_id=59&first_name=Tamekah+%7B+%2B+%3F+%3D+%3B+%3A+%C3%B9+%25+%3E+%3C+%7B+%2B+k%C3%B9";
        //Process do_redirect()
        $redirect = caldera_forms::do_redirect($referrer,$form, "159");

        //We can't look for ? or % or + but the failing test would return characters like ; : ù > < { in $redirect
        $this->assertTrue( strpos($redirect, "{") === false );
        $this->assertTrue( strpos($redirect, ":") === false );
        $this->assertTrue( strpos($redirect, ";") === false );
        $this->assertTrue( strpos($redirect, "<") === false );
        $this->assertTrue( strpos($redirect, "ù") === false );
        $this->assertTrue( strpos($redirect, "Tamekah") === 42 );
        $this->assertTrue( strpos($redirect, "k") === 46 );
    }

}