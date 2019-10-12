<?php

namespace App\Storm\Saver;

use App\Storm\DataDefinition\DataDefinition;
use App\Storm\Model\Model;

/**
 * Class Saver
 *
 * Provides persistence for models.
 *
 * @package App\Storm\Saver
 */
abstract class Saver
{
	/**
	 * Gets the data definition for what data the model accepts.
	 *
	 * @return DataDefinition
	 */
	abstract public function getDataDefinition(): DataDefinition;

	/**
	 * Saves the model throwing an exception on failure.
	 *
	 * @param Model $model
	 * @throws SaveFailedException
	 */
	abstract public function save(Model $model);
}