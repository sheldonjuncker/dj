class DreamQueryField
{
	constructor(name, operators, values)
	{
		//Field name
		this.name = name;

		//Allowed search operators with default
		this.operators = operators || ['equals', 'not equals', 'contains', 'not contains'];

		//Allowed values for searching via dropdown (undefined for free-form text entry)
		this.values = values || undefined;
	}
}

export default DreamQueryField;