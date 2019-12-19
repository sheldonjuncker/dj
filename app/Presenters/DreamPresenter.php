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
use App\Gui\JS\Script;
use App\Storm\Model\DreamModel;
use App\Storm\Model\DreamTypeModel;
use App\Storm\Query\DreamQuery;
use App\Storm\Saver\SqlSaver;
use Nette;

final class DreamPresenter extends BasePresenter
{
	/** @var Nette\Database\Context $database */
	protected $database;

	public function __construct(Nette\Database\Context $database)
	{
		parent::__construct();
		$this->database = $database;

		$this->addBreadcrumb(new Breadcrumb('Dream Journal', '/'));

		//Register scripts needed for dreams
		$this->getScriptRegistrar()->registerScript(
			new Script('tagsinput/tagsinput-typeahead.js')
		);
		$this->getScriptRegistrar()->registerScript(
			new Script('summernote.js')
		);
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

		$dreamWeaver = new Dream($dream, $this->database);

		//Dream types
		$sorcerer = new Sorcerer($dream, '', '');
		$dreamTypesElement = new LatteTemplate('components/dream_types_edit.latte', [
			'dreamTypes' => $dreamWeaver->getAvailableTypes(),
			'checked' => function (DreamTypeModel $type) use($dreamWeaver){
				return $dreamWeaver->hasType($type) ? 'checked="checked"' : '';
			},
			'disabled' => 'disabled="disabled"'
		]);
		$sorcerer->addElement(new WithLabel('Dream Type', $dreamTypesElement));
		$this->template->add('dreamTypesElement', $sorcerer);

		//Dream categories
		$this->template->add('categories', $dreamWeaver->getCategories());
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

		$dreamWeaver = new Dream($dream, $this->database);
		$dreamWeaver->save($dreamPost);

		$this->redirect('show', [
			'id' => $dream->getId()
		]);
	}

	protected function getSorcerer(DreamModel $model, string $mode)
	{
		$dreamWeaver = new Dream($model, $this->database);

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
				'rows' => 6,
				'id' => 'Dream_description',
				'class' => 'dj-summernote'
			]))
		);

		$sorcerer->addElement(new WithLabel('Dream Type', new LatteTemplate('components/dream_types_edit.latte', [
			'dreamTypes' => $dreamWeaver->getAvailableTypes(),
			'checked' => function (DreamTypeModel $type) use($dreamWeaver){
				return $dreamWeaver->hasType($type) ? 'checked="checked"' : '';
			},
			'disabled' => ''
		])));

		//Add dream to dream categories
		$categories = [];
		foreach($dreamWeaver->getCategories() as $category)
		{
			$categories[] = $category->getId();
		}

		$sorcerer->addElement(new WithLabel('Dream Categories', new Tag('input', '', [
			'name' => 'Dream[categories]',
			'id' => 'Dream_categories',
			'value' => implode(',', $categories)
		])));

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