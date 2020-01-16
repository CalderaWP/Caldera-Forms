export default {
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