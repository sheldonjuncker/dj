<?php


namespace App\Storm\Query;


use App\Storm\DataFormatter\UuidDataFormatter;
use App\Storm\Model\DreamToDreamTypeModel;
use App\Storm\Model\Model;
use Nette\Database\Table\Selection;

class DreamToDreamTypeQuery extends SqlQuery
{
	protected $dream_id;
	protected $type_id;

	public function dream(string $id): self
	{
		$uuidFormatter = new UuidDataFormatter();
		$this->dream_id = $uuidFormatter->formatToDataSource($id);
		return $this;
	}

	public function type(int $type)
	{
		$this->type_id = $type;
		return $this;
	}

	 public function getModel(): Model
	{
		return new DreamToDreamTypeModel();
	}

	protected function buildQuery(): Selection
	{
		$dreamTypes = $this->connection->table('dream_to_dream_type');

		if($this->dream_id)
		{
			$dreamTypes->where('dream_id = ?', $this->dream_id);
		}

		if($this->type_id)
		{
			$dreamTypes->where('type_id = ?', $this->type_id);
		}

		return $dreamTypes;
	}

	/**
	 * @return DreamToDreamTypeModel[]
	 */
	public function find(): \Iterator
	{
		yield from parent::find();
	}

	/**
	 * @return DreamToDreamTypeModel[]
	 */
	public function findAll(): array
	{
		return parent::findAll();
	}

	/**
	 * @return DreamToDreamTypeModel|null
	 */
	public function findOne(): ?Model
	{
		return parent::findOne();
	}
}