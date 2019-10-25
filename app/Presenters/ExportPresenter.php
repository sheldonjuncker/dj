<?php

namespace App\Presenters;

use App\Gui\Breadcrumb;
use App\Gui\Form\Element\DateInput;
use App\Gui\Form\Element\DropDownList;
use App\Gui\Form\Element\WithLabel;
use App\Gui\Form\Sorcerer;
use App\Storm\Form\ExportFormModel;
use App\Storm\Query\DreamQuery;
use Nette\Application\Responses\FileResponse;
use Nette\Database\Context;
use Nette\Utils\DateTime;

class ExportPresenter extends BasePresenter
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
		$this->addBreadcrumb(new Breadcrumb('Export', '', true));

		$formModel = new ExportFormModel();
		$formModel->format = 'json';
		$formModel->start_date = new DateTime();
		$formModel->end_date = new DateTime();

		$sorcerer = new Sorcerer($formModel, '/export/execute', 'post');

		$sorcerer->addElement(
			new WithLabel('Start Date', new DateInput($formModel, 'start_date'))
		);

		$sorcerer->addElement(
			new WithLabel('End Date', new DateInput($formModel, 'end_date'))
		);

		$dropDownList = new DropDownList($formModel, 'format');
		$dropDownList->addOptions($formModel->getFormatListOptions());
		$sorcerer->addElement(
			new WithLabel('Format', $dropDownList)
		);

		$sorcerer->addSubmit([
			'value' => 'Export'
		]);

		$this->template->add('sorcerer', $sorcerer);
	}

	/**
	 * Takes all of a user's dreams and exports them as JSON.
	 *
	 * @throws \Exception
	 */
	public function renderExecute()
	{
		$formModel = new ExportFormModel($this->getHttpRequest());
		$format = $formModel->format;

		if($format == 'html')
		{
			$this->exportToHtml();
		}
		else
		{
			$this->exportToJson();
		}
	}

	protected function exportToHtml()
	{
		$this->template->setFile(__DIR__ . '/templates/Export/html.latte');
		$dreamQuery = new DreamQuery($this->database);

		//Group dreams by date
		$dateToDreams = [];
		$dreamCount = 0;

		foreach($dreamQuery->find() as $dream)
		{
			$dreamCount++;
			$dreamDate = $dream->getFormattedDate();
			if(!isset($dateToDreams[$dreamDate]))
			{
				$dateToDreams[$dreamDate] = [];
			}
			$dateToDreams[$dreamDate][] = $dream;
		}

		$this->template->add('dateToDreams', $dateToDreams);
		$this->template->add('dreamCount', $dreamCount);
		$this->template->add('userName', 'Sheldon Juncker');
		$this->template->add('currentTime', date('l, F dS Y'));
	}

	/**
	 * Exports dreams to JSON and sends file.
	 *
	 * @throws \Exception
	 */
	protected function exportToJson()
	{
		$exportId = (string) round(microtime(true));
		$tempFileName = __DIR__ . '/../../temp/export/dream_export_' . $exportId . '.json';

		$tempFile = fopen($tempFileName, 'w+');
		if(!$tempFile)
		{
			throw new \Exception('Failed to open file.');
		}

		$dreamQuery = new DreamQuery($this->database);

		fwrite($tempFile, "[\n");
		foreach($dreamQuery->find() as $dream)
		{
			//Add appropriate tabbing
			$dreamJsonLines = explode("\n", $dream->toJson());
			foreach($dreamJsonLines as $lineNumber => $dreamJsonLine)
			{
				$dreamJsonLines[$lineNumber] = "\t" . $dreamJsonLine;
			}
			$dreamJson = implode("\n", $dreamJsonLines);
			fwrite($tempFile, $dreamJson);
			fwrite($tempFile, ",\n");
		}
		fseek($tempFile, -2, SEEK_CUR);
		fwrite($tempFile, "\n]");
		fclose($tempFile);
		$this->sendResponse(new FileResponse($tempFileName));
	}
}