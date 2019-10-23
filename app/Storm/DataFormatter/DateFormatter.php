<?php


namespace App\Storm\DataFormatter;


use Nette\Utils\DateTime;

class DateFormatter extends DataFormatter
{
	public function formatFromDataSource($data)
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

	public function formatToDataSource($data)
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