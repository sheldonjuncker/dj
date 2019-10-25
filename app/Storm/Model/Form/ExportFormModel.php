<?php

namespace App\Storm\Form;

use App\Storm\DataDefinition\DataDefinition;
use App\Storm\DataDefinition\DataFieldDefinition;
use App\Storm\DataFormatter\DateFormatter;
use App\Storm\Model\Model;

class ExportFormModel extends FormModel implements Model
{
	public $format;
	public $start_date;
	public $end_date;

	public function getDataDefinition(): DataDefinition
	{
		$dataDefinition = new DataDefinition($this);
		$dataDefinition->addDataField(new DataFieldDefinition('format'));
		$dataDefinition->addDataField(new DataFieldDefinition('start_date', new DateFormatter()));
		$dataDefinition->addDataField(new DataFieldDefinition('end_date', new DateFormatter()));
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
			'html' => 'HTML',
			'json' => 'JSON'
		];
	}
}