<?php

namespace App\DreamJournal;

use App\Storm\Model\DJ\Dream;
use App\Storm\Model\DJ\DreamToDreamType;
use App\Storm\Model\DJ\DreamType;
use App\Storm\Query\DreamToDreamTypeQuery;
use App\Storm\Query\DreamTypeQuery;
use App\Storm\Saver\SqlSaver;
use Nette\Database\Context;

class DreamTypeRelation extends RelatedData
{
	protected $dream;
	protected $database;

	public function __construct(Dream $dream, Context $database)
	{
		parent::__construct();
		$this->dream = $dream;
		$this->database = $database;
	}

	protected function getMappingModel(DreamType $type, bool $create = false): ?DreamToDreamType
	{
		$query = new DreamToDreamTypeQuery($this->database);
		$mappingModel = $query->dream($this->dream->getId())->type($type->getId())->findOne();
		if(!$mappingModel && $create)
		{
			$mappingModel = new DreamToDreamType();
			$mappingModel->setTypeId($type->getId());
			$mappingModel->setDreamId($this->dream->getId());
		}
		return $mappingModel;
	}

	public function save()
	{
		$sqlSaver = new SqlSaver($this->database);
		foreach($this->deleted as $deletedType)
		{
			$mapping = $this->getMappingModel($deletedType);
			if($mapping)
			{
				$sqlSaver->delete($mapping);
			}
		}
		$this->deleted->removeAll($this->deleted);

		foreach($this->data as $dreamType)
		{
			$mapping = $this->getMappingModel($dreamType, true);
			$sqlSaver->save($mapping);
		}
	}

	public function load(bool $refresh = false)
	{
		if(!$refresh && $this->loaded)
		{
			return;
		}

		$this->clear();
		$dreamTypeQuery = new DreamTypeQuery($this->database);
		foreach($dreamTypeQuery->dream($this->dream)->find() as $dreamType)
		{
			$this->add($dreamType);
		}
	}
}