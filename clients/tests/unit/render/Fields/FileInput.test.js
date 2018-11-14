import renderer from 'react-test-renderer';
import {Input} from "../../../../render/components/Fields/Input";
import {FileInput} from "../../../../render/components/Fields/FileInput";
import {MockFileFieldRenderer} from "./Mocks/MockFileFieldRenderer";
import {fileFieldConfigs} from "./Mocks/fileFieldConfigs";



import React from 'react';
import {MockRender} from "./Mocks/MockFileFieldRenderer";
import {mount} from "enzyme/build";

const handler = () => {
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
		expect(prepared.multiple).toEqual('true');
		expect(prepared.field.hasOwnProperty('required')).toEqual(true);
	});

	it('Prepares props and sets multiple prop to false when it should', () => {
		const prepared = FileInput.fieldConfigToProps(fileFieldConfigs.required_single_allow_png);
		expect(prepared.multiple).toEqual(false);
	});

	it('Prepares props and sets Previews(false) props', () => {
		const prepared = FileInput.fieldConfigToProps(fileFieldConfigs.required_single);
		expect(prepared.usePreviews).toEqual( false );
	});

  it('Prepares props and sets Previews(true) props', () => {
    const prepared = FileInput.fieldConfigToProps(fileFieldConfigs.required_single_allow_png);
    expect(prepared.usePreviews).toEqual( true );
  });

	it('Prepares props and sets previewStyle', () => {
		const prepared = FileInput.fieldConfigToProps(fileFieldConfigs.width_40_height_20);
    expect(prepared.previewHeight).toEqual( 20 );
    expect(prepared.previewWidth).toEqual( 40 );
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
	});

});


