import './index.scss';
import React, {Suspense} from 'React';
import {render} from 'react-dom';
const FormsAdminApp = React.lazy(() => import('./MainDashboard/components/FormsAdminApp'));
function LazyApp(props){
    return (
        <Suspense fallback={<div>Loading...</div>}>
            <FormsAdminApp {...props} />
        </Suspense>
    )
}
window.addEventListener('load', (event) => {
    const isProConnected = 'object' === typeof CF_ADMIN && CF_ADMIN.isProConnected;
    const props = {
        isProConnected,
    };

    render( <LazyApp {...props} />,document.getElementById('caldera-forms-clippy'));
});

