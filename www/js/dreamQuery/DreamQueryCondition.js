import DreamQueryField from './DreamQueryField';

class DreamQueryCondition
{
	/**
	 * Simple condition without children.
	 */
	constructor(type)
	{
		this.type = type || 'single';

		this.conditions = ['AND', 'OR'];

		//Set the field (any valid string for now)
		this.fields = [
			new DreamQueryField('title'),
			new DreamQueryField('body'),
			new DreamQueryField('title+body'),
			new DreamQueryField('type', undefined, ['Normal', 'Lucid', 'Nightmare', 'Recurrent']),
			new DreamQueryField('category'),
		];

		this.children = [];
	}

	getChildren(){
		return this.children;
	}

	addChild()
	{
		//Change type to list
		if(this.type === 'single')
		{
			this.type = 'list';
			this.children = [
				this,
				new DreamQueryCondition()
			];
		}
		else
		{
			this.children.push(condition);
		}
	}

	getType()
	{
		return this.type;
	}
}

export default DreamQueryCondition;