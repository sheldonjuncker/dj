<?php

namespace App\Presenters;

use App\Storm\Model\DreamModel;
use App\Storm\Query\DreamQuery;
use App\Storm\Saver\DreamSaver;
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
		$dreamQuery = new DreamQuery($this->database);
		$dream = $dreamQuery->id($id)->findOne();
		$this->template->add('dream', $dream);
	}

	public function renderNew()
	{

	}

	public function renderSave(string $id = '')
	{
		if($id)
		{
			$dreamQuery = new DreamQuery($this->database);
			$dream = $dreamQuery->id($id)->findOne();
		}
		else
		{
			$dream = new DreamModel();
		}

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

	public function renderTest()
	{
		$dream = DreamQuery::create($this->database)->findOne();

		$this->sendResponse(new Nette\Application\Responses\TextResponse("Hello, world!"));
	}
}