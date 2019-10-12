<?php

namespace App\Presenters;

use App\Storm\Query\DreamQuery;
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

	}
}