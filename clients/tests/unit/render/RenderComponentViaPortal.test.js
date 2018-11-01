import {RenderComponentViaPortal} from "../../../render/components/RenderComponentViaPortal";
import renderer from 'react-test-renderer';
import {mount} from 'enzyme';
import EnzymeAdapter from '../createEnzymeAdapter'
const handler = () => {};


describe('Portal rendering', () => {
	let element;
	let elementId;
	beforeEach( () =>{
		elementId =  Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
		element = document.createElement('div');
		element.setAttribute('id', elementId );
		document.body.appendChild(element);
	});

	afterEach( () => {
		document.body.removeChild(element);
		elementId = null;

	});

	it('Test set up is creating dom nodes correctly', () => {
		expect( document.getElementById(elementId)).toBeDefined();
	});

	it('Renders a child', () => {
		const component =
			mount(
				<RenderComponentViaPortal domNode={document.getElementById(elementId)}>
					<p>Hi Roy</p>
				</RenderComponentViaPortal>
			);
		expect( component.children().children.length ).toBe(1);
		expect( component.children().contains('Hi Roy') ).toBe(true);
	});

	it('Renders many children', () => {
		const Something = () => (<div>Something</div>);
		const component =
			mount(
				<RenderComponentViaPortal domNode={document.getElementById(elementId)}>
					<Something/>
					<div>line2</div>
					<p>Hi Roy</p>
				</RenderComponentViaPortal>
			);
		expect( component.children().contains('Something') ).toBe(true);
		expect( component.children().contains('line2') ).toBe(true);
		expect( component.children().contains('Hi Roy') ).toBe(true);
	});

	it( 'Modifies dom node', () => {
		const component =
			mount(
				<RenderComponentViaPortal domNode={document.getElementById(elementId)}>
					<p>Hi Roy</p>
				</RenderComponentViaPortal>
			);

		expect( document.getElementById( elementId ).innerHTML ).toBe( '<p>Hi Roy</p>' );
	});
});
