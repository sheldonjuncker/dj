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

	protected function getModel(): Model
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
}