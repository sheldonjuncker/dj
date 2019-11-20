<?php

namespace App\Tool;

use App\Storm\Model\DreamModel;
use App\Storm\Saver\SqlSaver;
use Nette\Database\Context;
use Nette\FileNotFoundException;
use Nette\Utils\DateTime;

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
					//TODO: Test this as it changed
					$dreamModel = new DreamModel();
					$dreamModel->fromArray((array) $dreamData);
					$dreamModels[] = $dreamModel;
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
		else
		{
			//Importing from text is much more
			$importFile = fopen($this->filePath, "r");
			if(!$importFile)
			{
				throw new FileNotFoundException("Failed to open import file {$this->filePath}.");
			}

			while(!feof($importFile))
			{
				$this->parseDreamEvents($importFile);
			}
		}
	}

	protected function parseDreamEvents($importFile)
	{
		$dreamEventDate = NULL;
		$dream = NULL;
		$dreamSaver = new SqlSaver($this->database);

		while($line = fgets($importFile))
		{
			$matches = [];
			if(preg_match('|Dream Event[\s]+([0-9]+/[0-9]+/[0-9]+)|', $line, $matches))
			{
				//Save previous dream
				if($dream)
				{
					$dreamSaver->save($dream);
					$dream = NULL;
				}

				$dreamEventDate = new DateTime($matches[1]);
			}
			else if(preg_match('|Dream[\s]*#[\s]*[0-9]+[\s]*--[\s]*(.+)|', $line, $matches))
			{
				//Save previous dream
				if($dream)
				{
					$dreamSaver->save($dream);
				}

				$dream = new DreamModel();
				$dream->setUserId(1);
				$dream->setDreamtAt($dreamEventDate);
				$dream->setTitle($matches[1]);
			}
			else if($dream)
			{
				$description = $dream->getDescription();
				if($description)
				{
					$dream->setDescription($description . "\n" . $line);
				}
				else
				{
					$dream->setDescription($line);
				}
			}
			else
			{
				//ignore, no dream, no match
				continue;
			}
		}

		//Save last dream
		if($dream)
		{
			$dreamSaver->save($dream);
		}
	}
}