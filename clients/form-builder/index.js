import {
    SubscribesToFormFields,
} from '@calderajs/form-builder';

import {render} from '@wordpress/element';
import domReady from '@wordpress/dom-ready';

domReady(function () {
    let isLoaded = false;
    document.getElementById('tab_conditions').addEventListener("click", () => {
        if (!isLoaded) {
            isLoaded = true;
            render(
                (
                    <SubscribesToFormFields
                        jQuery={window.jQuery}
                        component={(formFields) => {
                            console.log(formFields);
                            return <div>Editor</div>
                        }}
                    />

                ),
                document.getElementById('caldera-forms-conditions-panel')
            );
        }
    });


});
