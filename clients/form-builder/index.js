import {
    Page,
} from '@calderajs/form-builder';
console.log(Page);
import {render} from '@wordpress/element';
import domReady from '@wordpress/dom-ready';
domReady(function () {
    render(
        <div>Caldera Forms</div>, document.querySelectorAll('.wrap')[0] );
});
