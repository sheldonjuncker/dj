<?php

namespace App\Storm\Query;

use App\Storm\Model\DreamCategoryModel;
use App\Storm\Model\Model;
use Nette\Database\Table\Selection;

class DreamCategoryQuery extends SqlQuery
{
	protected $id;
	protected $name;

	public function id(int $id): DreamCategoryQuery
	{
		$this->id = $id;
		return $this;
	}

	public function name(string $name)
	{
		$this->name = $name;
		return $this;
	}

	protected function getModel(): Model
	{
		return new DreamCategoryModel();
	}

	protected function buildQuery(): Selection
	{
		$dreamCategories = $this->connection->table('dream_category');
		$dreamCategories->order('name ASC');

		if(isset($this->id))
		{
			$dreamCategories->where('id = ?', $this->id);
		}

		if(isset($this->name))
		{
			$dreamCategories->where('name = ?', $this->name);
		}

		return $dreamCategories;
	}

	/**
	 * @return DreamCategoryModel[]
	 */
	public function find(): \Iterator
	{
		yield from parent::find();
	}

	/**
	 * @return DreamCategoryModel|null
	 */
	public function findOne(): ?Model
	{
		return parent::findOne();
	}

	/**
	 * @return DreamCategoryModel[]
	 */
	public function findAll(): array
	{
		return parent::findAll();
	}
}