<?php


namespace App\Storm\Form;


use App\Storm\DataDefinition\DataDefinition;
use App\Storm\DataDefinition\DataFieldDefinition;

class DreamSearchModel extends FormModel
{
	public $search;

	public function getDataDefinition(): DataDefinition
	{
		return new DataDefinition($this, [
			new DataFieldDefinition('search')
		]);
	}
}