<?php

namespace App\Storm\Query;

use App\Storm\Model\DreamTypeModel;
use App\Storm\Model\Model;
use Nette\Database\Table\Selection;

class DreamTypeQuery extends SqlQuery
{
	protected $nameScope;

	public function name(string $name): self
	{
		$this->nameScope = $name;
		return $this;
	}

	protected function getModel(): Model
	{
		return new DreamTypeModel();
	}

	protected function buildQuery(): Selection
	{
		$dreamTypes = $this->connection->table('dream_type');

		if($this->nameScope)
		{
			$dreamTypes->where('name = ?', $this->nameScope);
		}

		return $dreamTypes;
	}
}