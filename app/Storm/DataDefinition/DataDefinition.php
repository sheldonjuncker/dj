<?php

namespace App\Storm\DataDefinition;

use App\Storm\Model\Model;

/**
 * Class DataDefinition
 *
 * Defines the data accepted by a Model and what data to save to the Data Source.
 *
 * @package App\Storm\DataDefinition
 */
class DataDefinition
{
	/** @var Model $model Model used for reflection */
	protected $model;

	/** @var DataFieldDefinition[]  */
	protected $fields = [];

	/**
	 * DataDefinition constructor.
	 *
	 * @param DataFieldDefinition[] $dataFields
	 */
	public function __construct(Model $model, array $dataFields = [])
	{
		$this->model = $model;
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
		$dataField->setModel($this->model);
		$this->fields[$dataField->getName()] = $dataField;
	}

	/**
	 * Gets all of the fields.
	 *
	 * @return DataFieldDefinition[]
	 */
	public function getFields(): array
	{
		return $this->fields;
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