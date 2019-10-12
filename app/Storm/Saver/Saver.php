<?php

namespace App\Storm\Saver;

use App\Storm\DataDefinition\DataDefinition;
use App\Storm\Model\Model;
use Nette\Database\Context;

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
	 * Saves the model throwing an exception on failure.
	 *
	 * @param Model $model
	 * @throws SaveFailedException
	 */
	abstract public function save(Model $model);
}