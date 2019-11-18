<?php

namespace App\Storm\Model;

use App\Storm\DataDefinition\DataDefinition;
use App\Storm\DataDefinition\DataFieldDefinition;
use App\Storm\DataFormatter\IntegerDataFormatter;
use App\Storm\DataFormatter\UuidDataFormatter;

class DreamToDreamCategoryModel extends BaseModel
{
	protected $dream_id;
	protected $category_id;

	public function getDataDefinition(): DataDefinition
	{
		return new DataDefinition($this, [
			new DataFieldDefinition('dream_id', new UuidDataFormatter()),
			new DataFieldDefinition('category_id', new IntegerDataFormatter())
		]);
	}
}