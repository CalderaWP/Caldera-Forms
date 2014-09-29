=== Caldera Forms ===
Contributors: Desertsnowman
Tags: forms, formbuilder, form builder, contact form, contact, custom form, custom forms, forms creator
Requires at least: 3.9
Tested up to: 4.0
Stable tag: 1.1.7
License: GPLv2

Create complex grid based, responsive forms easily with an easy to use drag and drop layout builder.

== Description ==
Caldera Forms is a free, simple form builder with a layout builder that enables you to create the format you want for your form. It is fully responsive and with form processors, it gives you flexibility to handle the form data how you need it.

For issues and updates - Caldera Forms is on [GitHub](https://github.com/Desertsnowman/Caldera-Forms)
Form Docs, Demos & Templates can be found on the docs site (work in progress) [Caldera Forms Docs](http://docs.calderaforms.com/)

= A Few Feature Highlights =
* Responsive Grid design based on Bootstrap 3
* Advanced Conditionals allows for multi, complex matching
* Form Processors allows for stacking up form functionality, conditionaly
* Export and Import forms across installations
* Ajax or Page reload
* Multipage forms
* CSV of submission attached to notification email
* Auto Responder
* File Uploads

= Auto Values & Magic Tags =
Capture system values, Post/Page data and custom fields, User data, and processor returns.

= Current Available Fields (more can be added on request) =
* Calculation
* Range Slider
* Star Rating
* Phone
* Text - Includes Custom Masking
* File Upload
* reCaptcha
* HTML content block - with dynamic data binding ( %field_slug% updates dynamically in html )
* Hidden
* Button
* Email
* Paragraph
* Toggle Switch
* Dropdown Select
* Checkbox
* Radio
* Date Picker
* Color Picker

Everything can be extended. For developers, there are enough hooks and filters to build on. From frontend handling, to form processing, to editor panels, to field types. At it's core, it's a framework for building applications so you can make what you want.

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

= 1.1.7 (29 September, 2014) =

= Fixed =
* Javascript error on deleting entries in pin mode
* Minor bug fixes

= Improved =
* CSV exporter improvements

= Added =
* Ajax submissions on by default

= Updated =
* Balrick.JS updated to 2.3
* Ajax spinner removed and replaced with overlay blocking.
* Addon licensing v3

= 1.1.6 (25 September, 2014) =

= Added =
* Akismet spam filtering processor. Listed once Askismet plugin is active and activated.
* Honeypot spamtrap option to place an invisible field in the form to trick spam bots.

= Fixed =
* Checkboxes, Radio & Dropdown select Show Values option corrected to show the values as intended.
* Better Instancing of multiple forms on page.
* Minor activation output error.
* Minor Bugfixes.

= 1.1.5 (18 September, 2014) =

= Fixed =
* corrected typos in the textdomain
* Validation classes added to Alerts and not Forms.
* Minor bug fixes that where annoying me

= Updated =
* Handlebars.js v2

= Added =
* Gravatar field for live display of gravatar.

= 1.1.4 (17 August, 2014) =

= Improved =
* Loading speed improved on form editor
* More Control hooks for form control

= Fixed =
* "Contains" clause in conditionals works correctly
* multi-site fix

= Added =
* option to clone form
* disable condition option for fields
* 'entry_id' magic tag
* Magic tag enabled all mailer fields


= 1.1.3 (12 August, 2014) =

= Fixed =
* foreach warning in new installs.
* language load corrected
* minor bugs

= 1.1.2 (6 August, 2014) =

= Fixed =
* datepicker clashing with jquery ui.
* Conditionals working correctly again round 2.
* validation across pages
* minor bugs

= Added =
* Pin to menu option to make direct access to entries way faster
* Extend menu item for addons and licenses 
* Community menu item for sharing stuff


= 1.1.1 (4 August, 2014) =

= Fixed =
* (partly) Success Message magic tags work on ajax mode. not yet on standard submit.
* Conditionals working correctly again.

= 1.1.0 (1 August, 2014) =

= Added =
* Variables tab: create custom magic tags and URL return values by combining other tags, processor values and static strings.
* Magic tag autocomplete to magic tag enabled inputs in editor.
* Autopopulate options from post type or taxonomy for static select fields (radio, checkboxes, dropdown, toggle switches)


= Enhanced =
* Rebuilt the get_entry and view methods to be easier to use for developers and be more reliable
* Form Instancing and field ID's - can have multiple instances of the same form.
* Additional hooks and filters

= 1.0.91 (27 July, 2014) =

= Bugfix =
* Fixed the preview button

= Added =
* Form ID added as form class

= 1.0.9 (25 July, 2014) =

= Bugfix =
* Conditionals error on numerical condition value
* placeholder field took preference on masked input instead of default field

= Additions =
* Form preview button
* Processors can now return an error to stop process chain
* Process transient now accessable for storing process data for redirects and such
* Field ID added to field config panel for reference
* Extra checks for valid data
* Extra filters
* Meta Data view templates to processors

= 1.0.8 (21 July, 2014) =

= Bugfix =
* PHP 5.2+ compatibility fix on grid generation
* Multi-page bug that stopped the page config from being saved
* Range Slider bug fixed that broke sliders on multipage forms

= 1.0.7 (20 July, 2014) =

= Bugfix =
* Left off an important table update for the status- very sorry. I hate doing two updates in a day.

= 1.0.6 (20 July, 2014) =

= Additions =
* Range Slider field type
* Star Rating view filter (show starts in entry view)
* Auto generate mail body from submissions + {summary} magic tag
* field %field_slug% tags dynamically bound in HTML field type ( updates dynamically with field values )
* Gravitar field binding to antry capture to show gravitar of form submitter if email is available - else uses userid of logged in user.
* Trash, restore & delete for submission entry management.
* Bulk actions for submission management (trash, delete, restore & export)
* Form Processor return values are now bindable options for other processors
* Field tags are now bindable values for processors


= Bug Fixes =
* max_input_limit for configurations. fixed without the need to update php.ini (w00t!)
* fixed conditionals support for IE8
* some minor fixes I can't remember right now.

= Enhancements =
* Added processor meta values so that form processors can add to the form submission
* optimized js in editor UI
* more filters and actions for developers
* other stiff I can't remember

= 1.0.5 (13 July, 2014) =

= Additions =
* Multi Page Forms (still some work to do to make it easier)
* Bulk option insert for select fields (dropdown, radio, checkboxes, toggle button)
* Magic Tags on fields and mailer
* tag conditionals on email message
* Ajax return filter
* Placeholder field to add a custom placeholder rather than using the lable.

= Bug Fixes =
* Bug in ajax verification
* Missing checkmark image in chrome
* Conditionals on checkboxes now works
* Conditionals performance on frontend
* Calculations field responds to conditionals correctly
* Static field types (select options etc) cannot be minipulated from frontend. Preprocessing on submit restores set values.

= Enhancements =
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