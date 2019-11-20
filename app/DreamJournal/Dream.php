<?php

namespace App\DreamJournal;

use App\Storm\Model\DreamCategoryModel;
use App\Storm\Model\DreamModel;
use App\Storm\Model\DreamToDreamCategoryModel;
use App\Storm\Model\DreamToDreamTypeModel;
use App\Storm\Model\DreamTypeModel;
use App\Storm\Query\DreamCategoryQuery;
use App\Storm\Query\DreamToDreamCategoryQuery;
use App\Storm\Query\DreamToDreamTypeQuery;
use App\Storm\Query\DreamTypeQuery;
use App\Storm\Saver\SqlSaver;
use Nette\Database\Context;
use Nette\Utils\DateTime;

class Dream
{
	/** @var  Context $database */
	protected $database;

	/** @var  DreamModel $dreamModel */
	protected $dreamModel;

	/** @var  DreamTypeModel[] The dream's types. */
	protected $dreamTypes;

	/** @var DreamCategoryModel[] The dream's categories. */
	protected $dreamCategories;

	public function __construct(DreamModel $dreamModel, Context $database)
	{
		$this->dreamModel = $dreamModel;
		$this->database = $database;
	}

	/**
	 * Checks to see if the dream is of a specific type.
	 *
	 * @param DreamTypeModel $type
	 * @return bool
	 */
	public function hasType(DreamTypeModel $type): bool
	{
		foreach($this->getTypes() as $myType)
		{
			if($myType->getId() == $type->getId())
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * Gets the dream's types.
	 *
	 * @return DreamTypeModel[]
	 */
	public function getTypes(): array
	{
		//Only load once
		if($this->dreamTypes === NULL)
		{
			$this->dreamTypes = [];
			$dreamTypeQuery = new DreamTypeQuery($this->database);
			foreach($dreamTypeQuery->dream($this->dreamModel)->find() as $dreamType)
			{
				$this->dreamTypes[] = $dreamType;
			}
		}

		return $this->dreamTypes;
	}

	/**
	 * Gets all available types for dreams.
	 *
	 * @return DreamTypeModel[]
	 */
	public function getAvailableTypes(): array
	{
		$query = new DreamTypeQuery($this->database);
		return $query->excludeNormal()->findAll();
	}

	/**
	 * Gets the dreams categories.
	 *
	 * @return DreamCategoryModel[]
	 */
	public function getCategories(): array
	{
		//Only load once
		if($this->dreamCategories === NULL)
		{
			$this->dreamCategories = [];
			$dreamCategoryQuery = new DreamCategoryQuery($this->database);
			foreach($dreamCategoryQuery->dream($this->dreamModel)->find() as $dreamCategory)
			{
				$this->dreamCategories[] = $dreamCategory;
			}
		}

		return $this->dreamCategories;
	}

	/**
	 * Saves the model from POST data.
	 *
	 * @param array $dreamPost
	 */
	public function save(array $dreamPost)
	{
		$dreamTypePost = $dreamPost['types'] ?? [];
		$dream = $this->dreamModel;

		$dream->setTitle($dreamPost['title']);
		$dreamtAt = $dreamPost['dreamt_at'] ?? 'now';
		$dreamtAt = new DateTime($dreamtAt);
		$dream->setDreamtAt($dreamtAt);
		$dream->setDescription($dreamPost['description']);
		$dream->setUserId(1);

		$dreamSaver = new SqlSaver($this->database);
		$dreamSaver->save($dream);

		//Remove all dream type associations so that we can readd
		$dreamToDreamTypeQuery = new DreamToDreamTypeQuery($this->database);
		$dreamToDreamTypeQuery->dream($dream->getId());
		foreach($dreamToDreamTypeQuery->find() as $dreamToDreamType)
		{
			$dreamSaver->delete($dreamToDreamType);
		}
		$this->dreamTypes = NULL;

		//Add new dream type associations
		if($dreamTypePost)
		{
			foreach($dreamTypePost as $dreamType => $checked)
			{
				$dreamToTypeModel = new DreamToDreamTypeModel();
				$dreamToTypeModel->setDreamId($dream->getId());
				$dreamToTypeModel->setTypeId($dreamType);
				$dreamSaver->insert($dreamToTypeModel);
			}
		}

		//Remove all dream category associations
		$dreamToDreamCategoryQuery = new DreamToDreamCategoryQuery($this->database);
		foreach($dreamToDreamCategoryQuery->dream($dream->getId())->find() as $dreamToDreamCategory)
		{
			$dreamSaver->delete($dreamToDreamCategory);
		}
		$this->dreamCategories = NULL;

		//Add new category associations
		$categories = explode(',', $dreamPost['categories'] ?? '');

		foreach($categories as $category)
		{
			$dreamCategoryQuery = new DreamCategoryQuery($this->database);
			$dreamCategory = $dreamCategoryQuery->name($category)->findOne();
			if($dreamCategory)
			{
				$dreamToDreamCategory = new DreamToDreamCategoryModel();
				$dreamToDreamCategory->setDreamId($dream->getId());
				$dreamToDreamCategory->setCategoryId($dreamCategory->getId());
				$dreamSaver->insert($dreamToDreamCategory);
			}
		}
	}
}