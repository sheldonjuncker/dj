<?php

namespace App\Storm\Query;

use App\Storm\Model\DreamTypeModel;
use App\Storm\Model\Model;
use Nette\Database\Table\Selection;

class DreamTypeQuery extends SqlQuery
{
	const TYPE_NORMAL = 'Normal';
	const TYPE_LUCID = 'Lucid';
	const TYPE_NIGHTMARE = 'Nightmare';
	const TYPE_RECURRENT = 'Recurrent';

	protected $excludeNormal = false;
	protected $nameScope;

	public function name(string $name): self
	{
		$this->nameScope = $name;
		return $this;
	}

	public function excludeNormal(bool $excludeNormal = true)
	{
		$this->excludeNormal = $excludeNormal;
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

		if($this->excludeNormal)
		{
			$dreamTypes->where('name <> ?', self::TYPE_NORMAL);
		}

		return $dreamTypes;
	}
}