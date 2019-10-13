<?php


namespace App\Storm\Saver;

use App\Storm\Model\Model;
use Nette\Database\Context;

/**
 * Class SqlSaver
 *
 * Used to persist any Models to a SQL database.
 * This is a generic saver class and will attempt to use the 'id" field
 * as the primary key and the model name as the table name.
 * If you need custom behavior, extend from this and override methods.
 *
 * @package App\Storm\Saver
 */
class SqlSaver extends Saver
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
	 * This defaults to ['id'] for normal models, and will need
	 * to be overridden for custom ones.
	 *
	 * @return array
	 */
	public function getPrimaryKey(): array
	{
		return ['id'];
	}

	/**
	 * Once we have the PK and table name, we can pretty easily
	 * perform an insert statement.
	 *
	 * By default gets the database table name from the model name.
	 *
	 * @param Model $model
	 * @return string
	 */
	public function getTableName(Model $model): string
	{
		$rc = new \ReflectionClass($model);
		$shortClassName = $rc->getShortName();
		$tableName = str_replace('Model', '', $shortClassName);
		$tableName = strtolower($tableName);
		return $tableName;
	}

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
		$tableName = $this->getTableName($model);
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
		$tableName = $this->getTableName($model);
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