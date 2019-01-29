import {
	url,
	user,
	pass,
	login,
	activatePlugin,
	visitPluginPage
} from '../support/util';

/**
 * Before tests, login
 *
 * This is an anti-pattern
 * @todo Use wp-cli or basic authentication headers
 */
before(() => {
	login();
	activatePlugin('caldera-forms')
});

/**
 * Tests for main Caldera Forms page
 */
describe('Caldera Forms admin main page', () => {
	/**
	 * Before each test, go to main admin page
	 */
	beforeEach(() => {
		visitPluginPage('caldera-forms');
	});

	/**
	 * Create a contact form
	 */
	it('New form', () => {
		cy.get('.cf-new-form-button').click();
		cy.wait(200);
		cy.get('input[value="starter_contact_form"]').click({force: true});
		const name = 'My New Contact Form';
		cy.get('input.new-form-name').type(name);
		cy.get('.cf-create-form-button').click();
		cy.url().should('include', 'edit')
		cy.get('.caldera-element-type-label').contains(name);
	});
});

/**
 * Test the block
 */
describe('Block', () => {
	/**
	 * Before each test, go to new post page
	 */
	beforeEach(() => {
		cy.visit(url + '/wp-admin/post-new.php');
	});

	/**
	 * Can insert CF block
	 */
	it('Can insert CF Block', () => {
		cy.wait(200);
		cy.get('.edit-post-header-toolbar button.components-button.components-icon-button.editor-inserter__toggle').click({force: true});
		cy.get('button.editor-block-types-list__item.editor-block-list-item-calderaforms-cform').click({force: true});
		cy.get('.editor-block-list__layout').contains('Caldera Form');
	});
});