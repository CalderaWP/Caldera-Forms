import {
    printedData,
    cfAdmin
} from "../../../state/api/cfAdmin";

describe( 'util functions for data printed by wp_localize_script', () => {
    describe( 'CF_FORMS', () => {
        it( 'is an array', () => {
            expect( Array.isArray( printedData ) ).toBe(true);
        });
    });

    describe( 'CF_ADMIN', () => {
        const mockCfAdminData = {
            adminAjax : "https://wordpress.test/wp-admin/admin-ajax.php",
            api: {
                root: "https://wordpress.test/wp-json/cf-api/v2/",
                form: "https://wordpress.test/wp-json/cf-api/v2/forms/",
                entries: "https://wordpress.test/wp-json/cf-api/v2/entries/",
                entrySettings: "https://wordpress.test/wp-json/cf-api/v2/settings/entries/",
                nonce: "2a72cd462f"
            },
            dateFormat: "F j, Y g:i a",
            rest: {
                root: "https://wordpress.test/wp-json/cf-api/v2/",
                nonce: "2a72cd462f"
            }
        };

        it( 'is an object', () => {
            expect( typeof cfAdmin  ).toBe('object');
        });

        it( 'Has api info', ()=> {
            expect(  cfAdmin  ).toHaveProperty('api');
            expect(  typeof  cfAdmin  ).toBe('object');

            Object.keys(mockCfAdminData.api).map( (apiKey ) => {
                expect(  cfAdmin.api  ).toHaveProperty(apiKey);
                expect(  typeof cfAdmin.api[apiKey]  ).toBe('string');
            });

        });

        it( 'Has Admin AJAX info', ()=> {
            expect(  cfAdmin  ).toHaveProperty('adminAjax');
        });

        it( 'Has dateForms ', ()=> {
            expect(  cfAdmin  ).toHaveProperty('dateFormat');
            expect(  typeof cfAdmin.dateFormat  ).toBe('string');
        });

        it( 'Has rest info ', ()=> {
            expect(  cfAdmin  ).toHaveProperty('rest');
            expect(  typeof cfAdmin.rest  ).toBe('object');
            Object.keys(mockCfAdminData.rest).map( (apiKey ) => {
                expect(  cfAdmin.rest  ).toHaveProperty(apiKey);
                expect(  typeof cfAdmin.rest[apiKey]  ).toBe('string');
            });
        });
    });

})