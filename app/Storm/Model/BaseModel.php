<?php


namespace App\Storm\Model;


use App\Storm\DataDefinition\DataFieldDefinition;
use Nette\Schema\Elements\Base;

abstract class BaseModel implements Model
{
	/**
	 * Converts the Model to an array.
	 *
	 * @param int $format
	 * @return array
	 */
	public function toArray(int $format = DataFieldDefinition::FORMAT_TYPE_NONE): array
	{
		$array = [];
		foreach($this->getDataDefinition()->getFields() as $field)
		{
			$array[$field->getName()] = $field->getValue($format);
		}
		return $array;
	}

	/**
	 * Converts the Model to JSON.
	 *
	 * @return string
	 */
	public function toJson(int $format = DataFieldDefinition::FORMAT_TYPE_NONE): string
	{
		$array = $this->toArray($format);
		return json_encode($array, JSON_PRETTY_PRINT);
	}

	/**
	 * Creates a Model from an array.
	 * Needs to be refactored into a Query class which uses an array data source.
	 *
	 * @param array $data
	 * @return BaseModel
	 */
	public static function fromArray(array $data): BaseModel
	{
		$model = new static();
		$dataDefinition = $model->getDataDefinition();
		foreach($data as $key => $value)
		{
			if($field = $dataDefinition->getField($key))
			{
				$field->setValue($value);
			}
		}
		return $model;
	}
}