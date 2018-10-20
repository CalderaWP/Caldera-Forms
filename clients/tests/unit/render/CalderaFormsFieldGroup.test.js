import {CalderaFormsFieldGroup} from "../../../render/components";
import renderer from 'react-test-renderer';

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
	const handler = () => {
	};
	it('Matches snapshot with basic args', () => {
		expect(
			renderer.create(
				<CalderaFormsFieldGroup
					onChange={handler}
					field={fieldConfig}
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
				/>
			).toJSON()
		).toEqual(null)
	});

	it('Creates the label correctly', () => {
		const testRenderer = renderer.create(<CalderaFormsFieldGroup
			onChange={handler}
			field={fieldConfig}
			shouldDiable={true}
		/>);
		const testInstance = testRenderer.root;

		expect(testInstance.findByType('label').props.htmlFor).toBe(fieldIdAttr);
		expect(testInstance.findByType('label').props.className).toBe('control-label');
		expect(testInstance.findByType('label').props.children).toBe(fieldConfig.fieldLabel);


	});


	it('Creates the inner input correctly', () => {
		const testRenderer = renderer.create(<CalderaFormsFieldGroup
			onChange={handler}
			field={fieldConfig}
		/>);
		const testInstance = testRenderer.root;

		expect(testInstance.findByType('input').props.required).toBe(false);
		expect(testInstance.findByType('input').props.className).toBe('form-control');
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
		expect(testInstance.findByType('span').props.className).toBe( 'help-block');
		expect(testInstance.findByType('span').props.children).toBe( 'Click Me');




	});

});