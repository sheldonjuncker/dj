<?php

namespace App\Presenters;

use App\Gui\Form\Element\FileInput;
use App\Storm\Form\ImportFormModel;
use Nette\Application\UI\Presenter;
use Nette\Database\Context;
use App\Gui\Form\Sorcerer;
use App\Gui\Form\Element\DropDownList;
use App\Gui\Form\Element\WithLabel;

class ImportPresenter extends Presenter
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
		$formModel = new ImportFormModel();
		$formModel->format = 'json';

		$sorcerer = new Sorcerer($formModel, '/import/execute', 'post');

		$dropDownList = new DropDownList($formModel, 'format');
		$dropDownList->addOptions($formModel->getFormatListOptions());
		$sorcerer->addElement(
			new WithLabel('Format', $dropDownList)
		);
		$sorcerer->addElement(
			new WithLabel('File', new FileInput($formModel, 'file'))
		);

		$sorcerer->addSubmit([
			'value' => 'Import'
		]);

		$this->template->add('sorcerer', $sorcerer);
	}

	/**
	 * Takes all of a user's uploaded dreams from Text or JSON format and imports.
	 *
	 * @throws \Exception
	 */
	public function renderExecute()
	{
		$post = $this->getHttpRequest()->getPost('ExportForm');
		$format = $post['format'] ?: 'json';
		if($format == 'text')
		{
			$this->importFromHtml();
		}
		else
		{
			$this->importFromJson();
		}
	}

	/**
	 * Imports
	 */
	protected function importFromHtml()
	{

	}

	/**
	 * Exports dreams to JSON and sends file.
	 *
	 * @throws \Exception
	 */
	protected function importFromJson()
	{

	}
}