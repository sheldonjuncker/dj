<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Storm\Query\DreamQuery;
use Nette;

final class HomepagePresenter extends Nette\Application\UI\Presenter
{
	/** @var Nette\Database\Context $database */
	protected $database;

	public function __construct(Nette\Database\Context $database)
	{
		parent::__construct();
		$this->database = $database;
	}

	public function renderShow()
	{
		$dreamQuery = new DreamQuery($this->database);
		$this->template->add('dreams', $dreamQuery->findAll());
	}
}
