<?php


namespace App\Storm\Saver;

/**
 * Class SaveFailedException
 *
 * This exception is thrown whenever a model fails to save.
 * Later I will add some context to these errors for debugging.
 *
 * @package App\Storm\Saver
 */
class SaveFailedException extends \Exception
{

}