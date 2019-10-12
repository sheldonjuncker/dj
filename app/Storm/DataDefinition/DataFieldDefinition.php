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
	 * @param bool $format Specifies whether or not to format the value before returning.
	 * @return mixed
	 */
	public function getValue(bool $format = true)
	{
		$reflectionProperty = new \ReflectionProperty($this->model, $this->name);
		$reflectionProperty->setAccessible(true);
		$value = $reflectionProperty->getValue($this->model);
		if($format)
		{
			$value = $this->getFormatter()->formatToDataSource($value);
		}
		return $value;
	}

	/**
	 * Sets the value of a model field.
	 *
	 * @param bool $format Specifies whether or not to format the value before setting.
	 * @param mixed $value
	 */
	public function setValue($value, bool $format = true)
	{
		$reflectionProperty = new \ReflectionProperty($this->model, $this->name);
		$reflectionProperty->setAccessible(true);

		if($format)
		{
			$value = $this->getFormatter()->formatFromDataSource($value);
		}

		$reflectionProperty->setValue($this->model, $value);
	}
}