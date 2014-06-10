<?php


class Caldera_Forms_Widget extends WP_Widget {

	function Caldera_Forms_Widget() {
		// Instantiate the parent object
		parent::__construct( false, __('Caldera Form', 'caldera-forms' ) );
	}

	function widget( $args, $instance ) {

		if(!empty($instance['form'])){

			extract($args, EXTR_SKIP);



			echo $before_widget;
			$title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
			if ( !empty( $title ) ) { echo $before_title . $title . $after_title; };

				echo Caldera_Forms::render_form($instance['form']);
			
			echo $after_widget;

				
		}
	}

	function update( $new_instance, $old_instance ) {
		// Save widget options
		return $new_instance;
	}

	function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title = strip_tags($instance['title']);
		
		do_action( 'caldera_forms_widget_form_start', $instance );

		echo "<p><label for=\" " . $this->get_field_id('title') . "\">" . __('Title') . ": <input class=\"widefat\" id=\"" . $this->get_field_id('title') . "\" name=\"" . $this->get_field_name('title') . "\" type=\"text\" value=\"" . esc_attr($title). "\" /></label></p>\r\n";
		// get forms
		$forms = get_option( '_caldera_forms' );

		echo "<p><label for=\" " . $this->get_field_id('title') . "\">" . __('Form') . ": </label><select style=\"width:100%;\" name=\"" . $this->get_field_name('form') . "\">\r\n";

		foreach($forms as $formid=>$form){
			$sel = "";
			if(!empty($instance['form'])){
				if($instance['form'] == $formid){
					$sel = ' selected="selected"';
				}
			}
			echo "<option value=\"" . $formid . "\"".$sel.">" . $form['name'] ."</option>\r\n";
		}

		echo "</select></p>\r\n";
		do_action( 'caldera_forms_widget_form_end', $instance, $this );
	}
}

function register_caldera_form_widget() {
	register_widget( 'Caldera_Forms_Widget' );
}

add_action( 'widgets_init', 'register_caldera_form_widget' );