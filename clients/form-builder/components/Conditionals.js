import React from 'react';
import cfEditorState from "@calderajs/cf-editor-state";
import Conditional from './Conditional';
import {getFieldsNotAllowedForConditional, getFieldsUsedByConditional} from "../stateFactory";


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
const ConditionalListItem = ({active, condition, onChooseItem}) => {
    const {id} = condition;
    const name = React.useRef(condition.hasOwnProperty('config') && condition.config.hasOwnProperty('name') ? condition.config.name : '');
    return (
        <li className={`caldera-condition-nav ${active} caldera-forms-condition-group condition-point-${id}`}>
            <a
                className="condition-open-group"
                onClick={(e) => {
                    e.preventDefault();
                    onChooseItem(id);
                }}
                style={{cursor: "pointer"}}
            >
            <span id={`condition-group-${id}`}>
                {name.current}
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
 * @param onChooseItem
 * @param strings
 * @param onNewConditional
 * @returns {*}
 * @constructor
 */
export const ConditionalsList = ({conditionals, onChooseItem, strings, onNewConditional, getConditional, active}) => {
    //Tracks the new group's name
    const [newGroupName, setNewGroupName] = React.useState('');

    //Tracks the new group's ID
    const [newGroupId, setNewGroupId] = React.useState('');

    //Should we show new group name input?
    const [showNewGroupName, setShowNewGroupName] = React.useState(false);

    //Ref for new group name input so we can control focus.
    let newNameInputRef = React.createRef();

    /**
     * When new group name input is blurred:
     * * If has name, add group and hide new group name.
     * * If no name, hide new group name.
     *
     * @since 1.8.10
     *
     */
    const onNewBlur = () => {
        if (newGroupName.length) {
            onNewConditional(newGroupName, newGroupId);
            setShowNewGroupName(false);
        } else {
            setShowNewGroupName(false);
            setNewGroupName('');
            setNewGroupId('');
        }
    };

    /**
     * When we click on the new group button:
     * * Create ID for group
     * * Show new group name input
     * * Focus new group name input
     *
     * @since 1.8.10
     */
    const onClickNew = () => {
        const id = `con_${Math.random().toString(36).replace(/[^a-z]+/g, '').substr(0, 5)}`;
        setNewGroupId(id);
        setShowNewGroupName(true);
        setNewGroupName('');
        newNameInputRef.current.focus();
    };

    return (
        <div className="caldera-editor-conditions-panel" style={{marginBottom: "32px"}}>
            <ul className="active-conditions-list">
                {conditionals.map(condition => (
                        <ConditionalListItem
                            active={condition.id == active}
                            key={condition.id}
                            condition={condition}
                            onChooseItem={onChooseItem}
                        />
                    )
                )}
            </ul>
            {!showNewGroupName && <NewConditionalButton
                text={strings['new-conditional']}
                onClick={onClickNew}
            />}
            {showNewGroupName &&
            <input
                type="text"
                name={`conditions[${newGroupId}][name]`}
                value={newGroupName}
                className="condition-new-group-name"
                placeholder={strings['new-group-name']}
                style={{width: "100%"}}
                onChange={(e) => setNewGroupName(e.target.value)}
                ref={newNameInputRef}
                onBlur={onNewBlur}
            />
            }


        </div>
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
export default function ({state, strings, formFields, conditionals, updateConditional, addConditional, removeConditional}) {

    /**
     * Tracks the Id of the open conditional
     */
    const [openCondition, setOpenCondition] = React.useState('');
    const [conditional,setConditional] = React.useState(null );

    /**
     * When conditional is opened:
     *
     * * update conditional
     * * update openCondition
     * @param conditionalId
     */
    const onOpenConditional = (conditionalId) => {
        setConditional( conditionals.find( conditional => conditional.id === conditionalId ) );
        setOpenCondition(conditionalId);
    };

    /**
     * Get fields NOT allowed to be used by current function
     * @type {Function}
     */
    const getFieldsNotAllowedForOpenConditional = () => {
        if (!openCondition) {
            return [];
        }

        const fieldsNotAllowed = getFieldsNotAllowedForConditional(openCondition, state);
        return undefined === fieldsNotAllowed ? [] : fieldsNotAllowed;

    };

    /**
     * Get the fields that apply to the open conditional
     *
     * @since 1.8.10
     *
     * @type {Function}
     */
    const getFieldsAppliedForOpenConditional = React.useCallback(() => {
        if (!openCondition) {
            return [];
        }
        return getFieldsUsedByConditional(openCondition, state);
    }, [openCondition]);


    /**
     * Callback for adding conditional
     * @param name
     */
    const onNewConditional = (name, id) => {
        addConditional({type: 'show', config: {name}, id});
        onOpenConditional(id);
    };

    /**
     * Callback for removing conditional
     * @param name
     */
    const onRemoveConditional = (conditionalId) => {
        removeConditional(conditionalId);
        setOpenCondition('');
    };


    /**
     * Callback for updating a conditonal.
     *
     * @param update
     */
    const onUpdateConditional = (update) => {
        updateConditional(update);
        setConditional(update);
    };

    return (
        <React.Fragment>
            <ConditionalsList
                conditionals={conditionals}
                onChooseItem={onOpenConditional}
                strings={strings}
                onNewConditional={onNewConditional}
                active={openCondition}
            />

            {openCondition &&
                <Conditional
                    conditional={conditional}
                    formFields={formFields}
                    id={openCondition}
                    strings={strings}
                    fieldsUsed={getFieldsAppliedForOpenConditional()}
                    fieldsNotAllowed={getFieldsNotAllowedForOpenConditional()}
                    onRemoveConditional={onRemoveConditional}
                    onUpdateConditional={onUpdateConditional}
                />
            }
        </React.Fragment>
    )
}
