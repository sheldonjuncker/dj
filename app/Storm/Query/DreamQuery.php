<?php


namespace App\Storm\Query;


use App\Storm\Model\Model;
use App\Storm\Model\DreamModel;

class DreamQuery extends SqlQuery
{
	protected $scopeId;

	/**
	 * Primary key scope.
	 *
	 * @param string $id UUID
	 */
	public function id($id): self
	{
		$this->scopeId = $id;
		return $this;
	}

	public function buildQuery(): string
	{
		$sql = "
			SELECT
				*
			FROM
				dj.dreams dream
			WHERE
				{conditions}
			;
		";

		$conditions = ["1"];
		if($this->scopeId)
		{
			$conditions[] = "dream.id = " . $this->scopeId;
		}
		$conditionsSql = implode(" AND ", $conditions);

		$sql = str_replace("{conditions}", $conditionsSql, $sql);
		return $sql;
	}

	/**
	 * Finds a single dream.
	 *
	 * @return DreamModel
	 */
	public function findOne(): ?Model
	{
		//So efficient!
		$result = $this->query($this->buildQuery())[0] ?? NULL;
		$model = new DreamModel();
		$model->setProperties($result);
		return $model;
	}

	/**
	 * Finds all of the dreams.
	 *
	 * @return DreamModel[]
	 */
	public function findAll(): array
	{
		$results = $this->query($this->buildQuery());
		$models = [];
		foreach($results as $result)
		{
			$model = new DreamModel();
			$model->setProperties($result);
			$models[] = $model;
		}
		return $models;
	}
}