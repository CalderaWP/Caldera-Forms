<?php
$format_field = new Caldera_Forms_Admin_Field();
$format_field->set_from_array(
	array(
		'type' => 'text',
		'name' => 'format',
		'label' => __( 'Format', 'caldera-forms' ),
		'args' => array(
			'description' => $description,
			'block' => true,
			'magic' => true,
			'classes' => 'cfdatepicker-set-format '
		)
	)
);

$languages = array(
	'ar',
	'az',
	'bg',
	'bs',
	'ca',
	'cs',
	'cy',
	'da',
	'de',
	'el',
	'en-GB',
	'es',
	'et',
	'eu',
	'fa',
	'fi',
	'fo',
	'fr-CH',
	'gl',
	'he',
	'hr',
	'hu',
	'hy',
	'id',
	'is',
	'it-CH',
	'ja',
	'ka',
	'kh',
	'kk',
	'kr',
	'lt',
	'mk',
	'ms',
	'nb',
	'nl-BE',
	'nl',
	'no',
	'pl',
	'pt-BR',
	'ro',
	'rs-latin',
	'rs',
	'ru',
	'sk',
	'sl',
	'sq',
	'sr-latin',
	'sr',
	'sv',
	'sw',
	'th',
	'tr',
	'uk',
	'vi',
	'zh-CN',
	'zh-TW'
);
$language_options = array(
	'' => 'en-US',
);
foreach ( $languages as $language ){
	$language_options[ $language ] = $language;
}

$lang_field = new Caldera_Forms_Admin_Field();
$lang_field->set_from_array(
	array(
		'type' => 'select',
		'name' => 'language',
		'label' => __( 'Language', 'caldera-forms' ),
		'options' => $language_options,
		'args' => array(
			'description' => __( 'Language to use. e.g. pt-BR', 'caldera-forms' ),
			'block'       => true,
			'classes'     => 'cfdatepicker-set-language '
		)
	)
);

$fields = array(
	'default',
	$format_field,
	Caldera_Forms_Admin_UI::checkbox_field(
		'autoclose',
		__('Close After Selection?', 'caldera-forms' ),
		array(
			'autoclosed' => __('Enable autoclose', 'caldera-forms')
		),
		__( 'If enabled, the date picker will automatically close after selecting the final input', 'caldera-forms')
	),
	Caldera_Forms_Admin_UI::select_field(
		'startview',
		__( 'Start View', 'caldera-forms' ),
		array(
			'month'  => __( 'Month (Default)', 'caldera-forms' ),
			'year'   => __( 'Year', 'caldera-forms' ),
			'decade' => __( 'Decade', 'caldera-forms' ),
		),
		__('The starting view of the date picker (month, year, decade)', 'caldera-forms')
	),
	Caldera_Forms_Admin_UI::text_field(
		'startdate',
		__( 'Start Date', 'caldera-forms' ),
		__( 'The starting date of the date picker, relative to current day. For example,  "+1d" for 1 day after today, "-2y" for two years ago, or "+4m" for 4 months from now.', 'caldera-forms')
	),
	Caldera_Forms_Admin_UI::text_field(
		'startdate',
		__( 'Start Date', 'caldera-forms' ),
		__( 'The first possible date that can be selected, relative to current day. For example,  "+1d" for 1 day after today, "-2y" for two years ago, or "+4m" for 4 months from now.', 'caldera-forms')
	),
	Caldera_Forms_Admin_UI::text_field(
		'end-date',
		__( 'End Date', 'caldera-forms' ),
		__( 'The last possible date that can be selected, relative to current day. For example,  "-12d" for 12 days before today, "+42y" for 42 years from now, or "-6m" for 6 months ago.', 'caldera-forms')
	),
	$lang_field,

);

echo Caldera_Forms_Admin_UI::fields( $fields, 'date_picker' );
