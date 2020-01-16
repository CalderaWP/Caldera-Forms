# Form Builder

This client is used for adding new code to the form editor/ form builder. It is destined to replace assets/js/edit.js.


## Conditional Logic Editor

### Rules About What Fields A Conditional Can Be Applied To

Conditional rules can apply to 0 or more form fields. A field can only be applied by one conditional. Therefore:

* If a form field does not apply to any conditional groups, it should be an enabled option to the group being edited.
* If a form field does apply to the group being edited, it should be an enabled, and checked option.
* If a form field applies to a group that is not the group being edited, that field should be shown as a disabled, unchecked option.

