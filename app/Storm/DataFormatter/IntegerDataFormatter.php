<?php


namespace App\Storm\DataFormatter;


class IntegerDataFormatter extends DataFormatter
{
	public function formatFromDataSource(): int
	{
		return intval(parent::formatFromDataSource());
	}

	public function formatToDataSource(): int
	{
		return intval(parent::formatToDataSource());
	}
}