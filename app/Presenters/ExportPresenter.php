<?php

namespace App\Presenters;

use App\Storm\Query\DreamQuery;
use Nette\Application\Responses\FileResponse;
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

	/**
	 * Takes all of a user's dreams and exports them as JSON.
	 *
	 * @param string $type Type of output to generate json|html
	 * @throws \Exception
	 */
	public function renderExecute(string $type)
	{
		if($type == 'html')
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