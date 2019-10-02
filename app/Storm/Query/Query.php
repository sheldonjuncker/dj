<?php


namespace App\Storm\Query;

use App\Storm\Model\Model;

/**
 * Class Query
 *
 * The query class is used to search for models.
 *
 *
 * How you implement the SQL (or whether you even use SQL!)
 * and what methods you expose for searching is almost entirely up to you,
 * but a few methods are required to allow for some consistent usage.
 *
 * @package App\Storm
 */
abstract class Query
{
	/**
	 * Gets an iterator for the models to improve performance.
	 *
	 * @return Model[]
	 */
	abstract public function find(): \Iterator;

	/**
	 * Finds a single Model.
	 *
	 * @return Model
	 */
	abstract public function findOne(): ?Model;

	/**
	 * Returns a list of all models found.
	 *
	 * @return Model[]
	 */
	abstract public function findAll(): array;
}