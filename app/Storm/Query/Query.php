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
	/** @var array Basic key/value field scopes. */
	protected $fields = [];

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

	/**
	 * Uses horrible dynamic PHP to add a scope on the fly.
	 *
	 * @param string $field The model field to query.
	 * @param mixed $value The value to query for.
	 *
	 * @note Each query class must apply conditions for the $fields array
	 * in order for this method to have an effect.
	 * This is used to provide dynamic functionality for model querying
	 * and is used for relationships.
	 *
	 * @return Query
	 */
	public function applyFieldScope(string $field, $value): Query
	{
		$this->fields[$field] = $value;
		return $this;
	}
}