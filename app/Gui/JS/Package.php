<?php

namespace App\Gui\JS;

/**
 * Class Package
 *
 * Represents a collection of JS scripts that have to be included together.
 *
 * @package App\Gui\JS
 */
class Package
{
	/** @var  string $name The package's name. */
	protected $name;

	/** @var Script[] $scripts Scripts to be included in the package. */
	protected $scripts = [];

	/**
	 * Package constructor.
	 *
	 * @param string $name
	 * @param Script[] $scripts
	 */
	public function __construct(string $name, array $scripts)
	{
		$this->name = $name;

		foreach($scripts as $script)
		{
			$this->addScript($script);
		}
	}

	public function addScript(Script $script)
	{
		$this->scripts[] = $script;
	}

	/**
	 * @return Script[]
	 */
	public function getScripts(): array
	{
		return $this->scripts;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}
}