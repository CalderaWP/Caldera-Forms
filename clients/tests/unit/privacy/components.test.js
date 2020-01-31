import React from 'react';

import renderer from 'react-test-renderer';
import {FieldGroup} from "../../../privacy/components/FieldGroup";
import {HelpBox} from "../../../privacy/components/HelpBox";
import {DocLinks} from "../../../privacy/components/DocLinks";
import {IsEmailIdentifyingField} from "../../../privacy/components/IsEmailIdentifyingField";
import {FormPrivacySettings} from "../../../privacy/components/FormPrivacySettings";
import {FieldsPrivacySettings} from "../../../privacy/components/FieldsPrivacySettings";
import {FieldPrivacySettings} from "../../../privacy/components/FieldPrivacySettings";
import {IsPiiField} from "../../../privacy/components/IsPiiField";

describe( 'Field group component', () => {
	it('Works with help text',() => {
		const fieldGroup = renderer.create(
			<FieldGroup
				id={'control-22'}
				label={'Who'}
				help={'Who to say hi to'}
			/>
		);
		expect( fieldGroup.toJSON() ).toMatchSnapshot();
	});

	it('Help text is optional',() => {
		const fieldGroup = renderer.create(
			<FieldGroup
				id={'control-22'}
				label={'Who'}
			/>
		);
		expect( fieldGroup.toJSON() ).toMatchSnapshot();
	});
});

describe( 'Helpbox', () => {
	it( 'Does what it does. This needs broken up', () => {
		const helpBox = renderer.create(
			<HelpBox
				saveButton={<button>Push Me</button>}
			/>
		);
		expect( helpBox.toJSON() ).toMatchSnapshot();

	});
});

describe( 'Docs links', () => {
	it( 'Renders', () => {
		const helpBox = renderer.create(
			<DocLinks/>
		);
		expect( helpBox.toJSON() ).toMatchSnapshot();

	});
});

describe( 'Settings', () => {
	const field = {'ID': 'fld_9899154', 'name': 'Priceaa', 'type': 'hidden'};
	const fields = {
		'fld_9899154': field,
		'fld_4917648': {'ID': 'fld_4917648', 'name': 'Email', 'type': 'email'},
		'fld_1081036': {'ID': 'fld_1081036', 'name': 'Total', 'type': 'calculation'}
	};
	const formId = 'CF5b197831b60ae';
	const settings = {
		'ID': formId,
		'name': 'Contact Form',
		'fields': fields,
		'emailIdentifyingFields': ['fld_4917648'],
		'piiFields': ['fld_9899154', 'fld_4917648'],
		'privacyExporterEnabled': true
	};

	describe('Email and PII Identify Field settings', () => {
		describe('Email identifying field', () => {
			it('Works', () => {
				const emailSettings = renderer.create(
					<IsEmailIdentifyingField
						field={field}
						privacySettings={settings}
						onCheck={() => {
						}}
					/>
				);
				expect(emailSettings.toJSON()).toMatchSnapshot();
			});
		});
		describe('Pii field', () => {
			it('Works', () => {
				const piiField = renderer.create(
					<IsPiiField
						field={field}
						privacySettings={settings}
						onCheck={() => {}}
					/>
				);
				expect(piiField.toJSON()).toMatchSnapshot();

			});
		});

	});

	describe('Field settings ', () => {
		it('Works', () => {
			const fieldPrivacySettings = renderer.create(
				<FieldPrivacySettings
					formId={formId}
					field={field}
					privacySettings={settings}
					onCheck={() => {}}
					onCheckIsEmail={() => {}}
					onCheckIsPii={() => {}}
				/>
			);
			expect(fieldPrivacySettings.toJSON()).toMatchSnapshot();

		});
	});
	describe('Fields settings ', () => {
		it('Works', () => {
			const fieldsPrivacySettings = renderer.create(
				<FieldsPrivacySettings
					formId={formId}
					fields={fields}
					privacySettings={settings}
					onCheckIsEmail={() => {}}
					onCheckIsPii={() => {}}
				/>
			);
			expect(fieldsPrivacySettings.toJSON()).toMatchSnapshot();

		});
	});
	describe( 'Form privacy settings', () => {
		it( 'Works', () => {
			const formPrivacySettings = renderer.create(
				<FormPrivacySettings
					form={{
						ID: formId,
						fields: fields
					}}
					onSave={() => {}}
					privacySettings={settings}
					onStateChange={() => {}}
				/>
			)
		});
	});
});
