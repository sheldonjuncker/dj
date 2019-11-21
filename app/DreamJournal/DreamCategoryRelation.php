<?php

namespace App\DreamJournal;

use App\Storm\Model\DreamCategoryModel;
use App\Storm\Model\DreamModel;
use App\Storm\Model\DreamToDreamCategoryModel;
use App\Storm\Query\DreamCategoryQuery;
use App\Storm\Query\DreamToDreamCategoryQuery;
use App\Storm\Saver\SqlSaver;
use Nette\Database\Context;

class DreamCategoryRelation extends RelatedData
{
	protected $dream;
	protected $database;

	public function __construct(DreamModel $dream, Context $database)
	{
		parent::__construct();
		$this->dream = $dream;
		$this->database = $database;
	}

	protected function getMappingModel(DreamCategoryModel $category, bool $create = false): ?DreamToDreamCategoryModel
	{
		$query = new DreamToDreamCategoryQuery($this->database);
		$mappingModel = $query->dream($this->dream->getId())->category($category->getId())->findOne();

		if(!$mappingModel && $create)
		{
			$mappingModel = new DreamToDreamCategoryModel();
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

	public function load()
	{
		$this->clear();
		$dreamCategoryQuery = new DreamCategoryQuery($this->database);
		foreach($dreamCategoryQuery->dream($this->dream)->find() as $dreamCategory)
		{
			$this->add($dreamCategory);
		}
	}
}