<?php

namespace App\DreamJournal;

use App\Storm\DataDefinition\DataFieldDefinition;
use App\Storm\Model\DreamCategoryModel;
use App\Storm\Model\DreamModel;
use App\Storm\Model\DreamTypeModel;
use App\Storm\Query\DreamTypeQuery;
use App\Storm\Saver\SqlSaver;
use Nette\Database\Context;
use Tracy\Debugger;

class Dream
{
	/** @var  Context $database */
	protected $database;

	/** @var  DreamModel $dreamModel */
	protected $dreamModel;

	/** @var  DreamTypeRelation The dream's types. */
	protected $dreamTypes;

	/** @var DreamCategoryRelation The dream's categories. */
	protected $dreamCategories;

	public function __construct(DreamModel $dreamModel, Context $database)
	{
		$this->dreamModel = $dreamModel;
		$this->database = $database;

		$this->dreamCategories = new DreamCategoryRelation($this->dreamModel, $this->database);
		$this->dreamTypes = new DreamTypeRelation($this->dreamModel, $this->database);
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
			if($type->getId() == $myType->getId())
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
		return $this->dreamTypes->get();
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
		return $this->dreamCategories->get();
	}

	/**
	 * Saves the model from POST data.
	 *
	 * @param array $dreamPost
	 */
	public function save(array $dreamPost)
	{
		//Debugger::dump($dreamPost);
		$dreamTypePost = $dreamPost['types'] ?? [];
		$dream = $this->dreamModel;

		//Set data from POST and then override user since there's only one and it's me
		$dream->fromArray($dreamPost, DataFieldDefinition::FORMAT_TYPE_FROM_UI);
		$dream->setUserId(1);

		$dreamSaver = new SqlSaver($this->database);
		$dreamSaver->save($dream);

		//Remove all dream type associations so that we can readd
		$this->dreamTypes->load();
		$this->dreamTypes->removeAll();
		$this->dreamTypes->save();

		//Add new dream type associations
		if($dreamTypePost)
		{
			foreach($dreamTypePost as $dreamType => $checked)
			{
				$dreamTypeModel = new DreamTypeModel();
				$dreamTypeModel->setId($dreamType);
				$this->dreamTypes->add($dreamTypeModel);
			}
		}
		$this->dreamTypes->save();

		//Remove all dream category associations
		$this->dreamCategories->load();
		$this->dreamCategories->removeAll();
		$this->dreamCategories->save();

		//Add new category associations
		$categories = explode(',', $dreamPost['categories'] ?? '');

		foreach($categories as $category)
		{
			if(!$category)
			{
				continue;
			}
			$dreamCategory = new DreamCategoryModel();
			$dreamCategory->setId($category);
			$this->dreamCategories->add($dreamCategory);
		}
		$this->dreamCategories->save();
	}
}