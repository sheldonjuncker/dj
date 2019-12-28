<?php

namespace App\Storm\Query;

use App\Storm\DataFormatter\UuidDataFormatter;
use App\Storm\Model\DJ\Dream;
use App\Storm\Model\DJ\DreamType;
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
	protected $id;

	/** @var  Dream $dream */
	protected $dream;

	public function name(string $name): self
	{
		$this->nameScope = $name;
		return $this;
	}

	public function id(int $id)
	{
		$this->id = $id;
		return $this;
	}

	public function excludeNormal(bool $excludeNormal = true)
	{
		$this->excludeNormal = $excludeNormal;
		return $this;
	}

	public function dream(Dream $dream)
	{
		$this->dream = $dream;
		return $this;
	}

	public function getModel(): Model
	{
		return new DreamType();
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

		if($this->id)
		{
			$dreamTypes->where('id = ?', $this->id);
		}

		if($this->dream)
		{
			$dreamTypes->joinWhere(':type', '1');
			$uuidFormatter = new UuidDataFormatter();
			$dreamTypes->where(':type.dream_id = ?', $uuidFormatter->formatToDataSource($this->dream->getId()));
		}

		return $dreamTypes;
	}

	/**
	 * @return Dream[]
	 */
	public function find(): \Iterator
	{
		yield from parent::find(); // TODO: Change the autogenerated stub
	}

	/**
	 * @return Dream|null
	 */
	public function findOne(): ?Model
	{
		return parent::findOne(); // TODO: Change the autogenerated stub
	}

	/**
	 * @return DreamType[]
	 */
	public function findAll(): array
	{
		return parent::findAll(); // TODO: Change the autogenerated stub
	}
}