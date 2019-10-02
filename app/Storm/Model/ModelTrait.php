<?php


namespace App\Storm\Model;
use Nette\Database\ResultSet;
use Nette\Database\Row;

/**
 * Trait ModelTrait
 *
 * Since I don't want to use inheritance for building models,
 * the trait will contain any necessary functionality.
 *
 * @package App\Storm
 */
trait ModelTrait
{
	/**
	 * Sets the properties of the model from query results.
	 *
	 * @param ResultSet $queryResults
	 * @throws \Exception
	 */
	public function setProperties(Row $queryResults)
	{
		foreach($queryResults as $prop => $value)
		{
			if(property_exists($this, $prop))
			{
				$this->{$prop} = $value;
			}
			else
			{
				throw new \Exception("Model of type " . get_class($this) . " has no property " . $prop . ".");
			}
		}
	}
}