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

	protected function getModel(): Model
	{
		return new DreamModel();
	}

	protected function buildQuery(): string
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


	# Only added for type hinting

	/**
	 * @return DreamModel[]
	 */
	public function find(): \Iterator
	{
		yield from parent::find();
	}

	/**
	 * @return DreamModel|null
	 */
	public function findOne(): ?Model
	{
		return parent::findOne();
	}

	/**
	 * @return DreamModel[]
	 */
	public function findAll(): array
	{
		return parent::findAll();
	}
}