<?php

namespace App\Tool;

use App\Storm\Model\DreamModel;
use App\Storm\Saver\SqlSaver;
use Nette\Database\Context;
use Nette\FileNotFoundException;

class DreamImporter
{
	protected $filePath;
	protected $format;
	protected $database;

	public function __construct(string $filePath, string $format, Context $database)
	{
		$this->filePath = $filePath;
		$this->format = $format;
		$this->database = $database;
	}

	public function execute()
	{
		if(!file_exists($this->filePath))
		{
			throw new FileNotFoundException("Failed to find import file {$this->filePath}.");
		}

		if($this->format == 'json')
		{
			$jsonData = json_decode(file_get_contents($this->filePath));

			$dreamModels = [];
			if($jsonData)
			{
				foreach($jsonData as $dreamData)
				{
					$dreamModels[] = DreamModel::fromArray((array) $dreamData);
				}
			}

			$dreamSaver = new SqlSaver($this->database);
			foreach($dreamModels as $dreamModel)
			{
				if($dreamModel instanceof DreamModel)
				{
					$dreamModel->setId(NULL);
					$dreamSaver->save($dreamModel);
				}
			}
		}
	}
}