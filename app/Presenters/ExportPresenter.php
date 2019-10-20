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
	 * @throws \Exception
	 */
	public function renderExecute()
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