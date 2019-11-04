<?php


namespace App\Storm\Form;


use App\Storm\DataDefinition\DataDefinition;
use App\Storm\DataDefinition\DataFieldDefinition;
use App\Storm\Model\DreamModel;
use App\Storm\Query\DreamQuery;
use Nette\Database\Context;

class DreamSearchModel extends FormModel
{
	public $search;

	public function getDataDefinition(): DataDefinition
	{
		return new DataDefinition($this, [
			new DataFieldDefinition('search')
		]);
	}

	/**
	 * Hacky test thing which performs a search using Python!
	 *
	 * @param Context $database
	 * @return DreamModel[]
	 */
	public function search(Context $database): array
	{
		$dreamIds = [];
		$result = NULL;

		$search = escapeshellarg($this->search);
		exec("C:/xampp/htdocs/da/venv/Scripts/python.exe C:/xampp/htdocs/da/venv/search.py {$search}", $dreamIds, $result);

		$dreams = [];
		if($result == 0)
		{
			$dreamQuery = new DreamQuery($database);
			foreach($dreamIds as $dreamId)
			{
				$parts = explode(" ", $dreamId);
				$id = $parts[0];

				$dream = $dreamQuery->id($id)->findOne();
				if($dream)
				{
					$dreams[] = $dream;
				}
			}
		}
		return $dreams;
	}
}