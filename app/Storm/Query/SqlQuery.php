<?php


namespace App\Storm\Query;

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
			$this->setProperties($model, $result);
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
		$model = $this->getModel();
		$this->setProperties($model, $result);
		return $model;
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
			$this->setProperties($model, $result);
			$models[] = $model;
		}
		return $models;
	}

	protected function setProperties(Model $model, ActiveRow $result)
	{
		$dataDefinition = $model->getDataDefinition();
		foreach($result->toArray() as $key => $value)
		{
			$field = $dataDefinition->getField($key);
			if($field)
			{
				$field->setValue($value);
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