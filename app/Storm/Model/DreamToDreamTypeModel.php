<?php


namespace App\Storm\Model;


use App\Storm\DataDefinition\DataDefinition;
use App\Storm\DataDefinition\DataFieldDefinition;
use App\Storm\DataFormatter\IntegerDataFormatter;
use App\Storm\DataFormatter\UuidDataFormatter;

class DreamToDreamTypeModel extends BaseModel
{
	protected $dream_id;
	protected $type_id;

	public function getDataDefinition(): DataDefinition
	{
		return new DataDefinition($this, [
			new DataFieldDefinition('dream_id', new UuidDataFormatter()),
			new DataFieldDefinition('type_id', new IntegerDataFormatter())
		]);
	}

	public function setDreamId(string $id)
	{
		$this->dream_id = $id;
	}

	public function setTypeId(string $id)
	{
		$this->type_id = $id;
	}
}