<?php


namespace App\Presenters;

use App\Gui\Form\Element\TextInput;
use App\Gui\Form\Element\WithLabel;
use App\Gui\Form\Sorcerer;
use App\Storm\Form\DreamSearchModel;
use App\Storm\Query\DreamQuery;
use Nette\Database\Context;
use App\Gui\Breadcrumb;

class SearchPresenter extends BasePresenter
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
		$this->addBreadcrumb(new Breadcrumb('Search'));
		$this->template->add('sorcerer', $this->getSorcerer());
	}

	public function renderResults()
	{
		$this->addBreadcrumb(new Breadcrumb('Search Results'));

		$searchModel = new DreamSearchModel($this->getHttpRequest());
		$dreamQuery = new DreamQuery($this->database);
		$dreamQuery->search($searchModel->search);
		$this->template->add('dreams', $dreamQuery->find());
	}

	public function getSorcerer(): Sorcerer
	{
		$searchModel = new DreamSearchModel();
		$sorcerer = new Sorcerer($searchModel, '/search/results', 'post');
		$sorcerer->addElement(
			new WithLabel('Search Text', new TextInput($searchModel, 'search'))
		);
		$sorcerer->addSubmit(['value' => 'Search']);
		return $sorcerer;
	}
}