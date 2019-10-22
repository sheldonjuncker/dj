<?php


namespace App\Storm\Form;

use App\Storm\DataDefinition\DataDefinition;
use App\Storm\DataDefinition\DataFieldDefinition;
use App\Storm\Model\BaseModel;
use App\Storm\Model\Model;

class ImportFormModel extends BaseModel implements Model
{
	public $format;
	public $file;

	public function getDataDefinition(): DataDefinition
	{
		$dataDefinition = new DataDefinition($this);
		$dataDefinition->addDataField(new DataFieldDefinition('format'));
		$dataDefinition->addDataField(new DataFieldDefinition('format'));
		return $dataDefinition;
	}

	/**
	 * Gets the options for the format list menu.
	 *
	 * @return array
	 */
	public function getFormatListOptions(): array
	{
		return [
			'text' => 'Text',
			'json' => 'JSON'
		];
	}
}