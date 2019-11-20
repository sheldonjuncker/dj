<?php

namespace App\Storm\Model\Info;

/**
 * Class ModelInfo
 *
 * PHP has no way of association data to an object without storing the data
 * within the object as PHP reuses it's unique object IDs.
 *
 * This class contains data needed in order to efficiently work with models.
 *
 * @package App\Storm\Model\Info
 */
class ModelInfo
{
	protected $new = true;

	/**
	 * @return bool
	 */
	public function isNew(): bool
	{
		return $this->new;
	}

	/**
	 * @param bool $new
	 */
	public function setNew(bool $new)
	{
		$this->new = $new;
	}
}