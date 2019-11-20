<?php

namespace App\DreamJournal;

use App\Storm\Model\DreamModel;
use App\Storm\Model\DreamTypeModel;
use App\Storm\Query\DreamTypeQuery;
use Nette\Database\Context;

class Dream
{
	/** @var  Context $database */
	protected $database;

	/** @var  DreamModel $dreamModel */
	protected $dreamModel;

	/** @var  DreamTypeModel[] The dream's types. */
	protected $dreamTypes;

	public function __construct(DreamModel $dreamModel, Context $database)
	{
		$this->dreamModel = $dreamModel;
		$this->database = $database;
	}

	/**
	 * Gets the dream's types.
	 *
	 * @return DreamTypeModel[]
	 */
	public function getTypes(): array
	{
		//Only load once
		if($this->dreamTypes === NULL)
		{
			$this->dreamTypes = [];
			$dreamTypeQuery = new DreamTypeQuery($this->database);
			foreach($dreamTypeQuery->dream($this->dreamModel)->find() as $dreamType)
			{
				$this->dreamTypes[] = $dreamType;
			}
		}

		return $this->dreamTypes;
	}
}