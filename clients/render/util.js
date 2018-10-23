export const getFieldConfigBy = (fieldConfigs, findBy, findWhere ) =>{
	return fieldConfigs.find( field => findWhere ===field[findBy] );

}