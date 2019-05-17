import {createElement} from "@wordpress/element";

export const HelpBlock = ({className, children}) =>
    (
        <div
            className={className ? className : 'screen-reader-text'}
        >
            {children}
        </div>
    );
