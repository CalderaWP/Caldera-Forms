import stateFactory from "../../../form-builder/stateFactory";

describe('State factory', () => {
    const systemTags = {
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
        const factory = stateFactory(systemTags);
        const state = factory.createState();
        expect(state.getAllMagicTags().length).toBe(28);
    });

});