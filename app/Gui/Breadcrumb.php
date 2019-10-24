<?php

namespace App\Gui;

class Breadcrumb
{
	protected $title;
	protected $url;
	protected $active;

	public function __construct(string $title, string $url = '', bool $active = false)
	{
		$this->title = $title;
		$this->url = $url;
		$this->active= $active;
	}

	public function setActive(bool $active)
	{
		$this->active = $active;
	}

	public function getActive(): string
	{
		return $this->active ? 'active' : '';
	}

	public function getTitle(): string
	{
		if($this->url)
		{
			return '<a href="' . $this->url . '">' . $this->title . '</a>';
		}
		else
		{
			return $this->title;
		}
	}
}