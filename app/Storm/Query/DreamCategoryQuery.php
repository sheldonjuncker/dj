<?php

namespace App\Storm\Query;

use App\Storm\DataFormatter\UuidDataFormatter;
use App\Storm\Model\DJ\DreamCategory;
use App\Storm\Model\DJ\Dream;
use App\Storm\Model\Model;
use Nette\Database\Table\Selection;

class DreamCategoryQuery extends SqlQuery
{
	protected $id;
	protected $name;

	/** @var  Dream $dream */
	protected $dream;

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

	public function dream(Dream $dream)
	{
		$this->dream = $dream;
		return $this;
	}

	public function getModel(): Model
	{
		return new DreamCategory();
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

		if($this->dream)
		{
			$dreamCategories->joinWhere(':category', '1');
			$uuidFormatter = new UuidDataFormatter();
			$dreamCategories->where(':category.dream_id = ?', $uuidFormatter->formatToDataSource($this->dream->getId()));
		}

		return $dreamCategories;
	}

	/**
	 * @return DreamCategory[]
	 */
	public function find(): \Iterator
	{
		yield from parent::find();
	}

	/**
	 * @return DreamCategory|null
	 */
	public function findOne(): ?Model
	{
		return parent::findOne();
	}

	/**
	 * @return DreamCategory[]
	 */
	public function findAll(): array
	{
		return parent::findAll();
	}
}