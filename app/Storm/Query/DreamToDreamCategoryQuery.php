<?php

namespace App\Storm\Query;

use App\Storm\DataFormatter\UuidDataFormatter;
use App\Storm\Model\DreamToDreamCategoryModel;
use App\Storm\Model\Model;
use Nette\Database\Table\Selection;

class DreamToDreamCategoryQuery extends SqlQuery
{
	protected $dream_id;
	protected $category_id;

	public function dream(string $id)
	{
		$uuidFormatter = new UuidDataFormatter();
		$this->dream_id = $uuidFormatter->formatToDataSource($id);
	}

	public function category(int $id)
	{
		$this->category_id = $id;
	}

	protected function getModel(): Model
	{
		return new DreamToDreamCategoryModel();
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
}