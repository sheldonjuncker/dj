<?php


namespace App\Storm\DataFormatter;


interface IDataFormatter
{
	public function formatFromDataSource();
	public function formatToDataSource();
}