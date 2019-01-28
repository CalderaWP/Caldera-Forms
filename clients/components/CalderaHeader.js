/**
 * Creates the Caldera admin page header
 *
 * @since 1.7.0
 *
 * @param props
 * @returns {*}
 * @constructor
 */
export const CalderaHeader = (props) => {
    return(
        <div className="caldera-editor-header">
            <ul className="caldera-editor-header-nav">
                <li className="caldera-editor-logo">
                    <span className="caldera-forms-name">
                        Caldera Forms: {props.name ? props.name : 'Caldera Forms'}
                    </span>
                </li>
                {props.children}
            </ul>
        </div>
    );
};