<?php


namespace App\Storm\DataFormatter;


class BooleanDataFormatter extends DataFormatter
{
	public function formatFromDataSource($data): bool
	{
		return boolval(parent::formatFromDataSource($data));
	}

	public function formatToDataSource($data): int
	{
		return intval(parent::formatToDataSource($data));
	}
}