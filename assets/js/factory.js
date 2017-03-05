/**
 * A generic factory
 *
 * @constructor
 */
var CFObj = function (){};

/**
 * Keys of field config
 *
 * @since 1.5.1
 *
 * @type {string[]}
 */
CFObj.prototype.fieldKeys = [
    'ID',
    'type',
    'label',
    'slug',
    'config',
    'caption',
    'custom_class',
    'default',
    'conditions'
];

/**
 * Check if object has a key
 *
 * @since 1.5.1
 *
 * @param object
 * @param key
 * @returns {boolean}
 */
CFObj.prototype.has = function (object, key) {
        return object ? hasOwnProperty.call(object, key) : false;
};
/**
 * Check if is empty object
 *
 * @since 1.5.1
 *
 * @param obj
 * @returns {boolean}
 */
CFObj.prototype.emptyObject = function (obj) {
    return Object.keys(obj).length === 0 && obj.constructor === Object;
};

/* Create a new field config object
 *
 * @since 1.5.1
 *
 * @param fieldId
 * @param type
 * @returns {{ID: *, type: *, config: {}}}
 */
CFObj.prototype.fieldFactory =-function (fieldId, type) {
        var field = {
        ID: fieldId,
        type: type,
        config: {}
    };

    fieldKeys.forEach(function (index) {
        if (!has(field, index)) {
            field[index] = '';
        }
    });

    return field;
};

