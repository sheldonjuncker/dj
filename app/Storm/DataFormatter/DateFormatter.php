<?php


namespace App\Storm\DataFormatter;


use Nette\Utils\DateTime;

class DateFormatter extends DataFormatter
{
	//Nette handles formatting to/from DB

	public function formatToUi($data)
	{
		if($data instanceof DateTime)
		{
			return $data->format('Y-m-d');
		}
		else
		{
			return $data;
		}
	}

	public function formatFromUi($data)
	{
		if(!$data instanceof DateTime)
		{
			return new DateTime($data);
		}
		else
		{
			return $data;
		}
	}
}