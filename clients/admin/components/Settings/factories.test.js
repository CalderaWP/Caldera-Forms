import {proLocalSettingsFactory} from "./factories";

describe( 'proLocalSettingsFactory', () => {
	it( 'provides defaults', () => {
		const proSettings = proLocalSettingsFactory({});
		const {connected,generalSettings} = proSettings;
		expect( connected ).toEqual(false);
		expect( typeof generalSettings ).toEqual('object');
		expect(  generalSettings.logLevel ).toEqual(200);
	});

	it( 'Overwrites defaults with stored', () => {
		const proSettings = proLocalSettingsFactory({
			connected: true,
		});
		const {connected,generalSettings} = proSettings;
		expect( connected ).toEqual(true);
		expect(  generalSettings.logLevel ).toEqual(200);
	});

	it( 'Overwrites nested with stored', () => {
		const proSettings = proLocalSettingsFactory({
			apiKeys: {
				public:'aa'
			},
		});
		const {generalSettings,apiKeys} = proSettings;
		expect( generalSettings.generatePDFs ).toEqual(false);
		expect(  apiKeys.public ).toEqual('aa');
		expect(  apiKeys.secret ).toEqual('');
	});
});

describe( 'javascript', () => {
	it( 'Mutates array with forEach', () => {
		const collection = [
			{
				offset: false,
			}
		];
		collection.forEach( item => {
			item.offset = true;
		} );
		expect(collection[0].offset).toEqual(true);
	});
});