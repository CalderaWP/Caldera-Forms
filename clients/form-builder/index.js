import {
    ConditionalEditor,
    SubscribesToFormFields,
    translationStrings,
    conditionalsFromCfConfig,
    useProcessorConditonals,
    prepareProcessorConditonalFromSaved,
    ProcessorConditionalEditor

} from '@calderajs/form-builder';
import React from 'react';
import {render} from '@wordpress/element';
import domReady from '@wordpress/dom-ready';
import  {RenderComponentViaPortal} from "../render/components/RenderComponentViaPortal";

/**
 * UI for a single processor
 *
 * Currently only the conditionals
 */
const Processor = (props) => {
    const {processor} = props;
    const {
        conditional,
        onAddLine,
        onRemoveLine,
        onUpdateLine,
        onChangeType,
        onAddGroup,
    } = useProcessorConditonals({
        conditional: prepareProcessorConditonalFromSaved(processor.conditions),
    });
    return (
            <div style={{ marginTop: '20px' }}>
                <ProcessorConditionalEditor
                    strings={translationStrings}
                    formFields={props.formFields}
                    processorId={processor.id}
                    {...{
                        onAddGroup,
                        conditional,
                        onAddLine,
                        onRemoveLine,
                        onUpdateLine,
                        onChangeType,
                    }}
                />
            </div>

    );
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

    //The List of processors
    const [processors] = React.useState(() => {
       let collection = {};
       //no saved processors? Return early
       if( ! form.hasOwnProperty('processors')){
           return collection;
       }
       //set processors from saved form.
       Object.keys(form.processors).forEach(processorId =>{
           let processor = form.processors[processorId];
           if( ! processor.hasOwnProperty('conditions') ){
               processor.conditions = {};
           }
           collection[processorId] = processor;
       });
       return collection;
    });

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
                           <Processor processor={processors[activeProcessorId]}  strings={translationStrings} formFields={formFields} />
                        </RenderComponentViaPortal>
                    ) }
                    {/** Primary Conditional Logic Editor*/}
                    <RenderComponentViaPortal domNode={conditionalsNode}>
                        <ConditionalEditor formFields={formFields} strings={translationStrings} conditionals={initialConditionals}/>
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
