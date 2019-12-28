<?php


namespace App\Presenters;


use App\Storm\Model\Freud\Concept;
use App\Storm\Model\Freud\Word;
use App\Storm\Relation\ModelMapping;
use Nette\Database\Context;

class TestPresenter extends BasePresenter
{
	protected $db;

	public function __construct(Context $db)
	{
		parent::__construct();
		$this->db = $db;
	}

	public function renderTest()
	{
		$concept = new Concept();
		$concept->id = 10;

		$word = new Word();
		$word->id = 10;
		$word->word = 'Test';

		$modelMapping = new ModelMapping(
			$concept,
			['id' => 'id'],
			$word
		);
	}
}