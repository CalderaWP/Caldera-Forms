export const getFieldConfigBy = (fieldConfigs, findBy, findWhere ) =>{
	return fieldConfigs.find( field => findWhere ===field[findBy] );

};


const CryptoJS = require("crypto-js");

export const hashFiles = (files) => {
	return files.map( file => CryptoJS.MD5(contents).toString() );
};
