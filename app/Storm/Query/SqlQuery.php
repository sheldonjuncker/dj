<?php


namespace App\Storm\Query;

use Nette\Database\Context;
use Nette\Database\ResultSet;
use App\Storm\Model\Model;
use Nette\Database\Row;

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

	abstract protected function buildQuery(): string;
	abstract protected function getModel(): Model;

	/**
	 * Finds iteratively for improved performance.
	 *
	 * @return Model[]
	 */
	public function find(): \Iterator
	{
		$results = $this->query($this->buildQuery());
		foreach($results as $result)
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
		$result = $this->query($this->buildQuery())->fetch();
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
		$results = $this->query($this->buildQuery());
		$models = [];
		foreach($results as $result)
		{
			$model = $this->getModel();
			$this->setProperties($model, $result);
			$models[] = $model;
		}
		return $models;
	}

	protected function setProperties(Model $model, Row $result)
	{
		$dataDefinition = $model->getDataDefinition();
		foreach($result as $key => $value)
		{
			$field = $dataDefinition->getField($key);
			if($field && $field->isReadable())
			{
				$reflectionProperty = new \ReflectionProperty($model, $key);
				$reflectionProperty->setAccessible(true);
				$formattedValue = $field->getFormatter()->formatFromDataSource($value);
				$reflectionProperty->setValue($model, $formattedValue);
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