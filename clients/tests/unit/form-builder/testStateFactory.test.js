import stateFactory, {
    getAllFieldsUsed,
    getFieldsNotAllowedForConditional,
    getFieldsUsedByConditional,
} from "../../../form-builder/stateFactory";
import system_values from "./system_values";
describe('State factory', () => {

    const current_form_fields = {
        "fld_9272690": {
            "label": "selections",
            "slug": "selections",
            "type": "checkbox"
        },
        "fld_7896676": {
            "label": "total",
            "slug": "total",
            "type": "calculation"
        },
        "fld_1803763": {
            "label": "disc1",
            "slug": "disc1",
            "type": "hidden"
        },
        "fld_6770247": {
            "label": "disc2",
            "slug": "disc2",
            "type": "hidden"
        },
        "fld_3195385": {
            "label": "summary",
            "slug": "summary",
            "type": "summary"
        },
        "fld_1734684": {
            "label": "discount",
            "slug": "discount",
            "type": "calculation"
        },
        "fld_6532733": {
            "label": "grand total",
            "slug": "grand_total",
            "type": "calculation"
        }
    };
    it('Prepares system tags', () => {
        const factory = stateFactory({
            system: {
                tags: {
                    "text": [
                        "ip",
                        "user:first_name",
                    ],
                    "email": [
                        "user:user_email"
                    ],
                    "date_picker": [
                        "embed_post:post_date",
                    ]
                }
            }

        });
        expect(typeof factory.prepareSystemTags).toBe('function');
        const tags = factory.prepareSystemTags();
        expect(tags.length).toBe(4);
        expect(tags.find((t => 'ip' === t.tag))).toEqual(
            {tag: 'ip', type: 'text'}
        );
        expect(tags.find((t => 'user:first_name' === t.tag))).toEqual(
            {tag: 'user:first_name', type: 'text'},
        );
        expect(tags.find((t => 'ip' === t.tag))).toEqual(
            {tag: 'ip', type: 'text'}
        );
        expect(tags.find((t => 'user:user_email' === t.tag))).toEqual(
            {tag: 'user:user_email', type: 'email'},
        );
        expect(tags.find((t => 'embed_post:post_date' === t.tag))).toEqual(
            {tag: 'embed_post:post_date', type: 'date_picker'},
        );

    });

    it('Creates state with system tags', () => {
        const factory = stateFactory({

            system: {
                tags: {
                    "text": [
                        "ip",
                        "user:first_name",
                    ],
                    "email": [
                        "user:user_email"
                    ],
                    "date_picker": [
                        "embed_post:post_date",
                    ]
                }
            }
        });
        const state = factory.createState();
        expect(state.getAllMagicTags().length).toBe(4);
    });

    it('Adds additional tag types', () => {
        const factory = stateFactory({
            ...system_values,
            "field": {
                "tags": {},
                "type": "Fields",
                "wrap": [
                    "%",
                    "%"
                ]
            },
        });
        const state = factory.createState();
        expect(state.getAllMagicTags().length).toBe(28);
    });

    it('Prepares fields', () => {
        const factory = stateFactory();
        const fields = factory.prepareFields(current_form_fields);
        expect(fields.length).toBe(Object.keys(current_form_fields).length);
        expect(fields.find(f => 'fld_6770247' === f.ID).label).toBe("disc2");
        expect(fields.find(f => 'fld_6770247' === f.ID).tag).toBe("%disc2%");
    });
    it('Adds field tag types', () => {
        const factory = stateFactory({
            //One system tag to make sure they get merged in
            "system": {
                "type": "System Tags",
                "tags": {
                    "email": [
                        "user:user_email"
                    ],
                },
                "wrap": [
                    "{",
                    "}"
                ]
            },
            "field": {
                //This is intentionally wrong (missing fields)
                //This part of the object is NOT used intentionally.
                "tags": {
                    "hidden": [
                        "disc1",
                        "disc2"
                    ],
                    "summary": [
                        "summary"
                    ]
                },
                "type": "Fields",
                "wrap": [
                    "%",
                    "%"
                ]
            },
        }, current_form_fields);
        const state = factory.createState();
        expect(state.getAllMagicTags().length).toBe(Object.keys(current_form_fields).length + 1);
        expect(state.getMagicTagsByType('hidden').length).toBe(2);
        expect(state.getMagicTagsByType('summary').length).toBe(1);
    });


    it('Adds fields', () => {
        const factory = stateFactory(system_values, current_form_fields);
        const state = factory.createState();
        expect(state.getMagicTagsByType('hidden').length).toBe(2);
        expect(state.addField({
            ID: 'fld1234',
            "label": "New Field",
            "slug": "new_field",
            "type": "hidden",

        })).toBe(true);

        expect(state.getMagicTagsByType('hidden').length).toBe(3);
    });

    it('Removes fields', () => {
        const factory = stateFactory(system_values, current_form_fields);
        const state = factory.createState();
        expect(state.getMagicTagsByType('hidden').length).toBe(2);
        expect(state.removeField('fld_1803763')).toBe(true);
        expect(state.getMagicTagsByType('hidden').length).toBe(1);
    });

    const conditional = {
        "id": "con_8",
        "type": "hide",
        config: {
            "name": "Hide 1",
            "type": "hide",
            "fields": {
                cl4: "fld_9272690"
            },
            group: {
                rw5: {
                    cl4: {
                        "parent": "rw5",
                        "field": "fld_9272690",
                        "compare": "is",
                        "value": "opt19"
                    }
                }
            }
        }

    };

    it('Adds a conditional group', () => {
        const factory = stateFactory(system_values, current_form_fields);
        const state = factory.createState();
        expect(state.getAllConditionals().length).toBe(0);
        state.addConditional({id: 'r1', type: "disable"});
        expect(state.getAllConditionals().length).toBe(1);
        state.addConditional({id: 'r2', type: "enable"});
        expect(state.getAllConditionals().length).toBe(2);
        expect( state.getConditional('r1').type ).toBe('disable')
    });

    it('Updates a conditional group', () => {
        const factory = stateFactory(system_values, current_form_fields);
        const state = factory.createState();
        state.addConditional({id: 'r1', type: "enable"});
        state.addConditional({id: 'r2', type: "enable"});
        state.addConditional({id: 'r3', type: "enable"});
        state.updateConditional({id: 'r2', type: "disable"});
        expect( state.getConditional('r2').type ).toBe('disable')
    });

    it('Removes a conditional group', () => {
        const factory = stateFactory(system_values, current_form_fields);
        const state = factory.createState();
        expect(state.getAllConditionals().length).toBe(0);
        state.addConditional(conditional);
        expect(state.getAllConditionals().length).toBe(1);
    });

    test( 'getAllFieldsUsed utility method', () => {
        const factory = stateFactory(system_values, current_form_fields);
        const state = factory.createState();
        state.addConditional(conditional);

        expect(getAllFieldsUsed(state).length).toBe(1);

        //Add two more conditions
        state.addConditional({
            id: 'r2', type: "enable",
            config: {name: 'r2', fields: {c2: 'fld2'}}
        });
        //This one uses one of the same fields as previous
        state.addConditional({
            id: 'r3', type: "enable",
            config: {name: 'r2', fields: {c5: 'fld_4', c6: 'fld2'}}
        });

        expect(getAllFieldsUsed(state).length).toBe(4);
    });

    test( 'getFieldsUsedByConditional utility method',( ) => {
        const factory = stateFactory(system_values, current_form_fields);
        const state = factory.createState();
        state.addConditional({
            id: 'none', type: "enable",
            config: {name: 'No fields'}
        });
        expect( getFieldsUsedByConditional('none', state).length ).toBe(0);

        state.addConditional({
            id: 'r3', type: "enable",
            config: {name: 'r threee', fields: {c5: 'fld_4', c6: 'fld2'}}
        });

        expect( getFieldsUsedByConditional('r3', state).length ).toBe(2);
    });

    test( 'getFieldsNotAllowedForConditional utility method',( ) => {
        const factory = stateFactory(system_values, current_form_fields);
        const state = factory.createState();
        state.addConditional({
            id: 'none', type: "enable",
            config: {name: 'No fields'}
        });
        expect( getFieldsUsedByConditional('none', state).length ).toBe(0);


        state.addConditional({
            id: 'r3', type: "enable",
            config: {name: 'r threee', fields: {c5: 'fld_4', c6: 'fld2'}}
        });
        //No other conditionals have fields used, so all fields are allowed.
        expect( getFieldsNotAllowedForConditional('r3', state) ).toEqual([]);

        //Other conditional group has 2 fields applied, so those are blocked
        expect( getFieldsNotAllowedForConditional('none', state) ).toEqual([
            'fld_4', 'fld2'
        ])
    });


});
