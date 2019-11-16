<?php


namespace App\Storm\Model\Info;
use App\Storm\Model\Model;

/**
 * Class Info
 *
 * Singleton which stores info about the models.
 * Data stored for each model:
 * 1. Whether Model was loaded from data source
 *
 * @package App\Storm\Model
 */
class InfoStore
{
	/** @var  InfoStore $instance Singleton instance */
	protected static $instance;

	/** @var array $infoStore */
	protected $infoStore = [];

	protected function getInfo(Model $model): ModelInfo
	{
		$key = spl_object_hash($model);
		if(!isset($this->infoStore[$key]))
		{
			$this->infoStore[$key] = new ModelInfo();
		}
		return $this->infoStore[$key];
	}

	/**
	 * Checks to see if a model is new.
	 *
	 * @param Model $model
	 * @return bool
	 */
	public function isNew(Model $model): bool
	{
		return $this->getInfo($model)->new;
	}

	/**
	 * Sets a model as new.
	 *
	 * @param Model $model
	 * @param bool $new
	 */
	public function setNew(Model $model, bool $new)
	{
		$this->getInfo($model)->new = $new;
	}

	public static function getInstance(): InfoStore
	{
		if(!self::$instance)
		{
			self::$instance = new self();
		}
		return self::$instance;
	}
}