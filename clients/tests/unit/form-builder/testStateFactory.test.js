import stateFactory from "../../../form-builder/stateFactory";

describe('State factory', () => {
    const system_values = {
        "field": {
            "tags": {
                "text": [
                    "selections",
                    "total",
                    "disc1",
                    "disc2",
                    "summary",
                    "discount",
                    "grand_total"
                ],
                "checkbox": [
                    "selections"
                ],
                "calculation": [
                    "total",
                    "discount",
                    "grand_total"
                ],
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
        "system": {
            "type": "System Tags",
            "tags": {
                "text": [
                    "entry_id",
                    "entry_token",
                    "ip",
                    "user:id",
                    "user:user_login",
                    "user:first_name",
                    "user:last_name",
                    "user:user_email",
                    "get:*",
                    "post:*",
                    "request:*",
                    "post_meta:*",
                    "embed_post:ID",
                    "embed_post:post_title",
                    "embed_post:permalink",
                    "embed_post:post_date",
                    "date:Y-m-d H:i:s",
                    "date:Y/m/d",
                    "date:Y/d/m",
                    "login_url",
                    "logout_url",
                    "register_url",
                    "lostpassword_url",
                    "referer_url"
                ],
                "email": [
                    "user:user_email"
                ],
                "date_picker": [
                    "embed_post:post_date",
                    "date:Y-m-d H:i:s"
                ]
            },
            "wrap": [
                "{",
                "}"
            ]
        },
        "increment_capture": {
            "type": "Increment Value",
            "tags": {
                "text": [
                    "increment_capture:increment_value"
                ]
            },
            "wrap": [
                "{",
                "}"
            ]
        }
    };
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
        expect(fields.find(f => 'fld_6770247' === f.id).label).toBe("disc2");
        expect(fields.find(f => 'fld_6770247' === f.id).tag).toBe("%disc2%");
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
            id: 'fld1234',
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

});
