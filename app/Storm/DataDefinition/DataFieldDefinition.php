<?php

namespace App\Storm\DataDefinition;

use App\Storm\DataFormatter\DataFormatter;
use App\Storm\Model\Model;

/**
 * Class DataField
 *
 * Represents a data field to be used on a Model.
 *
 * @package App\Storm\DataDefinition
 */
class DataFieldDefinition
{
	const FORMAT_TYPE_NONE = 0;
	const FORMAT_TYPE_TO_DATA_SOURCE = 1;
	const FORMAT_TYPE_FROM_DATA_SOURCE = 2;
	const FORMAT_TYPE_TO_UI = 3;
	const FORMAT_TYPE_FROM_UI = 4;

	/** @var Model $model Used for reflection */
	protected $model;

	/** @var  string $name The name of the field */
	protected $name;

	/** @var DataFormatter $formatter */
	protected $formatter;

	public function __construct(string $name, DataFormatter $formatter = NULL)
	{
		$this->name = $name;
		$this->formatter = $formatter ?? new DataFormatter();
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @return DataFormatter
	 */
	public function getFormatter(): DataFormatter
	{
		return $this->formatter;
	}

	/**
	 * Sets the model to use for reflection.
	 *
	 * @param Model $model
	 */
	public function setModel(Model $model)
	{
		$this->model = $model;
	}

	/**
	 * Gets the value of a model field.
	 *
	 * @param int $format Specifies the formatting method to use.
	 * @return mixed
	 */
	public function getValue(int $format = self::FORMAT_TYPE_NONE)
	{
		$reflectionProperty = new \ReflectionProperty($this->model, $this->name);
		$reflectionProperty->setAccessible(true);
		$value = $reflectionProperty->getValue($this->model);

		//Handle formatting
		switch($format)
		{
			case self::FORMAT_TYPE_TO_DATA_SOURCE:
				return $this->getFormatter()->formatToDataSource($value);
			case self::FORMAT_TYPE_TO_UI:
				return $this->getFormatter()->formatToUi($value);
			case self::FORMAT_TYPE_NONE:
			default:
				return $value;
		}
	}

	/**
	 * Sets the value of a model field.
	 *
	 * @param bool $format Specifies whether or not to format the value before setting.
	 * @param mixed $value
	 */
	public function setValue($value, int $format = self::FORMAT_TYPE_NONE)
	{
		$reflectionProperty = new \ReflectionProperty($this->model, $this->name);
		$reflectionProperty->setAccessible(true);

		//Handle formatting
		switch($format)
		{
			case self::FORMAT_TYPE_FROM_DATA_SOURCE:
				$value = $this->getFormatter()->formatFromDataSource($value);
				break;
			case self::FORMAT_TYPE_FROM_UI:
				$value = $this->getFormatter()->formatFromUi($value);
		}

		$reflectionProperty->setValue($this->model, $value);
	}
}