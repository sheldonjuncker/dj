<?php

namespace App\Presenters;

use App\Gui\ActionItem;
use App\Gui\Form\Element\HiddenInput;
use App\Gui\Form\Element\TextInput;
use App\Gui\Form\Element\WithLabel;
use App\Gui\Form\Sorcerer;
use App\Storm\Model\DJ\DreamCategory;
use App\Storm\Query\DreamCategoryQuery;
use App\Storm\Saver\SqlSaver;
use Nette\Application\BadRequestException;
use Nette\Application\Responses\JsonResponse;
use Nette\Database\Context;
use App\Gui\Breadcrumb;

class DreamcategoryPresenter extends \App\Presenters\BasePresenter
{
	/** @var Context $database */
	protected $database;

	public function __construct(Context $database)
	{
		parent::__construct();
		$this->database = $database;

		$this->addBreadcrumb(new Breadcrumb('Dream Journal', '/'));
	}

	public function renderDefault()
	{
		$this->addBreadcrumb(new Breadcrumb('Dream Categories'));
		$this->addActionItem(new ActionItem('New', '/dreamcategory/new', 'primary'));

		$dreamCategoryQuery = new DreamCategoryQuery($this->database);

		if($this->getHttpRequest()->getQuery('type') == 'json')
		{
			$data = [];
			foreach($dreamCategoryQuery->find() as $category)
			{
				$data[] = [
					'id' => $category->getId(),
					'name' => $category->getName()
				];
			}
			$this->sendResponse(new JsonResponse($data));
		}
		else
		{
			$this->template->add('categories', $dreamCategoryQuery->find());
		}
	}

	public function renderShow(int $id)
	{
		$this->addBreadcrumb(new Breadcrumb('Dream Categories', '/dreamcategory'));
		$this->addBreadcrumb(new Breadcrumb('View'));

		$this->addActionItem(new ActionItem('New', '/dreamcategory/new', 'primary'));
		$this->addActionItem(new ActionItem('Edit', '/dreamcategory/edit/' . $id, 'secondary'));
		$this->addActionItem(new ActionItem('Delete', '/dreamcategory/delete/' . $id, 'danger'));

		$category = $this->getCategory($id);
		$sorcerer = $this->getSorcerer($category, Sorcerer::VIEW);
		$this->template->add('sorcerer', $sorcerer);
	}

	public function renderEdit(int $id)
	{
		$this->addBreadcrumb(new Breadcrumb('Dream Categories', '/dreamcategory'));
		$this->addBreadcrumb(new Breadcrumb('Edit'));

		$this->addActionItem(new ActionItem('New', '/dreamcategory/new', 'primary'));
		$this->addActionItem(new ActionItem('Cancel', '/dreamcategory/show/' . $id, 'secondary'));

		$category = $this->getCategory($id);
		$sorcerer = $this->getSorcerer($category, Sorcerer::EDIT);
		$this->template->add('sorcerer', $sorcerer);
	}

	public function renderNew()
	{
		$this->addBreadcrumb(new Breadcrumb('Dream Categories', '/dreamcategory'));
		$this->addBreadcrumb(new Breadcrumb('New'));
		$this->template->add('sorcerer', $this->getSorcerer(new DreamCategory(), Sorcerer::CREATE));
	}

	public function renderSave(int $id = NULL)
	{
		$categoryModel = $this->getCategory($id, true);
		$categoryPost = $this->getHttpRequest()->getPost('DreamCategory') ?? [];
		$categoryModel->setName($categoryPost['name'] ?? '');
		$categorySaver = new SqlSaver($this->database);
		$categorySaver->save($categoryModel);
		$this->redirect('show', ['id' => $categoryModel->getId()]);
	}

	public function renderDelete(int $id)
	{
		$categoryModel = $this->getCategory($id);
		$categorySaver = new SqlSaver($this->database);
		$categorySaver->delete($categoryModel);
		$this->redirect('default');
	}

	protected function getSorcerer(DreamCategory $category, string $mode): Sorcerer
	{
		$readOnly = $mode == Sorcerer::VIEW;

		$action = '/dreamcategory/save';
		if($mode == Sorcerer::EDIT)
		{
			$action .= '/' . $category->getId();
		}

		$sorcerer = new Sorcerer($category, $action, 'post');
		$attributes = [];
		if($readOnly)
		{
			$attributes['disabled'] = true;
		}

		$sorcerer->addElement(new WithLabel('Name', new TextInput($category, 'name', $attributes)));

		if(!$readOnly)
		{
			$submitValue = $mode == Sorcerer::CREATE ? 'Add' : 'Update';
			$sorcerer->addSubmit(['value' => $submitValue]);
		}

		return $sorcerer;
	}

	/**
	 * Loads the dream category model for usage by the controller.
	 *
	 * @param string $id
	 * @param bool $createNew Whether or not to return an empty object when it can't be found.
	 * @return DreamCategory
	 */
	public function getCategory(?int $id, bool $createNew = false): DreamCategory
	{
		$dreamCategoryQuery = new DreamCategoryQuery($this->database);
		if($id && ($dream = $dreamCategoryQuery->id($id)->findOne()))
		{
			return $dream;
		}
		else if($createNew)
		{
			return new DreamCategory();
		}
		else
		{
			throw new BadRequestException('No dream category ' . $id, '404');
		}
	}
}