<?php


namespace App\Presenters;


use App\Gui\ActionItem;
use App\Gui\Breadcrumb;
use Nette\Application\UI\Presenter;

class BasePresenter extends Presenter
{
	/** @var ActionItem[] $actionItems */
	protected $actionItems = NULL;

	/** @var Breadcrumb[] $breadcrumbs */
	protected $breadcrumbs = NULL;

	public function __construct()
	{
		parent::__construct();

		$this->breadcrumbs = new \ArrayObject();
		$this->actionItems = new \ArrayObject();
	}

	public function addBreadcrumb(Breadcrumb $breadcrumb)
	{
		$this->breadcrumbs[] = $breadcrumb;
	}

	public function addActionItem(ActionItem $actionItem)
	{
		$this->actionItems[] = $actionItem;
	}

	protected function beforeRender()
	{
		parent::beforeRender();

		if($this->template)
		{
			$this->template->add('breadcrumbs', $this->breadcrumbs);
			$this->template->add('actionItems', $this->actionItems);
		}
	}
}