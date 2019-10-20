<?php

namespace App\Presenters;

use Nette\Application\Responses\TextResponse;
use Nette\Application\UI\Presenter;
use Nette\Database\Context;

class ExportPresenter extends Presenter
{
	/** @var Context $database */
	protected $database;

	public function __construct(Context $database)
	{
		parent::__construct();
		$this->database = $database;
	}

	public function renderDefault()
	{

	}

	public function renderExecute()
	{
		$this->sendResponse(new TextResponse("Hello, world!"));
	}
}