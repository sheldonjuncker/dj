<?php


namespace App\Storm\Model;

use App\Storm\DataDefinition\DataDefinition;
use App\Storm\DataDefinition\DataFieldDefinition;
use App\Storm\DataFormatter\UuidDataFormatter;
use Nette;

class DreamModel implements Model
{
	public function getDataDefinition(): DataDefinition
	{
		$dataFields = [
			new DataFieldDefinition('id', new UuidDataFormatter()),
			new DataFieldDefinition('user_id'),
			new DataFieldDefinition('title'),
			new DataFieldDefinition('description'),
			new DataFieldDefinition('dreamt_at'),
			new DataFieldDefinition('created_at'),
			new DataFieldDefinition('updated_at')
		];
		return new DataDefinition($dataFields);
	}

	protected $id;
	protected $user_id;
	protected $title;
	protected $description;

	/** @var  Nette\Utils\DateTime $dreamt_at */
	protected $dreamt_at;

	/** @var  Nette\Utils\DateTime $created_at */
	protected $created_at;

	/** @var  Nette\Utils\DateTime $updated_at */
	protected $updated_at;

	public function getId(): string
	{
		return $this->id;
	}

	public function getTitle(): string
	{
		return $this->title;
	}

	public function getDescription(): string
	{
		return $this->description;
	}

	public function getFormattedDate(): string
	{
		return date('d.m.Y', $this->dreamt_at->getTimestamp());
	}
}