/**
 * Wrapper for an admin page's content
 *
 * @since 1.7.0
 *
 * @param props
 * @returns {*}
 * @constructor
 */
export const PageBody = (props) => {
    return(
        <div
            style={{marginTop:'75px'}}
            className={'caldera-forms-admin-page-wrap'}
        >
            {props.children}
        </div>
    )
}