<?php


namespace App\Storm\DataFormatter;


interface IDataFormatter
{
	public function formatFromDataSource($data);
	public function formatToDataSource($data);
}