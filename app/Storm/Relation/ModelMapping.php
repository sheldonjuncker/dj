<?php

namespace App\Storm\Relation;

use App\Storm\DataDefinition\DataFieldDefinition;
use App\Storm\Model\Model;
use App\Storm\Query\Query;

class ModelMapping
{
	/** @var  Model $from */
	protected $from;

	/** @var  array $mapping String to/from field mappings. */
	protected $mapping;

	/** @var  Model $to */
	protected $to;

	public function __construct(Model $from, array $mapping, Model $to)
	{
		$this->from = $from;
		$this->mapping = $mapping;
		$this->to = $to;
	}

	/**
	 * Gets the mapped models.
	 *
	 * @param Query $query The data source of the models.
	 * @return Model[]
	 */
	public function get(Query $query): array
	{
		//Apply scopes based on mappings
		foreach($this->mapping as $from => $to)
		{
			$from = str_replace('*', '', $from);
			$to = str_replace('*', '', $to);

			$fromValue = $this->from->getDataDefinition()->getField($from)->getValue(DataFieldDefinition::FORMAT_TYPE_TO_DATA_SOURCE);
			$query->applyFieldScope($to, $fromValue);
		}

		return $query->findAll();
	}

	/**
	 * Adds a model to the mapping.
	 *
	 * @param Model $model
	 */
	public function add(Model $model)
	{
		if(!$model instanceof $this->to)
		{
			throw new \Exception('Cannot map model types.');
		}

		foreach($this->mapping as $from => $to)
		{
			if(strpos($from, '*'))
			{
				$from = str_replace('*', '', $from);
				$fromField = $this->from->getDataDefinition()->getField($from);
				$toField = $this->to->getDataDefinition()->getField($to);
				$fromField->setValue($toField->getValue());
			}
			else if(strpos($to, '*'))
			{
				$to = str_replace('*', '', $to);
				$fromField = $this->from->getDataDefinition()->getField($from);
				$toField = $this->to->getDataDefinition()->getField($to);
				$toField->setValue($fromField->getValue());
			}
			else
			{
				throw new \Exception('Invalid mapping: no *.');
			}
		}
	}
}