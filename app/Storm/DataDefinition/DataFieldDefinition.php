<?php

namespace App\Storm\DataDefinition;

use App\Storm\DataFormatter\DataFormatter;

/**
 * Class DataField
 *
 * Represents a data field to be used on a Model.
 *
 * @package App\Storm\DataDefinition
 */
class DataFieldDefinition
{
	/** @var  string $name The name of the field */
	protected $name;

	/** @var bool $isReadable Specifies whether or not a Model can read this field from the Data Source */
	protected $isReadable = true;

	/** @var bool $isSavable Specifies whether or not the Data Source will save this field from the Model */
	protected $isSavable = true;

	/** @var DataFormatter $formatter */
	protected $formatter;

	public function __construct(string $name, DataFormatter $formatter = NULL, bool $isReadable = true, bool $isSavable = true)
	{
		$this->name = $name;
		$this->formatter = $formatter ?? new DataFormatter();
		$this->isReadable = $isReadable;
		$this->isSavable = $isSavable;
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
	 * @return bool
	 */
	public function isReadable(): bool
	{
		return $this->isReadable;
	}

	/**
	 * @return bool
	 */
	public function isSavable(): bool
	{
		return $this->isSavable;
	}
}