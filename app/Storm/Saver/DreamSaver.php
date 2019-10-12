<?php


namespace App\Storm\Saver;


use App\Storm\Model\DreamModel;
use App\Storm\Model\Model;
use Rhumsaa\Uuid\Uuid;

class DreamSaver extends SqlSaver
{
	public function getPrimaryKey(): array
	{
		return ['id'];
	}

	public function getTableName(): string
	{
		return 'dreams';
	}

	public function insert(Model $model)
	{
		//Generate a unique ID
		$model->setId(Uuid::uuid1()->toString());
		$model->setUserId(1);
		return parent::insert($model);
	}
}