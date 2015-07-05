=== Caldera Forms ===
Contributors: Desertsnowman, Shelob9
Tags: forms, formbuilder, form builder, contact form, contact, custom form, custom forms, forms creator, caldera forms
Requires at least: 3.9
Tested up to: 4.2.2
Stable tag: 1.2.3
License: GPLv2

Create beautiful, responsive forms with a simple drag and drop editor.

== Description ==
A diffent kind of WordPress form builder. With an intuitive drag and drop interface -– based on a responsive grid -- and a wide range of add-ons, it’s never been easier to create forms for your WordPress site that look great on any device, thanks to Caldera Forms. This free plugin includes all of the form types you want, mail and redirect processors, entry logging and AJAX submissions.

Easy enough for everyday users to create forms, and powerful enough for PHP experts to extend.

A free plugin by <a href="https://CalderaWP.com" title="CalderaWP: Transform Your WordPress Experience">CalderaWP</a>.

Pippin Williamson of Easy Digital Downloads, Restrict Content Pro and AffiliateWP gives [Caldera Forms a 5 star rating](https://themesurgeons.com/wordpress-plugins-recommendations/)!

John Teague of Theme Surgeons includes Caldera Forms in his list of [WordPress plugin recommendations I don’t get paid for](https://themesurgeons.com/wordpress-plugins-recommendations/).

= Docs & More Information =
* [More Information](https://calderawp.com/downloads/caldera-forms/)
* [Documentation](http://docs.calderaforms.com/)


= Addons =
[All Add-ons](https://calderawp.com/caldera-forms-add-ons/)

= Free Add-ons =
* [Verify Email for Caldera Forms](https://calderawp.com/downloads/verify-email-for-caldera-forms/) - Send an email with a validate link to verify the email address before completing the form submission.
* [Form as Metabox - Custom Fields](https://calderawp.com/downloads/caldera-form-metabox/) - Use a Caldera Form as a metabox in the post editor to save custom field values.
* [Slack Integration for Caldera Forms](https://calderawp.com/downloads/caldera-forms-slack-integration/) - Get notifications in Slack whenever a Caldera Form is submitted.
* [Run Action](https://calderawp.com/downloads/caldera-forms-run-action/) - Trigger a WordPress action with your form submission.
* [Sprout Invoices Integration](https://wordpress.org/plugins/caldera-forms-sprout-invoices-integration/) - Use Caldera Forms for [Sprout Invoice](https://sproutapps.co/sprout-invoices/) forms.
* [Conditional Fail](https://wordpress.org/plugins/conditional-fail-for-caldera-forms/) - Set conditions to cause that if met will allow or prevent form submssion.

= Premium Add-Ons =
* [MailChimp](https://calderawp.com/downloads/caldera-forms-mailchimp-add-on/) - Seamlessly integrate MailChimp optins into your forms
* [Stripe](https://calderawp.com/downloads/caldera-forms-stripe-add-on/) - Accept credit card payments via Stripe.
* [PayPal Express](https://calderawp.com/downloads/caldera-forms-paypal-express-add-on/) - Accept payments via Paypal Express.
* [Users](https://calderawp.com/downloads/caldera-forms-users-add/) - Register or login users from your form.
* [Geolocation](https://calderawp.com/downloads/geolocation-field-for-caldera-forms/) - Make a text field a geolocation auto-complete field and recorded geocoded data.
* [Mark Viewed](https://calderawp.com/downloads/caldera-forms-mark-viewed/) - Let users track what content they have viewed.

= A Few Feature Highlights =
* Drag and drop responsive form builder.
* Responsive grid is based on Bootstrap 3
* Process forms without a page load using AJAX
* Advanced conditional logic for hidding and showing fields.
* Export and Import forms across installations
* Multi-page forms
* Emails sent on form submission are easily customized and can include a CSV file of entry data.
* Configurable auto-responder and redirections.
* File Uploads

= Auto Values & Magic Tags =
Capture system values, form data, user data and use it populate form fields values, variables, or in response emails.

= Current Available Fields =
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
2. **Form Processors** - With many processors available and more coming, each form can handle submissions in very unique and powerful ways.
3. **Stackable, condition based Form Processors** - Add as many form processors as needed and add conditions for each to create specific processing flow based on user input.
4. **Great Looking Forms** - Create great looking forms.

== Changelog ==

= 1.2.3 ( , 2015) =

= Bugs Fixed =
* 

= New Features =
* Added form templates when creating new form.
* Automcomplete (select2) field types.

= New Filters =
* caldera_forms_get_form_templates : filter to add your own templates to new form templates


= 1.2.2 ( June, 2015) =

= Bugs Fixed =
* Fixed widget bug where forms in widget disapeared
* Fixed conditional logic where multiple forms on a page broke conditions
* Fixed a bug that stopped forms from saving if visual editor was disabled in profile
* A bunch of smaller bugs that was causing minor issues... again

= New Filters =
* caldera_forms_autopopulate_options_post_value_field
* caldera_forms_autopopulate_options_post_label_field
* caldera_forms_autopopulate_options_taxonomy_value_field
* caldera_forms_autopopulate_options_taxonomy_label_field


= 1.2.1 ( June, 2015) =

= Bugs Fixed =
* Fixed checkbox values not saving on transient redirect ( complicated, but now fixed )
* Fixed required field on HTML element causing a form to not submit without any notice
* Fixed issue with pin to menu rights resetting in form edit
* A bunch of smaller bugs that was causing minor issues

= Improvements =
* Updated the visual styles on forms
* updated mailer setup descriptions to prevent spam filtering of mails
* auto slugs are cleaner
* minor speed improvements in core

= 1.2.0 ( April, 2015) =

= Bugs Fixed =
* Exports on checkboxes caused issues
* calculation caused problems
* Fixed the slug:label magic tag...again.

= Improvements =
* Updated the visual styles
* Added additional actions and filters
* imroved saving speed


= 1.1.10 ( April, 2015) =

= Bugs Fixed =
* deleting entries in bulk, disabled form. - fixed.
* viewing entries occationally gave a blank modal.

= 1.1.9.10 ( April, 2015) =
* error on checkboxes and array tag showing

= 1.1.9.9 ( April, 2015) =

= Critical Bug fix =
* on php 5.3 widget forms and function render forms gave "permission denied" error. solved.

= 1.1.9.8 ( April, 2015) =

= Improvements =
* Added setting for custom thousand separator on calculator in money format.
* improved array handling
* set conditions to look at lable not value.
* slugs from option based fields can now reference the lable with %field_slug:label%

= Bug fixes =
* Corrected a bug in the file upload not stopping on incorrect file type
* fixed an action in the autopopulate options for field config
* fixed a bug that made the field bind autocomplete box dissapear when scrolling



= 1.1.9.7 ( April, 2015) =

= Bug fixes =
* Corrected a bug that allowed setting a field to its own conditional (infinte loop)

= Additions =
* Added a "Entry List" behaviour to Variables to allow the variable to show in entry list.
* Added filter 'caldera_forms_get_form' for filtering form structure before using it.
* Added the ability to render forms directly from an array structure ( Experimental dev feature : Allows you to render forms from a structure without needing to import one ).

= Improvements =
* Made selected field in edit easier to see.
* Added a Drfat / Deactivate mode for forms.

= 1.1.9.6 ( April, 2015) =

= Bug fixes =
* slashes removed on mailer body
* Reset fixed for toggle buttons working
* reCaptcha multi instances

= Improvements =
* changed the delete element and processor buttons to gray
* changed the Success message box to a textarea for larger notices.

= Added =
* added custom ajax callbacks and overrides
* Increment value processor
* added BCC and Reply To options for mailer
* Mailer Debug mode to track issues with sending notifications

= 1.1.9.5 ( March, 2015) =

= Bug fixes =
* Fixed datepicker language loader ... again
* Pagination next validator on checkbox prevented progression

= Improvements =
* cleaned up the view entry modal a bit
* cleaned up paragraph entry view
* moved a few hooks around for better handling
* added custom class to html element


= 1.1.9.4 ( March, 2015) =

= Improvements =
* Added US States to dropdown as option

= Bug fixes =
* Fixed a clash with FacetWP pagination
* Fixed a bug that stoped the text editor from running on WordPress 3.9
* A few minor bug fixes and improvements


= 1.1.9.3 ( Febuary, 2015) =

= Improvements =
* Added ID or Name value selector for autopopulation on selects
* Importer now creates a new form and wont overide the original.
* Modals only close on the dismiss or cancel buttons. no longer on clicking the overlay. (better for management)
* Added Filter: `caldera_forms_autoresponse_config` to allow modifiying the auto responder config
* Added Filter: `caldera_forms_autoresponse_mail` to allow modifiying the auto responder mail object before sending
* Changed the form ajax handler to use the more reliable wp admin-ajax method
* Hooks to extend the autopopulate for option based fields ( caldera_forms_autopopulate_types & caldera_forms_autopopulate_type_config )

= Updated =
* Updated the reCaptcha to use Google's new version

= Bug Fix =
* a few small minor issues where resolved.


= 1.1.9.2 (30 November, 2014) =

= Bug Fix =
* Issue with HTML binding not working. Solved but need to explore a little more later.
* a few smaller bug fixes & optimisations

= 1.1.9.1 (27 october, 2014) =

= Bug Fix =
* Sorted an issue with custom classes not being an array. *facepalm*

= 1.1.9 (26 october, 2014) =

= Fixed =
* multi page validation on radios + checkboxes
* minor bug fixes

= Improved =
* {embed_post:*} available in archive list

= Added =
* Live preview on shortcode in editor. really. It's awesome.
* Additional filters to aid in auto-populating fields

= 1.1.8 (7 October, 2014) =

= Fixed =
* Javascript error in reCapture field
* No Default on dropdown field type results in correct blank option
* Minor bug fixes

= Improved =
* Moved entry creation to just after pre-processor to allow for {entry_id} to be usable
* Class wrapper locations improved for better addons

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
Autoupdated in WordPress admin.
