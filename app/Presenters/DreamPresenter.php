<?php

namespace App\Presenters;

use App\Gui\Form\Element\DateInput;
use App\Gui\Form\Element\HiddenInput;
use App\Gui\Form\Element\TextArea;
use App\Gui\Form\Element\TextInput;
use App\Gui\Form\Element\WithLabel;
use App\Gui\Form\Sorcerer;
use App\Storm\Model\DreamModel;
use App\Storm\Query\DreamQuery;
use App\Storm\Saver\SqlSaver;
use Nette;

final class DreamPresenter extends Nette\Application\UI\Presenter
{
	/** @var Nette\Database\Context $database */
	protected $database;

	public function __construct(Nette\Database\Context $database)
	{
		parent::__construct();
		$this->database = $database;
	}

	public function renderDefault()
	{
		$dreams = new DreamQuery($this->database);
		$this->template->add('dreams', $dreams->find());
	}

	public function renderShow(string $id)
	{
		$dreamQuery = new DreamQuery($this->database);
		$dream = $dreamQuery->id($id)->findOne();
		$this->template->add('dream', $dream);
	}

	public function renderEdit(string $id)
	{
		$dream = $this->getDream($id);
		$this->template->add('sorcerer', $this->getSorcerer($dream, 'edit'));
	}

	public function renderNew()
	{
		$this->template->add('sorcerer', $this->getSorcerer(new DreamModel(), 'create'));
	}

	public function renderSave(string $id = '')
	{
		$dream = $this->getDream($id, true);

		$dreamPost = $this->getHttpRequest()->getPost('Dream') ?: [];

		$dream->setTitle($dreamPost['title']);
		$dreamtAt = $dreamPost['dreamt_at'] ?? 'now';
		$dreamtAt = new Nette\Utils\DateTime($dreamtAt);
		$dream->setDreamtAt($dreamtAt);
		$dream->setDescription($dreamPost['description']);
		$dream->setUserId(1);

		$dreamSaver = new SqlSaver($this->database);
		$dreamSaver->save($dream);

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

		if($mode == Sorcerer::EDIT)
		{
			$sorcerer->addElement(
				new HiddenInput($model, 'id')
			);
		}

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
		if($dream = $dreamQuery->id($id)->findOne())
		{
			return $dream;
		}
		else if($createNew)
		{
			return new DreamMOdel();
		}
		else
		{
			return NULL;
		}
	}
}