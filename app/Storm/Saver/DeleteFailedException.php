<?php

namespace App\Storm\Saver;

/**
 * Class DeleteFailedException
 *
 * This exception is thrown whenever a model fails to delete.
 * Later I will add some context to these errors for debugging.
 *
 * @package App\Storm\Saver
 */
class DeleteFailedException extends \Exception
{

}