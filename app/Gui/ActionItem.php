<?php

namespace App\Gui;

class ActionItem
{
	protected $text;
	protected $url;
	protected $type;

	public function __construct(string $text, string $url, string $type)
	{
		$this->text = $text;
		$this->url = $url;
		$this->type = $type;
	}

	public function getItem(): string
	{
		return '<a href="' . $this->url . '" class="btn btn-' . $this->type . '">' . $this->text . '</a>';
	}
}