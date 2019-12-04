<?php

namespace App\Gui\JS;

class ExternalScript extends Script
{
	/**
	 * ExternalScript constructor.
	 * Same as parent class except that it does not process the file path
	 * allowing for external scripts.
	 *
	 * @param string $src
	 * @param int $position
	 */
	public function __construct($src, $position = self::POS_END)
	{
		$this->src = $src;
		$this->position = $position;
	}
}