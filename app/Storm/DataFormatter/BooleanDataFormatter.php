<?php


namespace App\Storm\DataFormatter;


class BooleanDataFormatter extends DataFormatter
{
	public function formatFromDataSource(): bool
	{
		return boolval(parent::formatFromDataSource());
	}

	public function formatToDataSource(): int
	{
		return intval(parent::formatToDataSource());
	}
}