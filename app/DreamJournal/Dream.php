<?php

namespace App\DreamJournal;

use App\Storm\DataDefinition\DataFieldDefinition;
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
		return $this->dreamTypes->has($type);
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
		$dreamTypePost = $dreamPost['types'] ?? [];
		Debugger::dump($dreamPost);
		die();
		$dream = $this->dreamModel;

		//Set data from POST and then override user since there's only one and it's me
		$dream->fromArray($dreamPost, DataFieldDefinition::FORMAT_TYPE_FROM_UI);
		$dream->setUserId(1);

		$dreamSaver = new SqlSaver($this->database);
		$dreamSaver->save($dream);

		//Remove all dream type associations so that we can readd
		$this->dreamTypes->removeAll();

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
		$this->dreamCategories->removeAll();

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