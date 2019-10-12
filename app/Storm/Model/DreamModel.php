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
		return new DataDefinition($this, $dataFields);
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

	/**
	 * @param mixed $id
	 */
	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * @return mixed
	 */
	public function getUserId()
	{
		return $this->user_id;
	}

	/**
	 * @param mixed $user_id
	 */
	public function setUserId($user_id)
	{
		$this->user_id = $user_id;
	}

	public function getTitle(): string
	{
		return $this->title;
	}

	/**
	 * @param mixed $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}

	public function getDescription(): string
	{
		return $this->description;
	}

	/**
	 * @param mixed $description
	 */
	public function setDescription($description)
	{
		$this->description = $description;
	}

	/**
	 * @param Nette\Utils\DateTime $dreamt_at
	 */
	public function setDreamtAt(Nette\Utils\DateTime $dreamt_at)
	{
		$this->dreamt_at = $dreamt_at;
	}

	public function getFormattedDate(): string
	{
		return date('d.m.Y', $this->dreamt_at->getTimestamp());
	}
}