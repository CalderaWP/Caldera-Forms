import renderer from 'react-test-renderer';
import {Input} from "../../../../render/components/Fields/Input";
import {FileInput} from "../../../../render/components/Fields/FileInput";
const fileFieldConfigs  = {
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
			fieldValue: '',
			fieldIdAttr: 'required_single',
			configOptions:
				{
					multiple: 'false',
					multiUploadText: 'false',
					allowedTypes: 'false'
				}
		},
	required_single_allow_png:
		{
			type: 'file',
			outterIdAttr: 'cf2-required_single',
			fieldId: 'required_single',
			fieldLabel: 'Required Single',
			fieldCaption: '',
			fieldPlaceHolder: '',
			required: true,
			fieldDefault: '',
			fieldValue: '',
			fieldIdAttr: 'required_single',
			configOptions:
				{
					multiple: 'false',
					multiUploadText: 'false',
					allowedTypes: 'image/png'
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
					multiple: 'true',
					multiUploadText: 'false',
					allowedTypes: 'false'
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
					multiple: 'true',
					multiUploadText: 'The Default Text',
					allowedTypes: 'false'
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
					multiUploadText: 'false',
					allowedTypes: 'false'
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
					multiple: 'true',
					multiUploadText: 'false',
					allowedTypes: 'false'
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
					multiple: 'true',
					multiUploadText: 'The Default Text',
					allowedTypes: 'false'
				}
		}
};

describe('File Field ', () => {
	

	const handler = () => {
	};
	it('We have the file field configs to test with', () => {
		expect(fileFieldConfigs.required_single.fieldId).toBe('required_single');
		Object.keys(fileFieldConfigs).forEach(fieldId => {
			expect(fileFieldConfigs[fieldId].hasOwnProperty('configOptions')).toBe(true);
			expect(FileInput.fieldConfigToProps(fileFieldConfigs[fieldId]).hasOwnProperty('multiple')).toBe(true);
			expect(FileInput.fieldConfigToProps(fileFieldConfigs[fieldId]).hasOwnProperty('multiUploadText')).toBe(true);
			expect(FileInput.fieldConfigToProps(fileFieldConfigs[fieldId]).hasOwnProperty('accept')).toBe(true);
		});
	});

	it('Prepares props from field config', () => {
		const prepared = FileInput.fieldConfigToProps(fileFieldConfigs.not_required_multiple_has_button_text);
		expect(prepared.hasOwnProperty('multiple')).toBe(true);
		expect(prepared.hasOwnProperty('multiUploadText')).toBe(true);
		expect(prepared.hasOwnProperty('configOptions')).toBe(false);

	});


	it.skip('Prepares props from field config that is required', () => {
		const prepared = FileInput.fieldConfigToProps(fileFieldConfigs.required_multiple_has_button_text);
		console.log(fileFieldConfigs);

		expect(prepared.multiple).toEqual('true');
		expect(prepared.field.hasOwnProperty('required')).toEqual(true);
	});

	it('Prepares props and sets multiple prop to false when it should', () => {
		const prepared = FileInput.fieldConfigToProps(fileFieldConfigs.required_single_allow_png);
		expect(prepared.multiple).toEqual('false');
	});

	it.skip('Prepares props and sets the allowed types in inputProps when it should', () => {
		const prepared = FileInput.fieldConfigToProps(fileFieldConfigs.required_single);
		//expect( fileFieldConfigs ).toEqual( 'image/png');
		expect(prepared.accept).toEqual('image/png');
		expect(prepared.inputProps).toEqual({"accept": "image/png", "type": "file"}
		);
	});

	Object.keys(fileFieldConfigs).forEach(fieldId => {
		it(`Matches snapshot for ${fieldId}`, () => {
			const field = fileFieldConfigs[fieldId];
			const prepared = FileInput.fieldConfigToProps(fileFieldConfigs.not_required_multiple_has_button_text);
			const {multiple, multiUploadText, inputProps} = prepared;
			expect(
				renderer.create(<FileInput
					field={field}
					multiple={multiple}
					multiUploadText={multiUploadText}
					onChange={handler}
					inputProps={inputProps}
				/>).toJSON()
			).toMatchSnapshot();
		});
	})
});