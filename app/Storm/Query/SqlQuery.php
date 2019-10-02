<?php


namespace App\Storm\Query;

use Nette\Database\Context;
use Nette\Database\ResultSet;

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