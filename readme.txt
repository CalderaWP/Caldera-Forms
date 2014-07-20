=== Caldera Forms ===
Contributors: Desertsnowman
Tags: forms, formbuilder, form builder, contact form, contact, custom form, custom forms, forms creator
Requires at least: 3.9
Tested up to: 3.9.1
Stable tag: 1.0.6
License: GPLv2

Create complex grid based, responsive forms easily with an easy to use drag and drop layout builder.

== Description ==
Caldera Forms is a free, simple form builder with a layout builder that enables you to create the format you want for your form. It is fully responsive and with form processors, it gives you flexibility to handle the form data how you need it.

Add form processors to your forms to have the data go where you need it. Yo can stack up processors to have them do multiple tasks. Currently, there is only an Auto-Responder and Redirect processor, but more are coming.

Advanced conditional logic take away the basic "Match all or one" and gives you conditional groups. This allows you to create simple conditionals or complex, multi-condition matching.

Everything can be extended. For developers, there are enough hooks and filters to build on. From frontend handling, to form processing, to editor panels, to field types. At it's core, it's a framework for building applications so you can make what you want.

This is only the first version and much more is coming.

For issues and updates - Caldera Forms is on [GitHub](https://github.com/Desertsnowman/Caldera-Forms)

== Installation ==

Upload the caldera-forms folder to /wp-content/plugins/
Activate the plugin through the 'Plugins' menu in WordPress
Navigate to 'Caldera Forms' in wp-admin.

Once you have created a form, insert it in a page or post via the Shortcode inserter button above the content editor.

== Frequently Asked Questions ==
none yet.

== Screenshots ==
1. **Layout Builder** - Easy to use Drag & Drop grid based layout.
2. **Stackable Form Processors** - Add a form processor to handle the forms submission. Stack them up to apply multi processing.
3. **Great Looking Forms** - Create great looking forms.

== Changelog ==
= 1.0.6 (20 July, 2014) =

Additions:
* Range Slider field type
* Star Rating view filter (show starts in entry view)
* Auto generate mail body from submissions + {summary} magic tag
* field %field_slug% tags dynamically bound in HTML field type ( updates dynamically with field values )
* Gravitar field binding to antry capture to show gravitar of form submitter if email is available - else uses userid of logged in user.
* Trash, restore & delete for submission entry management.
* Bulk actions for submission management (trash, delete, restore & export)
* Form Processor return values are now bindable options for other processors
* Field tags are now bindable values for processors


Bug Fixes:
* max_input_limit for configurations. fixed without the need to update php.ini (w00t!)
* fixed conditionals support for IE8
* some minor fixes I can't remember right now.

Enhancements:
* Added processor meta values so that form processors can add to the form submission
* optimized js in editor UI
* more filters and actions for developers
* other stiff I can't remember

= 1.0.5 (13 July, 2014) =

Additions:
* Multi Page Forms (still some work to do to make it easier)
* Bulk option insert for select fields (dropdown, radio, checkboxes, toggle button)
* Magic Tags on fields and mailer
* tag conditionals on email message
* Ajax return filter
* Placeholder field to add a custom placeholder rather than using the lable.

Bug Fixes:
* Bug in ajax verification
* Missing checkmark image in chrome
* Conditionals on checkboxes now works
* Conditionals performance on frontend
* Calculations field responds to conditionals correctly
* Static field types (select options etc) cannot be minipulated from frontend. Preprocessing on submit restores set values.

Enhancements:
* Switched redirect filter and action order
* File upload method to use WordPress' handler to prevent issues on some installs.
* Field dragging reduces to a set block for easier field placement.

= 1.0.4 (20 June, 2014) =

* Added Ajax submissions option - found in General Settings.
* Added custom field class - field wrapper based
* Added general input masking for single line text field
* Added Form Exporting and Importing

= 1.0.3 (12 June, 2014) =

* Added custom input mask format for phone number
* Cleaned up form style
* Fixed bug in datepicker with no arrows showing
* Fixed text field showing behind star rating

= 1.0.2 (11 June, 2014) =

* Added Star Rating field
* Added Calculations

= 1.0.1 (10 June, 2014) =

* Added Phone Field Type
* Additional Hooks & Filters
* Some Bug fixes

= 1.0.0 =
Initial Release

== Upgrade Notice ==
still new, so nothing to upgrade.