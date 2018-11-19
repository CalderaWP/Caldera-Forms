import {CalderaFormsFieldGroup} from "../../../render/components";
import renderer from 'react-test-renderer';
const handler = () => {
};
import {shallow, mount} from 'enzyme';
import EnzymeAdapter from '../createEnzymeAdapter'


describe('CalderaFormsFieldGroup component', () => {
	const fieldIdAttr = 'fld_5843941_1';
	const fieldConfig = {
		fieldCaption: "",
		fieldDefault: "new default",
		fieldId: "fld_5843941",
		fieldIdAttr,
		fieldLabel: "Text 2 Field",
		fieldPlaceHolder: "",
		fieldValue: "",
		outterIdAttr: "cf2-fld_5843941_1",
		required: false,
		type: "text",
	};

	const getFieldConfig = (fieldIdAttr) =>{
		return fieldConfig;
	};
	it('Matches snapshot with basic args', () => {
		expect(
			renderer.create(
				<CalderaFormsFieldGroup
					onChange={handler}
					field={fieldConfig}
					getFieldConfig={getFieldConfig}
				/>
			).toJSON()
		).toMatchSnapshot();
	});


	it('Does not render if shouldShow is false', () => {
		expect(
			renderer.create(
				<CalderaFormsFieldGroup
					onChange={handler}
					field={fieldConfig}
					shouldShow={false}
					getFieldConfig={getFieldConfig}

				/>
			).toJSON()
		).toEqual(null)
	});

	it('Creates the label correctly', () => {
		const testRenderer = renderer.create(<CalderaFormsFieldGroup
			onChange={handler}
			field={fieldConfig}
			shouldDiable={true}
			getFieldConfig={getFieldConfig}
		/>);
		const testInstance = testRenderer.root;

		expect(testInstance.findByType('label').props.htmlFor).toBe(fieldIdAttr);
		expect(testInstance.findByType('label').props.className).toBe('control-label');


	});

	it('Creates the label with the right text', () => {
		const testRenderer = mount(<CalderaFormsFieldGroup
			onChange={handler}
			field={fieldConfig}
			shouldDiable={true}
			getFieldConfig={getFieldConfig}
		/>);
		const testInstance = testRenderer.root;


		expect(testRenderer.contains(fieldConfig.fieldLabel) ).toBe( true );


	});


	it('Creates the inner input correctly', () => {
		const testRenderer = renderer.create(<CalderaFormsFieldGroup
			onChange={handler}
			field={fieldConfig}
			getFieldConfig={getFieldConfig}
		/>);
		const testInstance = testRenderer.root;

		expect(testInstance.findByType('input').props.required).toBe(false);
		expect(testInstance.findByType('input').props.className).toBe('cf2-text form-control');
		expect(testInstance.findByType('input').props.placeholder).toBe('');
		expect(testInstance.findByType('input').props.type).toBe('text');
		expect(testInstance.findByType('input').props.value).toBe('');
		expect(typeof testInstance.findByType('input').props.onChange).toBe('function');


	});

	it('Disables is shouldDisable is true', () => {
		const testRenderer = renderer.create(<CalderaFormsFieldGroup
			onChange={handler}
			field={fieldConfig}
			shouldDisable={true}
			getFieldConfig={getFieldConfig}
		/>);
		const testInstance = testRenderer.root;

		expect(testInstance.findByType('input').props.disabled).toBe(true);

	});

	it('Sets required if it should', () => {
		const testRenderer = renderer.create(<CalderaFormsFieldGroup
			onChange={handler}
			field={{
				...fieldConfig,
				required: true
			}}
			getFieldConfig={getFieldConfig}

		/>);
		const testInstance = testRenderer.root;

		expect(testInstance.findByType('input').props.required).toBe(true);

	});

	it('Adds caption and aria attribute', () => {
		const testRenderer = renderer.create(<CalderaFormsFieldGroup
			onChange={handler}
			field={{
				...fieldConfig,
				caption: "Click Me"
			}}
		/>);
		const testInstance = testRenderer.root;
		expect(testInstance.findByType('span').props.className).toBe('help-block');
		expect(testInstance.findByType('span').props.children).toBe('Click Me');

	});

});

describe('chooses inner field', () => {
	const fileFieldConfig = {
		field:
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
				fieldIdAttr: 'not_required_multiple_has_button_text'
			},
		accept: '',
		multiple: 'false',
		multiUploadText: 'false'
	};

	const textFieldConfig = {
		fieldCaption: "",
		fieldDefault: "new default",
		fieldId: "cf2-fdl1",
		fieldIdAttr: "fdl1_1",
		fieldLabel: "Text 2 Field",
		fieldPlaceHolder: "",
		fieldValue: "",
		outterIdAttr: "cf2-fdl1_1",
		required: false,
		type: "text",
	};

	const getFieldConfig = (fieldIdAttr) =>{
		return textFieldConfig;
	};
	it( 'uses text input for text field', () =>{
		const testRenderer = renderer.create(<CalderaFormsFieldGroup
			onChange={handler}
			field={textFieldConfig}
			getFieldConfig={getFieldConfig}
		/>);
		const testInstance = testRenderer.root;
		expect(testInstance.findByType('input').props.type).toBe('text');
		expect(testInstance.findByType('input').props.className).toBe('cf2-text form-control');

	});

	it( 'uses file input for text field', () =>{
		const testRenderer = renderer.create(<CalderaFormsFieldGroup
			onChange={handler}
			field={fileFieldConfig.field}
			getFieldConfig={getFieldConfig}
		/>);
		const testInstance = testRenderer.root;
		expect(testInstance.findByType('input').props.type).toBe('file');
		expect(testInstance.findByType('div').props.className).toBe('form-group cf2-field-group');
	});

});

describe( 'DOM testing of CalderaFormsFieldGroup component', () => {
	const textFieldConfig = {
		fieldCaption: "",
		fieldDefault: "new default",
		fieldId: "cf2-fdl1",
		fieldIdAttr: "fdl1_1",
		fieldLabel: "Text 2 Field",
		fieldPlaceHolder: "",
		fieldValue: "",
		outterIdAttr: "cf2-fdl1_1",
		required: false,
		type: "text",
		isRequired: true
	};

	it('Shows required indicator when it should', () => {
		const component = mount(<CalderaFormsFieldGroup
			onChange={handler}
			field={textFieldConfig}
			shouldDisable={true}
			getFieldConfig={() => {}}
		/>);

		expect(component.contains('*')).toBe(true);
		expect(component.find('.field_required').length).toBe(1);

	});
});
