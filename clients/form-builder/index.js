import {
    ConditionalEditor,
    SubscribesToFormFields,
    translationStrings,
    conditionalsFromCfConfig,
    Processors
} from '@calderajs/form-builder';
import React from 'react';
import {render} from '@wordpress/element';
import domReady from '@wordpress/dom-ready';
import  {RenderComponentViaPortal} from "../render/components/RenderComponentViaPortal";
import ErrorBoundary from 'react-error-boundary';

const errorHandler = (error,componentStack) => {
    console.log(error.message);
    console.table(componentStack);
};

/**
 * The Caldera Forms form builder app for building forms.
 *
 * Currently manages both conditional logic editors.
 */
const FormBuilder = ({conditionalsNode,initialConditionals,form}) => {
    //The id of the currently active processor
    const [activeProcessorId,setActiveProcessorId] = React.useState('');
    //Element processor conditional logic UI is rendered on.
    const processorConditionalsNode = React.useRef();

    //Listen outside of app for changes to active conditional
    React.useEffect( () => {
        window.jQuery(document).on( 'click', '.caldera-editor-processor-config-wrapper .set-conditions',function()  {
            setActiveProcessorId(jQuery(this).data('pid'));
        });
        window.jQuery(document).on( 'click', '.caldera-processor-nav',function()  {
            setActiveProcessorId(jQuery(this).data('pid'));
        });

    },[setActiveProcessorId]);

    //When active processor changes, capture the DOM node to render processor conditional logic UI on.
    React.useEffect(() => {
        if( activeProcessorId ){
            processorConditionalsNode.current = document.getElementById(`${activeProcessorId}_conditions_pane`);
        }
    }, [activeProcessorId]);

    //Important: As long as some of the builder is not in React, we will ONLY render via portal here.
    return(<SubscribesToFormFields
        jQuery={window.jQuery}
        intitalFields={form.fields}
        component={({formFields}) => {
            return (
                <React.Fragment>
                    {/** Processor Conditional Logic Editor*/}
                    { processorConditionalsNode.current && (
                        <RenderComponentViaPortal domNode={processorConditionalsNode.current}>
                            <ErrorBoundary onError={errorHandler}>
                           <Processors
                               processors={form.hasOwnProperty('processors') ? form.processors : {}}
                               strings={translationStrings}
                               formFields={formFields}
                               activeProcessorId={activeProcessorId}
                           />
                            </ErrorBoundary>
                        </RenderComponentViaPortal>
                    ) }
                    {/** Primary Conditional Logic Editor*/}
                    <RenderComponentViaPortal domNode={conditionalsNode}>
                        <ErrorBoundary onError={errorHandler}>
                            <ConditionalEditor formFields={formFields} strings={translationStrings} conditionals={initialConditionals}/>
                        </ErrorBoundary>
                    </RenderComponentViaPortal>
                </React.Fragment>

            )
        }}
    />);
};


domReady(function () {
    let form = CF_ADMIN.form;
    if (!form.hasOwnProperty('fields')) {
        form.fields = {};
    }

    let initialConditionals = [];
    const conditionalsNode = document.getElementById('caldera-forms-conditions-panel');
    if( form.hasOwnProperty('conditional_groups')&& form.conditional_groups.hasOwnProperty('conditions')){
        initialConditionals = conditionalsFromCfConfig(form.conditional_groups.conditions,form.fields);
    }

    render(
        <FormBuilder initialConditionals={initialConditionals}  form={form}  conditionalsNode={conditionalsNode}/> ,
        document.getElementById('caldera-forms-form-builder')
    );
});
