<?php


namespace App\Storm\Model\Freud;


use App\Storm\DataDefinition\DataDefinition;
use App\Storm\DataDefinition\DataFieldDefinition;
use App\Storm\DataFormatter\IntegerDataFormatter;
use App\Storm\Model\BaseModel;

class Concept extends BaseModel
{
	/** @var  int $id */
	public $id;

	/** @var  string $name */
	public $name;

	public function getDataDefinition(): DataDefinition
	{
		return new DataDefinition($this, [
			new DataFieldDefinition('id', new IntegerDataFormatter()),
			new DataFieldDefinition('name')
		]);
	}
}