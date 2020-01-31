import React from 'react';

import renderer from 'react-test-renderer';
import {shallow} from 'enzyme';
import {LinkToFormEditor} from "../../../../blocks/components/LinkToFormEditor";
import EnzymeAdapter from '../../createEnzymeAdapter'

describe( 'Tests for LinkToFormEditor', () => {

	it('Check component props', () => {
		const formId = "formID-1";
		const linkComponent = renderer.create(
			<LinkToFormEditor formId={formId} />
		);
		expect(linkComponent.root.props).toEqual({ formId: "formID-1" });
	});

	it('Check component html', () => {
		const formId = "formID-2";
		const linkComponent = shallow(
			<LinkToFormEditor formId={formId} />
		);
		expect(linkComponent.html()).toBe('<div><a href=\"/wp-admin/admin.php?edit=formID-2&amp;page=caldera-forms\" title=\"Edit Caldera Form\">Edit Form</a></div>');
	});

});