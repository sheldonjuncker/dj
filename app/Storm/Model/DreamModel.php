<?php


namespace App\Storm\Model;


class DreamModel implements Model
{
	use ModelTrait;

	protected $id;
	protected $user_id;
	protected $title;
	protected $description;
	protected $dreamt_at;
	protected $created_at;
	protected $updated_at;

	public function getId(): string
	{
		return $this->id;
	}

	public function getTitle(): string
	{
		return $this->title;
	}
}