<?php

namespace App\Gui\JS;
use App\Info\PathInfo;

/**
 * Class Script
 *
 * Represents a JS file needed to be included on the page.
 *
 * @package App\Gui\JS
 */
class Script
{
	const POS_HEAD = 0; //Loads in the header
	const POS_END = 1; //Loads at the end of the body

	/** @var  int $position */
	protected $position;

	/** @var  string The file name. */
	protected $src;

	/**
	 * Script constructor.
	 * This class assumes scripts are local.
	 *
	 * @param string $src
	 * @param int $position
	 */
	public function __construct(string $src, int $position = self::POS_END)
	{
		$this->src = PathInfo::getInstance()->getJavaScriptPath() . '/' . $src;
		$this->position = $position;
	}

	/**
	 * Generates the script tag HTML.
	 *
	 * @return string
	 */
	public function render(): string
	{
		return '<script src="' . $this->src . '"></script>';
	}

	public function getSource(): string
	{
		return $this->src;
	}

	public function getPosition(): int
	{
		return $this->position;
	}
}