<?php

namespace App\Presenters;

use App\DreamJournal\Dream;
use App\Gui\ActionItem;
use App\Gui\Breadcrumb;
use App\Gui\Form\Element\DateInput;
use App\Gui\Form\Element\LatteTemplate;
use App\Gui\Form\Element\Tag;
use App\Gui\Form\Element\TextArea;
use App\Gui\Form\Element\TextInput;
use App\Gui\Form\Element\WithLabel;
use App\Gui\Form\Sorcerer;
use App\Storm\Model\DreamModel;
use App\Storm\Model\DreamToDreamCategoryModel;
use App\Storm\Model\DreamToDreamTypeModel;
use App\Storm\Model\DreamTypeModel;
use App\Storm\Model\Info\InfoStore;
use App\Storm\Query\DreamCategoryQuery;
use App\Storm\Query\DreamQuery;
use App\Storm\Query\DreamToDreamCategoryQuery;
use App\Storm\Query\DreamToDreamTypeQuery;
use App\Storm\Query\DreamTypeQuery;
use App\Storm\Saver\SqlSaver;
use Nette;
use Tracy\Debugger;

final class DreamPresenter extends BasePresenter
{
	/** @var Nette\Database\Context $database */
	protected $database;

	public function __construct(Nette\Database\Context $database)
	{
		parent::__construct();
		$this->database = $database;

		$this->addBreadcrumb(new Breadcrumb('Dream Journal', '/'));
	}

	public function renderDefault()
	{
		$this->addActionItem(new ActionItem('New', '/dream/new', 'primary'));
		$this->addBreadcrumb(new Breadcrumb('Overview', '', true));

		$dreamQuery = new DreamQuery($this->database);
		$dreams = $dreamQuery->orderBy('dreamt_at', 'DESC')->findAll();

		$dreamsByDay = [];

		if(count($dreams))
		{
			$currentDay = $dreams[0]->getFormattedDate() ?? '';
			foreach($dreams as $dream)
			{
				$dreamDay = $dream->getFormattedDate();
				if($dreamDay != $currentDay)
				{
					$currentDay = $dreamDay;
					$dreamsByDay[] = NULL;
				}
				$dreamsByDay[] = $dream;
			}
		}

		$this->template->add('dreams', $dreamsByDay);
	}

	public function renderShow(string $id)
	{
		$this->addActionItem(new ActionItem('New', '/dream/new', 'primary'));
		$this->addActionItem(new ActionItem('Edit', '/dream/edit/' . $id, 'secondary'));
		$this->addActionItem(new ActionItem('Delete', '/dream/delete/' . $id, 'danger'));

		$this->addBreadcrumb(new Breadcrumb('View', '', true));

		$dreamQuery = new DreamQuery($this->database);
		$dream = $dreamQuery->id($id)->findOne();
		$this->template->add('dream', $dream);

		$database = $this->database;

		$dreamTypeQuery = new DreamTypeQuery($this->database);
		$dreamTypeQuery->excludeNormal();

		//Dream types
		$sorcerer = new Sorcerer($dream, '', '');
		$dreamTypesElement = new LatteTemplate('components/dream_types_edit.latte', [
			'dreamTypes' => $dreamTypeQuery->find(),
			'checked' => function (DreamTypeModel $type) use($dream, $database){
				$dreamToDreamTypeQuery = new DreamToDreamTypeQuery($database);
				$dreamToDreamTypeQuery->dream($dream->getId());
				$dreamToDreamTypeQuery->type($type->getId());
				if($dreamToDreamTypeQuery->findOne())
				{
					return 'checked="checked"';
				}
				else
				{
					return '';
				}
			},
			'disabled' => 'disabled="disabled"'
		]);
		$sorcerer->addElement(new WithLabel('Dream Type', $dreamTypesElement));
		$this->template->add('dreamTypesElement', $sorcerer);

		//Dream categories
		$dreamToDreamCategories = [];
		$dreamCategoriesQuery = new DreamToDreamCategoryQuery($this->database);
		$dreamToDreamCategories = $dreamCategoriesQuery->dream($dream->getId())->findAll();

		$categories = [];
		foreach($dreamToDreamCategories as $dreamToDreamCategory)
		{
			$dreamCategoryQuery = new DreamCategoryQuery($this->database);
			$dreamCategory = $dreamCategoryQuery->id($dreamToDreamCategory->getCategoryId())->findOne();
			if($dreamCategory)
			{
				$categories[] = $dreamCategory->getName();
			}
		}
		$this->template->add('categories', $categories);
	}

	public function renderEdit(string $id)
	{
		$this->addActionItem(new ActionItem('New', '/dream/new', 'primary'));
		$this->addActionItem(new ActionItem('Cancel', '/dream/show/' . $id, 'secondary'));

		$this->addBreadcrumb(new Breadcrumb('Edit', '', true));
		$dream = $this->getDream($id);
		$this->template->add('dream', $dream);
		$this->template->add('sorcerer', $this->getSorcerer($dream, 'edit'));
	}

	public function renderDelete(string $id)
	{
		$dream = $this->getDream($id);
		$sqlSaver = new SqlSaver($this->database);
		$sqlSaver->delete($dream);
		$this->redirect('default');
	}

	public function renderNew()
	{
		$this->addBreadcrumb(new Breadcrumb('New', '', true));
		$this->template->add('sorcerer', $this->getSorcerer(new DreamModel(), 'create'));
	}

	public function renderSave(string $id = '')
	{
		$dream = $this->getDream($id, true);

		$dreamPost = $this->getHttpRequest()->getPost('Dream') ?: [];
		$dreamTypePost = $this->getHttpRequest()->getPost('DreamType');

		$dream->setTitle($dreamPost['title']);
		$dreamtAt = $dreamPost['dreamt_at'] ?? 'now';
		$dreamtAt = new Nette\Utils\DateTime($dreamtAt);
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

		$this->redirect('show', [
			'id' => $dream->getId()
		]);
	}

	protected function getSorcerer(DreamModel $model, string $mode)
	{
		$action = '/dream/save';
		if($mode == Sorcerer::EDIT)
		{
			$action .= '/' . $model->getId();
		}

		$sorcerer = new Sorcerer($model, $action, 'post');
		$sorcerer->setMode($mode);

		$sorcerer->addElement(
			new WithLabel('Title', new TextInput($model, 'title'))
		);
		$sorcerer->addElement(
			new WithLabel('Date', new DateInput($model, 'dreamt_at'))
		);
		$sorcerer->addElement(
			new WithLabel('Description', new TextArea($model, 'description', [
				'rows' => 6
			]))
		);

		//Add dream to dream types
		$dreamTypeQuery = new DreamTypeQuery($this->database);
		$dreamTypeQuery->excludeNormal();

		$database = $this->database;
		$sorcerer->addElement(new WithLabel('Dream Type', new LatteTemplate('components/dream_types_edit.latte', [
			'dreamTypes' => $dreamTypeQuery->find(),
			'checked' => function (DreamTypeModel $type) use($model, $database){
				if(InfoStore::getInstance()->isNew($model))
				{
					return '';
				}

				$dreamToDreamTypeQuery = new DreamToDreamTypeQuery($database);
				$dreamToDreamTypeQuery->dream($model->getId());
				$dreamToDreamTypeQuery->type($type->getId());
				if($dreamToDreamTypeQuery->findOne())
				{
					return 'checked="checked"';
				}
				else
				{
					return '';
				}
			},
			'disabled' => ''
		])));

		//Add dream to dream categories
		$dreamToDreamCategories = [];
		if($mode == Sorcerer::EDIT)
		{
			$dreamCategoriesQuery = new DreamToDreamCategoryQuery($this->database);
			$dreamToDreamCategories = $dreamCategoriesQuery->dream($model->getId())->findAll();
		}

		$categories = [];
		foreach($dreamToDreamCategories as $dreamToDreamCategory)
		{
			$dreamCategoryQuery = new DreamCategoryQuery($this->database);
			$dreamCategory = $dreamCategoryQuery->id($dreamToDreamCategory->getCategoryId())->findOne();
			if($dreamCategory)
			{
				$categories[] = $dreamCategory->getName();
			}
		}

		$sorcerer->addElement(new WithLabel('Dream Categories', new Tag('input', '', [
			'name' => 'Dream[categories]',
			'id' => 'Dream_categories',
			'value' => implode(',', $categories)
		])));
		$sorcerer->addElement(new Tag('script', '', [
			'src' => '/assets/js/components/dream_categories.js',
			'defer' => true
		]));

		$sorcerer->addSubmit();
		return $sorcerer;
	}

	/**
	 * Loads the dream model for usage by the controller.
	 *
	 * @param string $id
	 * @param bool $createNew Whether or not to return an empty object when it can't be found.
	 * @return DreamModel|null
	 */
	public function getDream(string $id, bool $createNew = false): ?DreamModel
	{
		$dreamQuery = new DreamQuery($this->database);
		if($id && ($dream = $dreamQuery->id($id)->findOne()))
		{
			return $dream;
		}
		else if($createNew)
		{
			return new DreamModel();
		}
		else
		{
			return NULL;
		}
	}
}