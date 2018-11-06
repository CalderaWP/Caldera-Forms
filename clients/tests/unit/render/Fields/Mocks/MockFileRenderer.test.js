import {shallow,mount} from 'enzyme';
import EnzymeAdapter from '../../../createEnzymeAdapter'
import {fileFieldConfigs} from "./fileFieldConfigs";
import {MockFileFieldRenderer} from "./MockFileFieldRenderer";
import React from 'react';

describe( 'DOM testing file components', () => {

	it( 'Can change value', () => {
		const field = fileFieldConfigs.required_single_allow_png;
		const component = mount(
			<MockFileFieldRenderer
				field={field}
			/>
		);
		component.instance().onChange( 'seven' );
		expect(component.state('value')).toBe('seven')
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

});

describe( 'DOM testing file components', () => {
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

	it( 'If multiple upload option is not enabled, can not add a second file', () => {
		const field = fileFieldConfigs.required_single_allow_png;
		const component = mount(
			<MockFileFieldRenderer
				field={field}

			/>
		);
		expect(component.find( '.btn').length).toBe(1);
		component.instance().onChange(['File blob']);
		expect(component.find( '.btn').length).toBe(0);
	});

	it( 'Shows image preview if is an image', () => {
		const size = 1024 * 1024 * 2;
		const mock = new MockFile();
		const file = mock.create("pic.png", size, "image/png");
		const field = fileFieldConfigs.required_single_allow_png;
		const component = mount(
			<MockFileFieldRenderer
				field={field}

			/>
		);
		component.instance().onChange([file]);
		expect(component.find( '.cf2-file-field-img-preview').length).toBe(1);
		expect(component.find( '.cf2-file-data').length).toBe(1);

	});

	it( 'Does not show image preview if is not an image', () => {
		const size = 1024 * 1024 * 2;
		const mock = new MockFile();
		const file = mock.create("index.html", size, "text/html");
		const field = fileFieldConfigs.required_single;
		const component = mount(
			<MockFileFieldRenderer
				field={field}

			/>
		);
		component.instance().onChange([file]);
		expect(component.find( '.cf2-file-field-img-preview').length).toBe(0);
		expect(component.find( '.cf2-file-data').length).toBe(1);
	});


	it( 'Shows image preview with correct size', () => {
		const size = 1024 * 1024 * 2;
		const mock = new MockFile();
		const file = mock.create("pic.png", size, "image/png");
		const field = fileFieldConfigs.width_40_height_20;
		const component = mount(
			<MockFileFieldRenderer
				field={field}

			/>
		);
		component.setState({value:[file]});
		expect(component.find( '.cf2-file-field-img-preview').prop( 'width' ) ).toEqual(40);
		expect(component.find( '.cf2-file-field-img-preview').first().prop( 'height' ) ).toEqual(20);
	});

	it( 'Shows image preview with default size', () => {
		const size = 1024 * 1024 * 2;
		const mock = new MockFile();
		const file = mock.create("pic.png", size, "image/png");
		const field = fileFieldConfigs.required_single_allow_png;
		const component = mount(
			<MockFileFieldRenderer
				field={field}

			/>
		);
		component.setState({value:[file]});
		expect(component.find( '.cf2-file-field-img-preview').prop( 'width' ) ).toEqual(24);
		expect(component.find( '.cf2-file-field-img-preview').first().prop( 'height' ) ).toEqual(24);
	});
});
