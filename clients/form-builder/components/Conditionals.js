import React from 'react';
import cfEditorState from "@calderajs/cf-editor-state";
import Conditional from './Conditional';

/**
 * One item in the conditionals list
 *
 * @since 1.8.10
 *
 * @param active
 * @param id
 * @param name
 * @returns {*}
 * @constructor
 */
const ConditionalListItem = ({active, id, name, onChooseItem}) => {
    return (
        <li className={`caldera-condition-nav ${active} caldera-forms-condition-group condition-point-${id}`}>
            <a className="condition-open-group" onClick={(e) => {
                e.preventDefault();
                onChooseItem(id);
            }}
               style={{cursor: "pointer"}}
            >
            <span id={`condition-group-${id}`}>
                {name}
            </span>
                <span className="condition-line-number"/>
            </a>
        </li>
    );
};

/**
 * The list of conditionals
 *
 * @since 1.8.10
 *
 * @param conditionals
 * @returns {*}
 * @constructor
 */
export const ConditionalsList = ({conditionals, onChooseItem}) => {
    return (
        <div className="caldera-editor-conditions-panel" style={{marginBottom: "32px"}}>
            <ul className="active-conditions-list">
                {conditionals.map(condition => <ConditionalListItem key={condition.id} {...condition}
                                                                    onChooseItem={onChooseItem}/>)}
            </ul>
        </div>
    )
};

/**
 * Set name for a group
 *
 * @since 1.8.10
 *
 * @param placeholder
 * @param onChange
 * @param id
 * @returns {*}
 * @constructor
 */
export const NewGroupName = ({placeholder, onChange, id, value}) => {
    return (
        <input
            type="text"
            name={`conditions[${id}][name]`}
            value={value}
            className="condition-new-group-name"
            placeholder={placeholder}
            style={{width: "100%"}}
            onChange={onChange}
        />
    )
};


/**
 * A button to add a new conditional with
 *
 * @param text
 * @param onClick
 * @returns {*}
 * @constructor
 */
export const NewConditionalButton = ({text, onClick}) => {
    return (
        <button
            style={{width: "250px"}}
            id="new-conditional"
            className="button"
            type="button"
            onClick={e => {
                e.preventDefault();
                onClick();
            }}
        >
            {text}
        </button>
    )
};


/**
 *
 * @param {cfEditorState }state
 * @param strings
 * @returns {*}
 */
export default function ({state, strings, fields}) {


    /**
     * Tracks the open conditional
     */
    const [openCondition, setOpenCondition] = React.useState('');

    /**
     * The conditional group that is open
     *
     * @type {function(): conditional}
     */
    const getOpenConditional = React.useCallback(() => {
            return state.getConditional(openCondition);
        }, [openCondition]
    );

    /**
     * Callback for adding conditional
     * @param name
     */
    const onNewConditional = (name) => {
        const id = `con_${Math.random().toString(36).replace(/[^a-z]+/g, '').substr(0, 5)}`
        state.addConditional({type: 'show', config: {name}, id});
        setOpenCondition(name);
    };

    /**
     * Callback for adding conditional
     * @param name
     */
    const onAddConditional = (conditionalId) => {
        state.removeConditional(conditionalId);
        setOpenCondition('');
    };

    /**
     * Callback for removing conditional
     * @param name
     */
    const onRemoveConditional = (conditionalId) => {
        state.removeConditional(conditionalId);
        setOpenCondition('');
    };



    return (
        <React.Fragment>
            <NewConditionalButton
                text={strings['new-conditional']}
                onClick={onNewConditional}
            />
            <ConditionalsList
                conditionals={state.getAllConditionals()}
                onChooseItem={setOpenCondition}
            />
            {openCondition &&
            <Conditional
                field={fields}
                group={getOpenConditional()}
                fields={fields}
                id={openCondition}
                strings={strings}
                onAddConditional={onAddConditional}
                onRemoveConditional={onRemoveConditional}
            />
            }
        </React.Fragment>
    )
}
