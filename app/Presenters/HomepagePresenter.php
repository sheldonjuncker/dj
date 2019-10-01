<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;

final class HomepagePresenter extends Nette\Application\UI\Presenter
{
	/** @var Nette\Database\Context $database */
	protected $database;

	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
		$results = $this->database->query("SELECT * FROM dj.dreams");
		foreach($results as $result)
		{
			print $result->title . "<br>";
		}
	}
}
