<?php


namespace App\Storm\DataFormatter;

/**
 * Class UuidDataFormatter
 *
 * Converts between string based UUIDs used by Models and the
 * BINARY data used by Data Sources.
 *
 * @package App\Storm\DataFormatter
 */
class UuidDataFormatter extends DataFormatter
{
	public function isUuid(): bool
	{
		return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/', $this->data);
	}

	public function formatFromDataSource(): string
	{
		//If the data is coming to us as UUID, we are good
		if($this->isUuid())
		{
			return $this->data;
		}
		else
		{
			//Attempt to convert from a binary string to hex
			$hex = bin2hex($this->data);
			$hex = str_pad($hex, '32', '0', STR_PAD_LEFT);
			return
				substr($hex, 0, 8) .
				'-' .
				substr($hex, 8, 4) .
				'-' .
				substr($hex, 12, 4) .
				'-' .
				substr($hex, 16, 4) .
				'-' .
				substr($hex, 20, 12)
			;
		}
	}

	public function formatToDataSource(): string
	{
		if($this->isUuid())
		{
			$hex = str_replace('-', '', $this->data);
			return hex2bin($hex);
		}
		else
		{
			return $this->data;
		}
	}
}