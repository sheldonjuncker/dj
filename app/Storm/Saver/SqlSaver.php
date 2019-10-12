<?php


namespace App\Storm\Saver;

use App\Storm\Model\Model;

abstract class SqlSaver extends Saver
{
	/**
	 * In order to save any Model, we need to know it's PK.
	 *
	 * @return string
	 */
	abstract public function getPrimaryKey(): string;

	/**
	 * Once we have the PK and table name, we can pretty easily
	 * perform an insert statement.
	 *
	 * @return string
	 */
	abstract public function getTableName(): string;

	public function save(Model $model)
	{
		$tableName = $this->getTableName();
		$primaryKeyFields = explode(",", $this->getPrimaryKey());
	}

	public function isNew(Model $model): bool
	{
		return $this->getModelInfo($model)->isNew();
	}

	protected function getModelInfo(Model $model): ModelInfo
	{
		return NULL;
	}

	public function insert(Model $model);

	public function update(Model $model);
}