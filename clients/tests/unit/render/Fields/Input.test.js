import React from 'react';
import renderer from 'react-test-renderer';
import{Input} from "../../../../render/components/Fields/Input";
describe( 'Input field', () => {
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
		required: false
		,
		type: "text",
	};
	const handler = () => {
	};


	it( 'matches snapshot with basic args', () => {
		expect(renderer.create(
			<Input
				onChange={handler}
				shouldDisable={false}
				field={fieldConfig}
			/>
			).toJSON()
		).toMatchSnapshot();
	});

	it( 'prints aria attribute', () => {

		expect(renderer.create(
			<Input
				onChange={handler}
				shouldDisable={false}
				field={fieldConfig}
				describedById={'foo'}
			/>
			).toJSON()
		).toMatchSnapshot();


	});

});