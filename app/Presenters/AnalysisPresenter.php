<?php

namespace App\Presenters;

use Nette\Database\Context;
use App\Gui\Breadcrumb;
use App\Gui\JS\Script;

class AnalysisPresenter extends BasePresenter
{
	/** @var Context $database */
	protected $database;

	public function __construct(Context $database)
	{
		parent::__construct();
		$this->database = $database;

		$this->addBreadcrumb(new Breadcrumb('Dream Journal', '/'));

		//Register scripts needed for dreams
		$this->getScriptRegistrar()->registerScript(
			new Script('chart.js')
		);
	}

	public function renderDefault()
	{

	}
}