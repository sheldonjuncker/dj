<?php


namespace App\Storm\DataFormatter;


class IntegerDataFormatter extends DataFormatter
{
	public function formatFromDataSource($data): int
	{
		return intval(parent::formatFromDataSource($data));
	}

	public function formatToDataSource($data): int
	{
		return intval(parent::formatToDataSource($data));
	}
}