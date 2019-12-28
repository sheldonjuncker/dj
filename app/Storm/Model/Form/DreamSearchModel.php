<?php


namespace App\Storm\Form;


use App\Gui\Form\Element\Exception;
use App\Storm\DataDefinition\DataDefinition;
use App\Storm\DataDefinition\DataFieldDefinition;
use App\Storm\Model\DJ\Dream;
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
	 * @return Dream[]
	 */
	public function search(Context $database): array
	{
		$dreamIds = [];
		$result = NULL;

		$factory = new \Socket\Raw\Factory();

		try
		{
			try
			{
				$student = $factory->createClient('127.0.0.1:1994');
			}
			catch(\Exception $e)
			{
				throw new Exception('Failed to connect to Jung.');
			}

			if(!$student->write($this->search))
			{
				throw new Exception('Failed to send message to Jung.');
			}

			$response = $student->read(8192);
			if(!$response)
			{
				throw new Exception('Failed to receive response from Jung.');
			}

			$response = json_decode($response);
			if($response === false)
			{
				throw new Exception('Invalid Jungian JSON data.');
			}

			$responseCode = $response->code ?? 'none';
			$error = $response->error ?? NULL;
			$data = $response->data ?? [];

			if($responseCode !== 200)
			{
				throw new Exception('Error: code=' . $responseCode . ', error=' . $error . '.');
			}
			else
			{
				foreach($data as $dreamData)
				{
					$dreamId = $dreamData[0] ?? NULL;
					if(!$dreamId)
					{
						throw new Exception('Invalid dream id.');
					}
					$dreamIds[] = $dreamId;
				}
			}
		}
		catch(\Exception $e)
		{
			return [];
		}

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