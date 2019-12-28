<?php

namespace App\Storm\Model\DJ;

use App\Storm\DataDefinition\DataDefinition;
use App\Storm\DataDefinition\DataFieldDefinition;
use App\Storm\DataFormatter\BooleanDataFormatter;
use App\Storm\DataFormatter\IntegerDataFormatter;
use App\Storm\Model\BaseModel;

class DreamType extends BaseModel
{
	/** @var  int $id */
	protected $id;

	/** @var  string $id */
	protected $name;

	/** @var  bool  $default */
	protected $default;

	public function getDataDefinition(): DataDefinition
	{
		return new DataDefinition($this, [
			new DataFieldDefinition('id', new IntegerDataFormatter()),
			new DataFieldDefinition('name'),
			new DataFieldDefinition('default', new BooleanDataFormatter())
		]);
	}

	public function getId(): int
	{
		return $this->id;
	}

	/**
	 * @param int $id
	 */
	public function setId(int $id)
	{
		$this->id = $id;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function setName(string $name)
	{
		$this->name = $name;
	}

	public function isDefault(): bool
	{
		return $this->default;
	}

	public function setDefault(bool $default)
	{
		$this->default = $default;
	}

	public function getFontIcon(): string
	{
		return 'repeat';
	}
}