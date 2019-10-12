<?php


namespace App\Storm\DataDefinition;

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

	public function __construct(string $name, bool $isReadable = true, bool $isSavable = true)
	{
		$this->name = $name;
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