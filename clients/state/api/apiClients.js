import * as calderaApiClient from '@caldera-labs/api-client';
import {cfAdmin} from "./cfAdmin";

/**
 * 1 instance of forms client
 *
 * @since 1.7.2
 *
 * @type {FormsClient}
 */
export const formsAdminApiClient = calderaApiClient.wpClientFactory(
	cfAdmin.rest.root,
	cfAdmin.rest.nonce,
	'forms'
);

/**
 * 1 instance of privacy settings client
 *
 * @since 1.7.2
 *
 * @type {PrivacySettingsClient}
 */
export const privacySettingsClient = calderaApiClient.wpClientFactory(
	cfAdmin.rest.root,
	cfAdmin.rest.nonce,
	'privacy'
);
