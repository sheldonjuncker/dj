<?php

namespace App\Presenters;

use App\Gui\Form\Element\FileInput;
use App\Storm\Form\ImportFormModel;
use App\Tool\DreamImporter;
use Nette\Database\Context;
use App\Gui\Form\Sorcerer;
use App\Gui\Form\Element\DropDownList;
use App\Gui\Form\Element\WithLabel;
use Nette\FileNotFoundException;
use Nette\Http\FileUpload;

class ImportPresenter extends BasePresenter
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
		$post = $this->getHttpRequest()->getPost('ImportForm');
		$format = $post['format'] ?: 'json';

		$request = $this->getHttpRequest();
		$file = $request->getFile('ImportForm')['file'] ?? NULL;

		if(!$file instanceof FileUpload)
		{
			throw new FileNotFoundException('No file uploaded.');
		}

		$filePath = $file->getTemporaryFile();
		$dreamImporter = new DreamImporter($filePath, $format, $this->database);
		$dreamImporter->execute();
		$this->flashMessage('Successfully imported dreams.', 'success');
		$this->redirect('Import:default');
	}
}