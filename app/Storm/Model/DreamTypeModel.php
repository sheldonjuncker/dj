<?php

namespace App\Storm\Model;

use App\Storm\DataDefinition\DataDefinition;
use App\Storm\DataDefinition\DataFieldDefinition;
use App\Storm\DataFormatter\BooleanDataFormatter;
use App\Storm\DataFormatter\IntegerDataFormatter;

class DreamTypeModel extends BaseModel implements Model
{
	protected $id;
	protected $name;
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
}