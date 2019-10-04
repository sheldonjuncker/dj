<?php

namespace App\Storm\DataFormatter;

/**
 * Class DataFormatter
 *
 * Formats data going to and from the Data Source and Model.
 * This base class does not perform any formatting and is appropriate
 * as the default formatter for strings.
 *
 * @package App\Storm\DataFormatter
 */
class DataFormatter implements IDataFormatter
{
	protected $data;

	public function __construct($data)
	{
		$this->data = $data;
	}

	/**
	 * Formats data coming from the Data Source to set on the Model.
	 *
	 * @return mixed
	 */
	public function formatFromDataSource()
	{
		return $this->data;
	}

	/**
	 * Formats data coming from Model and going to the Data Source.
	 *
	 * @return mixed
	 */
	public function formatToDataSource()
	{
		return $this->data;
	}
}