<?php


namespace App\Storm\Saver;

use App\Storm\Model\Model;
use Nette\Database\Context;

abstract class SqlSaver extends Saver
{
	/** @var Context $connection */
	protected $connection;

	/**
	 * Saver constructor.
	 * @param Context $connection
	 */
	public function __construct(Context $connection)
	{
		$this->connection = $connection;
	}

	/**
	 * In order to save any Model, we need to know it's PK.
	 *
	 * @return array
	 */
	abstract public function getPrimaryKey(): array;

	/**
	 * Once we have the PK and table name, we can pretty easily
	 * perform an insert statement.
	 *
	 * @return string
	 */
	abstract public function getTableName(): string;

	public function save(Model $model)
	{
		if($this->isNew($model))
		{
			$this->insert($model);
		}
		else
		{
			$this->update($model);
		}
	}

	public function update(Model $model): int
	{
		$tableName = $this->getTableName();
		$fields = $model->getDataDefinition();

		$updateFields = [];
		$primaryKeyFields = $this->getPrimaryKey();
		foreach($fields->getFields() as $field)
		{
			$updateFields[$field->getName()] = $field->getValue();
		}

		$updateFields = array_diff_key($updateFields, array_flip($primaryKeyFields));

		$update = $this->connection->table($tableName);
		foreach($primaryKeyFields as $primaryKeyField)
		{
			$update->where($primaryKeyField, $fields->getField($primaryKeyField)->getValue());
		}
		return $update->update($updateFields);
	}

	public function insert(Model $model)
	{
		$tableName = $this->getTableName();
		$fields = $model->getDataDefinition();

		$insertFields = [];
		foreach($fields->getFields() as $field)
		{
			$insertFields[$field->getName()] = $field->getValue();
		}

		return $this->connection->table($tableName)->insert($insertFields);
	}

	/**
	 * @param Model $model
	 * @return bool
	 */
	protected function isNew(Model $model): bool
	{
		$primaryKeyFields = $this->getPrimaryKey();
		$fields = $model->getDataDefinition();

		foreach($primaryKeyFields as $primaryKeyField)
		{
			$field = $fields->getField($primaryKeyField);
			if($field && $field->getValue(false))
			{
				return false;
			}
		}
		return true;
	}
}