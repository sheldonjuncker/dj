<?php


namespace App\Storm\Query;

use App\Storm\DataDefinition\DataFieldDefinition;
use App\Storm\Model\Info\InfoStore;
use Nette\Database\Context;
use Nette\Database\ResultSet;
use App\Storm\Model\Model;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;

/**
 * Class SqlQuery
 *
 * Query class for MySQL databases.
 *
 * @package App\Storm\Query
 */
abstract class SqlQuery extends Query
{
	/** @var  Context $connection */
	protected $connection;

	public function __construct(Context $connection)
	{
		$this->connection = $connection;
	}

	abstract protected function buildQuery(): Selection;
	abstract protected function getModel(): Model;

	/**
	 * Finds iteratively for improved performance.
	 *
	 * @return Model[]
	 */
	public function find(): \Iterator
	{
		foreach($this->buildQuery() as $result)
		{
			$model = $this->getModel();
			$this->processResult($model, $result);
			yield $model;
		}
	}

	/**
	 * Finds a single model.
	 *
	 * @return Model|null
	 */
	public function findOne(): ?Model
	{
		$result = $this->buildQuery()->fetch();
		if($result)
		{
			$model = $this->getModel();
			$this->processResult($model, $result);
			return $model;
		}
		else
		{
			return NULL;
		}
	}

	/**
	 * Finds all of the models.
	 *
	 * @return Model[]
	 */
	public function findAll(): array
	{
		$models = [];
		foreach($this->buildQuery() as $result)
		{
			$model = $this->getModel();
			$this->processResult($model, $result);
			$models[] = $model;
		}
		return $models;
	}

	/**
	 * Sets the properties on the model from the result and sets up model info
	 * for future use.
	 *
	 * @param Model $model
	 * @param ActiveRow $result
	 */
	protected function processResult(Model $model, ActiveRow $result)
	{
		InfoStore::getInstance()->setNew($model, false);

		$dataDefinition = $model->getDataDefinition();
		foreach($result->toArray() as $key => $value)
		{
			$field = $dataDefinition->getField($key);
			if($field)
			{
				$field->setValue($value, DataFieldDefinition::FORMAT_TYPE_FROM_DATA_SOURCE);
			}
		}
	}

	/**
	 * Performs a query and returns an array of associative arrays.
	 * @todo Add error handling.
	 *
	 * @param string $sql
	 * @param array $params
	 * @return ResultSet
	 */
	protected function query(string $sql, array $params = []): ResultSet
	{
		return $this->connection->query($sql, $params);
	}
}