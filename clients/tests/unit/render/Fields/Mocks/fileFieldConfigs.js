export const fileFieldConfigs  = {
	required_single:
		{
			type: 'file',
			outterIdAttr: 'cf2-required_single',
			fieldId: 'required_single',
			fieldLabel: 'Required Single',
			fieldCaption: '',
			fieldPlaceHolder: '',
			required: true,
			fieldDefault: '',
			fieldValue: [],
			fieldIdAttr: 'required_single',
			configOptions:
				{
					multiple: 'false',
					multiUploadText: 'My multi Upload Text',
					allowedTypes: 'false',
          usePreviews: 'false',
					previewHeight: 24,
					previewWidth: 24,
				}
		},
	required_single_allow_png:
		{
			type: 'file',
			outterIdAttr: 'required_single_allow_png',
			fieldId: 'required_single_allow_png',
			fieldLabel: 'Required Single Allow .png',
			fieldCaption: '',
			fieldPlaceHolder: '',
			required: true,
			isRequired: {
				required: true
      },
			fieldDefault: '',
			fieldValue: [],
			fieldIdAttr: 'required_single',
			configOptions:
				{
					multiple: 'false',
					multiUploadText: 'Cool text for required_single_allow_png',
					allowedTypes: '.png',
          usePreviews: true,
          previewHeight: 24,
          previewWidth: 24,
				}
		},
	required_multiple_no_button_text:
		{
			type: 'file',
			outterIdAttr: 'cf2-required_multiple_no_button_text',
			fieldId: 'required_multiple_no_button_text',
			fieldLabel: 'Required Multiple No Button Text',
			fieldCaption: '',
			fieldPlaceHolder: '',
			required: true,
			fieldDefault: '',
			fieldValue: '',
			fieldIdAttr: 'required_multiple_no_button_text',
			configOptions:
				{
					multiple: 1,
					multiUploadText: false,
					allowedTypes: 'false',
          usePreviews: true,
					previewHeight: 24,
					previewWidth: 24,
				}
		},
	required_multiple_has_button_text:
		{
			type: 'file',
			outterIdAttr: 'cf2-required_multiple_has_button_text',
			fieldId: 'required_multiple_has_button_text',
			fieldLabel: 'Required Multiple Has Button Text',
			fieldCaption: '',
			fieldPlaceHolder: '',
			required: true,
			fieldDefault: '',
			fieldValue: '',
			fieldIdAttr: 'required_multiple_has_button_text',
			configOptions:
				{
					multiple: 1,
					multiUploadText: 'The Custom Text',
					allowedTypes: 'false',
					usePreviews: true,
					previewHeight: 24,
					previewWidth: 24,
				}
		},
	not_required_single:
		{
			type: 'file',
			outterIdAttr: 'cf2-not_required_single',
			fieldId: 'not_required_single',
			fieldLabel: 'Not Required Single',
			fieldCaption: '',
			fieldPlaceHolder: '',
			required: 'false',
			fieldDefault: '',
			fieldValue: '',
			fieldIdAttr: 'not_required_single',
			configOptions:
				{
					multiple: 'false',
					multiUploadText: false,
					allowedTypes: 'false',
          usePreviews: true,
					previewHeight: 24,
					previewWidth: 24,
				}
		},
	not_required_multiple_no_button_text:
		{
			type: 'file',
			outterIdAttr: 'cf2-not_required_multiple_no_button_text',
			fieldId: 'not_required_multiple_no_button_text',
			fieldLabel: 'Not Required Multiple No Button Text',
			fieldCaption: '',
			fieldPlaceHolder: '',
			required: 'false',
			fieldDefault: '',
			fieldValue: '',
			fieldIdAttr: 'not_required_multiple_no_button_text',
			configOptions:
				{
					multiple: 1,
					multiUploadText: false,
					allowedTypes: 'false',
          usePreviews: true,
					previewHeight: 24,
					previewWidth: 24,
				}
		},
	not_required_multiple_has_button_text:
		{
			type: 'file',
			outterIdAttr: 'cf2-not_required_multiple_has_button_text',
			fieldId: 'not_required_multiple_has_button_text',
			fieldLabel: 'Not Required Multiple Has Button Text',
			fieldCaption: '',
			fieldPlaceHolder: '',
			required: 'false',
			fieldDefault: '',
			fieldValue: '',
			fieldIdAttr: 'not_required_multiple_has_button_text',
			configOptions:
				{
					multiple: 1,
					multiUploadText: 'The Default Text',
					allowedTypes: 'false',
          usePreviews: true,
					previewHeight: 24,
					previewWidth: 24,
				}
		},
	width_40_height_20:
		{
			type: 'file',
			outterIdAttr: 'cf2-width_40_height_20',
			fieldId: 'width_40_height_20',
			fieldLabel: 'Preview style set',
			fieldCaption: '',
			fieldPlaceHolder: '',
			required: 'false',
			fieldDefault: '',
			fieldValue: [],
			fieldIdAttr: 'width_40_height_20',
			configOptions:
				{
					multiple: 1,
					multiUploadText: 'The Default Text',
					allowedTypes: 'false',
          usePreviews: true,
					previewHeight: 20,
					previewWidth: 40,
				}
		}
};
