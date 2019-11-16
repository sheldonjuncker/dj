<?php


namespace App\Storm\Saver;

use App\Storm\DataDefinition\DataFieldDefinition;
use App\Storm\DataFormatter\UuidDataFormatter;
use App\Storm\Model\Info\InfoStore;
use App\Storm\Model\Model;
use Nette\Database\Context;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Rhumsaa\Uuid\Uuid;
use Tracy\Debugger;

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
		$baseName = str_replace('Model', '', $shortClassName);

		//Add an underscore before all capitals but the first
		$baseCharacters = str_split($baseName);
		$tableName = '';
		for($i=0; $i<count($baseCharacters) ; $i++)
		{
			$c = $baseCharacters[$i];
			if(ctype_upper($c) && $i !== 0)
			{
				$tableName .= '_';
			}

			$tableName .= strtolower($c);
		}

		return $tableName;
	}

	public function save(Model $model)
	{
		if($this->isNew($model))
		{
			$result = $this->insert($model);
			if($result instanceof ActiveRow)
			{
				foreach($result as $dataField => $value)
				{
					$field = $model->getDataDefinition()->getField($dataField);
					if($field)
					{
						$field->setValue($value, DataFieldDefinition::FORMAT_TYPE_FROM_DATA_SOURCE);
					}
				}
			}
			else if(!$result)
			{
				throw new SaveFailedException('Failed to create DB record for model ' . $model->getBaseName() . '.');
			}
		}
		else
		{
			$result = $this->update($model);
			if(!$result && false)
			{
				throw new SaveFailedException('Failed to update DB record for model ' . $model->getBaseName() . '.');
			}
		}
	}

	public function delete(Model $model)
	{
		$tableName = $this->getTableName($model);
		$fields = $model->getDataDefinition();

		$delete = $this->connection->table($tableName);
		$primaryKeyFields = $this->getPrimaryKeyFields($delete);

		foreach($primaryKeyFields as $primaryKeyField)
		{
			$delete->where($primaryKeyField, $fields->getField($primaryKeyField)->getValue(DataFieldDefinition::FORMAT_TYPE_TO_DATA_SOURCE));
		}
		if(!$delete->delete())
		{
			throw new DeleteFailedException("Failed to delete " . $tableName . "(" . implode(", ", $primaryKeyFields) . ").");
		}
	}

	public function update(Model $model): int
	{
		$tableName = $this->getTableName($model);
		$update = $this->connection->table($tableName);

		$fields = $model->getDataDefinition();

		$updateFields = [];
		$primaryKeyFields = $this->getPrimaryKeyFields($update);
		foreach($fields->getFields() as $field)
		{
			$updateFields[$field->getName()] = $field->getValue(DataFieldDefinition::FORMAT_TYPE_TO_DATA_SOURCE);
		}

		$updateFields = array_diff_key($updateFields, array_flip($primaryKeyFields));

		foreach($primaryKeyFields as $primaryKeyField)
		{
			$update->where($primaryKeyField, $fields->getField($primaryKeyField)->getValue(DataFieldDefinition::FORMAT_TYPE_TO_DATA_SOURCE));
		}
		return $update->update($updateFields);
	}

	public function insert(Model $model)
	{
		$tableName = $this->getTableName($model);
		$fields = $model->getDataDefinition();


		$table = $this->connection->table($tableName);
		$primaryKeyFields = $this->getPrimaryKeyFields($table);


		//Might need to set a PK if it's a UUID and empty
		if(count($primaryKeyFields) == 1)
		{
			$primaryKeyField = $fields->getField($primaryKeyFields[0]);
			if($primaryKeyField->getFormatter() instanceof UuidDataFormatter && !$primaryKeyField->getValue(DataFieldDefinition::FORMAT_TYPE_NONE))
			{
				$primaryKeyField->setValue(Uuid::uuid1(), DataFieldDefinition::FORMAT_TYPE_NONE);
			}
		}

		$insertFields = [];
		foreach($fields->getFields() as $field)
		{
			$insertFields[$field->getName()] = $field->getValue(DataFieldDefinition::FORMAT_TYPE_TO_DATA_SOURCE);
		}

		return $this->connection->table($tableName)->insert($insertFields);
	}

	/**
	 * @param Model $model
	 * @return bool
	 */
	protected function isNew(Model $model): bool
	{
		return InfoStore::getInstance()->isNew($model);
	}

	public function getPrimaryKeyFields(Selection $table): array
	{
		$primaryKey = $table->getPrimary();
		if(is_string($primaryKey))
		{
			return [$primaryKey];
		}
		else
		{
			return $primaryKey;
		}
	}
}