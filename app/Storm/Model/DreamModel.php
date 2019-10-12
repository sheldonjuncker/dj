<?php


namespace App\Storm\Model;

use Nette;

class DreamModel implements Model
{
	use ModelTrait;

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