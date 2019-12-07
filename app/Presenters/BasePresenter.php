<?php


namespace App\Presenters;


use App\Gui\ActionItem;
use App\Gui\Breadcrumb;
use App\Gui\JS\PackageStore;
use App\Gui\JS\Registrar;
use Nette\Application\UI\Presenter;

class BasePresenter extends Presenter
{
	/** @var ActionItem[] $actionItems */
	protected $actionItems = NULL;

	/** @var Breadcrumb[] $breadcrumbs */
	protected $breadcrumbs = NULL;

	/** @var Registrar $scriptRegistrar */
	protected $scriptRegistrar = NULL;

	public function __construct()
	{
		parent::__construct();

		$this->breadcrumbs = new \ArrayObject();
		$this->actionItems = new \ArrayObject();

		//Setup scripts
		$this->scriptRegistrar = new Registrar();
		$packageStore = new PackageStore();
		$this->getScriptRegistrar()->registerPackage(
			$packageStore->getBootstrapPackage()
		);
	}

	public function getScriptRegistrar(): Registrar
	{
		return $this->scriptRegistrar;
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
			//Set scripts
			$this->template->add('headerScripts', $this->getScriptRegistrar()->getHeaderScripts());
			$this->template->add('bodyScripts', $this->getScriptRegistrar()->getBodyScripts());

			//Add breadcrumbs and action items
			$this->template->add('breadcrumbs', $this->breadcrumbs);
			$this->template->add('actionItems', $this->actionItems);
		}
	}
}