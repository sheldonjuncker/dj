<?php

namespace App\Presenters;

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

	}

	public function renderShow(string $id)
	{

	}
}