import React from 'react';

import {
	CalderaFormsFieldPropType,
	CalderaFormsFieldRender,
	CalderaFormsRender, CalderaFormsFieldGroup
} from "../../../render/components";

describe('exports', () => {
	it('Exports CalderaFormsFieldPropType', () => {
		expect(typeof CalderaFormsFieldPropType).toBe('function');
	});
	it('Exports CalderaFormsFieldRender', () => {
		expect(typeof CalderaFormsFieldRender).toBe('function');
	});

	it('Exports CalderaFormsRender', () => {
		expect(typeof CalderaFormsRender).toBe('function');
	});
	it('Exports CalderaFormsFieldGroup', () => {
		expect(typeof  CalderaFormsFieldGroup).toBe('function');
	});
});