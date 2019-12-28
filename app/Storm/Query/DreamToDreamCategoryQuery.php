<?php

namespace App\Storm\Query;

use App\Storm\DataFormatter\UuidDataFormatter;
use App\Storm\Model\DJ\DreamToDreamCategory;
use App\Storm\Model\Model;
use Nette\Database\Table\Selection;

class DreamToDreamCategoryQuery extends SqlQuery
{
	protected $dream_id;
	protected $category_id;

	public function dream(string $id): self
	{
		$uuidFormatter = new UuidDataFormatter();
		$this->dream_id = $uuidFormatter->formatToDataSource($id);
		return $this;
	}

	public function category(int $id): self
	{
		$this->category_id = $id;
		return $this;
	}

	public function getModel(): Model
	{
		return new DreamToDreamCategory();
	}

	protected function buildQuery(): Selection
	{
		$table = $this->connection->table('dream_to_dream_category');

		if($this->category_id)
		{
			$table->where('category_id = ?', $this->category_id);
		}

		if($this->dream_id)
		{
			$table->where('dream_id = ?', $this->dream_id);
		}

		return $table;
	}

	/**
	 * @return DreamToDreamCategory[]
	 */
	public function find(): \Iterator
	{
		yield from parent::find();
	}

	/**
	 * @return DreamToDreamCategory|null
	 */
	public function findOne(): ?Model
	{
		return parent::findOne();
	}

	/**
	 * @return DreamToDreamCategory[]
	 */
	public function findAll(): array
	{
		return parent::findAll();
	}
}