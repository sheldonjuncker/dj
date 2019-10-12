<?php


namespace App\Storm\Query;


use App\Storm\DataFormatter\UuidDataFormatter;
use App\Storm\Model\Model;
use App\Storm\Model\DreamModel;
use Nette\Database\Table\Selection;

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
		$uuidFormatter = new UuidDataFormatter();
		$this->scopeId = $uuidFormatter->formatToDataSource($id);
		return $this;
	}

	protected function getModel(): Model
	{
		return new DreamModel();
	}

	protected function buildQuery(): Selection
	{
		$dreams = $this->connection->table('dreams');

		if($this->scopeId)
		{
			$dreams->where('id', $this->scopeId);
		}

		return $dreams;
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