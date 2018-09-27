import renderer from 'react-test-renderer';
import {FormSelectorNoGutenberg} from "../../../components/FormSelectorNoGutenberg";
import {CalderaHeader} from "../../../components/CalderaHeader";
import {PageBody} from "../../../components/PageBody";
import {StatusIndicator} from "../../../components/StatusIndicator";

const forms  = [
	{
		ID: 'CFreal',
		name: 'Become Real'
	},
	{
		ID: 'CFunreal',
		name: 'Become Unreal'
	},

];
describe( 'Form selector without Gutenberg dependency', () => {
	it( 'Has form selection', () => {
		const formSelector = renderer.create(
			<FormSelectorNoGutenberg
				forms={forms}
				onChange={() => {}}
				idAttr={'reality-selector'}
				selected={'CFunreal'}
			/>
		);
		expect( formSelector.toJSON()).toMatchSnapshot();
	})
});

describe( 'Caldera Header', () => {
	it( 'Works with no props', () => {
		const simpleHeader = renderer.create(
			<CalderaHeader/>
		);
		expect( simpleHeader.toJSON()).toMatchSnapshot();

	});

	it( 'Works with no name prop', () => {
		const headerWithName = renderer.create(
			<CalderaHeader
				name={'Settings'}
			/>
		);
		expect( headerWithName.toJSON()).toMatchSnapshot();

	});

	it( 'Can have menu a option', () => {
		const headerWithMenuItems = renderer.create(
			<CalderaHeader
				children={<li>Item</li>}
			/>
		);
		expect( headerWithMenuItems.toJSON()).toMatchSnapshot();
	});

	it( 'Can have menu options', () => {
		const headerWithMenuItems = renderer.create(
			<CalderaHeader
				children={[
					<li key={'1'}>Item 1</li>,
					<li key={'2'}>Item 2</li>
				]}
			/>
		);
		expect( headerWithMenuItems.toJSON()).toMatchSnapshot();
	});

});

describe( 'Page body', () => {
	it( 'Can have children', () => {
		const pageBody = renderer.create(
			<PageBody
				children={[
					<div key={'1'}>Item 1</div>,
					<div key={'2'}>Item 2</div>
				]}
			/>

		);
		expect( pageBody.toJSON()).toMatchSnapshot();

	});
});

describe( 'Status indicator', () => {
	it( 'Shows success', () => {
		const success = renderer.create(
			<StatusIndicator
				message={'It worked'}
				success={true}
				show={true}
			/>

		);
		expect( success.toJSON()).toMatchSnapshot();

	});

	it( 'Shows failure', () => {
		const failure = renderer.create(
			<StatusIndicator
				message={'It did not work'}
				success={false}
				show={true}
			/>

		);
		expect( failure.toJSON()).toMatchSnapshot();
	});

	it( 'Shows nothing', () => {
		const nothing = renderer.create(
			<StatusIndicator
				show={false}
			/>

		);
		expect( nothing.toJSON()).toMatchSnapshot();
	});


});
