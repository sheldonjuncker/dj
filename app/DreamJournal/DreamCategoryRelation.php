<?php

namespace App\DreamJournal;

use App\Storm\Model\DJ\DreamCategory;
use App\Storm\Model\DJ\Dream;
use App\Storm\Model\DJ\DreamToDreamCategory;
use App\Storm\Query\DreamCategoryQuery;
use App\Storm\Query\DreamToDreamCategoryQuery;
use App\Storm\Saver\SqlSaver;
use Nette\Database\Context;

class DreamCategoryRelation extends RelatedData
{
	protected $dream;
	protected $database;

	public function __construct(Dream $dream, Context $database)
	{
		parent::__construct();
		$this->dream = $dream;
		$this->database = $database;
	}

	protected function getMappingModel(DreamCategory $category, bool $create = false): ?DreamToDreamCategory
	{
		$query = new DreamToDreamCategoryQuery($this->database);
		$mappingModel = $query->dream($this->dream->getId())->category($category->getId())->findOne();

		if(!$mappingModel && $create)
		{
			$mappingModel = new DreamToDreamCategory();
			$mappingModel->setCategoryId($category->getId());
			$mappingModel->setDreamId($this->dream->getId());
		}
		return $mappingModel;
	}

	public function save()
	{
		$sqlSaver = new SqlSaver($this->database);

		//Delete removed relations
		foreach($this->deleted as $deletedCategory)
		{
			$mapping = $this->getMappingModel($deletedCategory);
			if($mapping)
			{
				$sqlSaver->delete($mapping);
			}
		}
		$this->deleted->removeAll($this->deleted);


		foreach($this->data as $dreamCategory)
		{
			$mapping = $this->getMappingModel($dreamCategory, true);
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
		$dreamCategoryQuery = new DreamCategoryQuery($this->database);
		foreach($dreamCategoryQuery->dream($this->dream)->find() as $dreamCategory)
		{
			$this->add($dreamCategory);
		}
	}
}