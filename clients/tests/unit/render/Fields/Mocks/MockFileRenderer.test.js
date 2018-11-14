import {shallow, mount} from 'enzyme';
import EnzymeAdapter from '../../../createEnzymeAdapter'
import {fileFieldConfigs} from "./fileFieldConfigs";
import {MockFileFieldRenderer} from "./MockFileFieldRenderer";
import React from 'react';
import { FileInput } from '../../../../../render/components/Fields/FileInput'

//https://gist.github.com/josephhanson/372b44f93472f9c5a2d025d40e7bb4cc
function MockFile() { };

MockFile.prototype.create = function (name, size, mimeType) {
  name = name || "mock.txt";
  size = size || 1024;
  mimeType = mimeType || 'plain/txt';

  function range(count) {
    var output = "";
    for (var i = 0; i < count; i++) {
      output += "a";
    }
    return output;
  }

  var blob = new Blob([range(size)], { type: mimeType });
  blob.lastModifiedDate = new Date();
  blob.name = name;

  return blob;
};

describe( 'DOM testing file components', () => {

	it( 'Can change value', () => {
    const size = 1024 * 1024 * 2;
    const mock = new MockFile();
    const file = mock.create("pic.png", size, "image/png");
    let prepared = FileInput.fieldConfigToProps(fileFieldConfigs.required_single);
    let field = prepared.field;
		let component = mount(
			<MockFileFieldRenderer
				field={field}
			/>
		);

    expect(component.state('value')).toEqual([])
    expect(field['fieldValue']['name']).toBeUndefined()
    expect(field['fieldValue'].length).toBe(0)
		component.instance().onChange(file);
		expect(component.state('value')).toBe(file)
		expect(field['fieldValue']['name']).toBeDefined()

	});

	it( 'Can setIsInvalid', () => {
		const field = fileFieldConfigs.required_single_allow_png;
		const component = mount(
			<MockFileFieldRenderer
				field={field}
			/>
		);
		component.instance().setIsInvalid( true );
		expect(component.state('isInvalid')).toBe(true);
	});


	it( 'Can set disabled', () => {
		const field = fileFieldConfigs.required_single_allow_png;
		const component = mount(
			<MockFileFieldRenderer
				field={field}
			/>
		);
		component.instance().setIsInvalid( true );
		expect(component.state('isInvalid')).toBe(true);
	});


	it( 'Can set message', () => {
		const field = fileFieldConfigs.required_single_allow_png;
		const component = mount(
			<MockFileFieldRenderer
				field={field}
			/>
		);
		component.instance().setMessage('Fail',true );
		expect(component.state('message')).toBe('Fail');
	});


	it( 'If multiple upload option is not enabled and a file/value is set, button to trigger file upload disappears', () => {
    const size = 1024 * 1024 * 2;
    const mock = new MockFile();
    const file = mock.create("pic.png", size, "image/png");
    const prepared = FileInput.fieldConfigToProps(fileFieldConfigs.required_single);
    let field = prepared.field;
		field.fieldValue = [file];
		let component = mount(
			<MockFileFieldRenderer
				field={field}
			/>
		);
    component.setProps(prepared);
		expect(component.find( 'button.btn' ).length).toBe(0);
		expect(component.find( '.cf2-one-file-notice' ).length).toBe(1);

	});

	it( 'Shows image preview if is an image', () => {
		const size = 1024 * 1024 * 2;
		const mock = new MockFile();
		const file = mock.create("pic.png", size, "image/png");
    let prepared = FileInput.fieldConfigToProps(fileFieldConfigs.required_single_allow_png);
    let field = prepared.field;
    prepared.field.fieldValue = [file];
		const component = mount(
      <MockFileFieldRenderer
        field={field}
      />
		);
		component.setProps(prepared);
		expect(component.find( 'img.cf2-file-field-img-preview').length).toBe(1);
		expect(component.find( '.cf2-file-data').length).toBe(1);

	});

	it( 'Does not show image preview if is not an image', () => {
		const size = 1024 * 1024 * 2;
		const mock = new MockFile();
		const file = mock.create("index.html", size, "text/html");
    let prepared = FileInput.fieldConfigToProps(fileFieldConfigs.required_single);
    let field = prepared.field;
    prepared.field.fieldValue = [file];
		const component = mount(
			<MockFileFieldRenderer
				field={field}
			/>
		);
    component.setProps(prepared);
		expect(component.find( '.cf2-file-field-img-preview').length).toBe(0);
		expect(component.find( '.cf2-file-data').length).toBe(1);
	});


	it( 'Shows image preview with correct size', () => {
		const size = 1024 * 1024 * 2;
		const mock = new MockFile();
		let file = mock.create("pic.png", size, "image/png");
		file.preview = file.name;
    let prepared = FileInput.fieldConfigToProps(fileFieldConfigs.width_40_height_20);
    let field = prepared.field;
    prepared.field.fieldValue = [file];
		const component = mount(
			<FileInput
				field={field}
			/>
		);
    component.setProps(prepared);
		expect( component.find( '.cf2-file-field-img-preview' ).html() ).toBe("<img class=\"cf2-file-field-img-preview\" width=\"40\" height=\"20\" src=\"pic.png\" alt=\"pic.png\">");
	});

	it( 'Shows image preview with default size', () => {
		const size = 1024 * 1024 * 2;
		const mock = new MockFile();
		let file = mock.create("pic.png", size, "image/png");
    file.preview = file.name;
    let prepared = FileInput.fieldConfigToProps(fileFieldConfigs.required_single_allow_png);
    let field = prepared.field;
    prepared.field.fieldValue = [file];
		const component = mount(
			<FileInput
				field={field}
			/>
		);
    component.setProps(prepared);
    expect( component.find( '.cf2-file-field-img-preview' ).html() ).toBe("<img class=\"cf2-file-field-img-preview\" width=\"24\" height=\"24\" src=\"pic.png\" alt=\"pic.png\">");
	});


});
