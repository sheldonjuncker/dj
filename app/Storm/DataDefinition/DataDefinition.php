<?php

namespace App\Storm\DataDefinition;

/**
 * Class DataDefinition
 *
 * Defines the data accepted by a Model and what data to save to the Data Source.
 *
 * @package App\Storm\DataDefinition
 */
class DataDefinition
{
	/** @var DataFieldDefinition[]  */
	protected $fields = [];

	/**
	 * DataDefinition constructor.
	 *
	 * @param DataFieldDefinition[] $dataFields
	 */
	public function __construct(array $dataFields = [])
	{
		foreach($dataFields as $dataField)
		{
			$this->addDataField($dataField);
		}
	}

	/**
	 * @param DataFieldDefinition $dataField
	 */
	public function addDataField(DataFieldDefinition $dataField)
	{
		$this->fields[$dataField->getName()] = $dataField;
	}

	/**
	 * Gets a field from the list of fields.
	 *
	 * @return DataFieldDefinition|null
	 */
	public function getField(string $name): ?DataFieldDefinition
	{
		return $this->fields[$name] ?? NULL;
	}
}