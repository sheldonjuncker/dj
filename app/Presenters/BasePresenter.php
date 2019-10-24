<?php


namespace App\Presenters;


use App\Gui\Breadcrumb;
use Nette\Application\UI\Presenter;

class BasePresenter extends Presenter
{
	/** @var Breadcrumb[] $breadcrumbs */
	protected $breadcrumbs = NULL;

	public function __construct()
	{
		parent::__construct();

		$this->breadcrumbs = new \ArrayObject();
	}

	public function addBreadcrumb(Breadcrumb $breadcrumb)
	{
		$this->breadcrumbs[] = $breadcrumb;
	}

	protected function beforeRender()
	{
		parent::beforeRender();

		if($this->template)
		{
			$this->template->add('breadcrumbs', $this->breadcrumbs);
		}
	}
}