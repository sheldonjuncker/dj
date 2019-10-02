<?php


namespace App\Storm\Model;

use Nette\Database\Row;

interface Model
{
	public function setProperties(Row $queryResults);
}