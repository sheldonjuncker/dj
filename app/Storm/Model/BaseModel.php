<?php


namespace App\Storm\Model;


use App\Storm\DataDefinition\DataFieldDefinition;

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
}