<?php


namespace App\Gui\JS;

/**
 * Class Registrar
 *
 * Registers scripts to be included by Nette.
 *
 * @package App\Gui\JS
 */
class Registrar
{
	/** @var Script[] $scripts All stand-alone scripts to be included, keyed with source. */
	protected $scripts = [];

	/** @var Package[] $packages All packages to be included, keyed by package name. */
	protected $packages = [];

	/**
	 * Registers a script to be included.
	 *
	 * @param Script $script
	 */
	public function registerScript(Script $script)
	{
		$this->scripts[$script->getSource()] = $script;
	}

	/**
	 * Unregisters a script.
	 *
	 * @param Script $script
	 */
	public function unregisterScript(Script $script)
	{
		unset($this->scripts[$script->getSource()]);
	}

	/**
	 * Registers a package so that all of it's scripts will be included.
	 *
	 * @param Package $package
	 */
	public function registerPackage(Package $package)
	{
		$this->packages[$package->getName()] = $package;
	}

	/**
	 * Unregisters a package.
	 *
	 * @param Package $package
	 */
	public function unregisterPackage(Package $package)
	{
		unset($this->packages[$package->getName()]);
	}

	/**
	 * Gets a unique array of all header scripts.
	 *
	 * @return Script[]
	 */
	public function getHeaderScripts(): array
	{
		return $this->getScriptsByPosition(Script::POS_HEAD);
	}

	/**
	 * Gets unique body scripts.
	 *
	 * @return Script[]
	 */
	public function getBodyScripts(): array
	{
		return $this->getScriptsByPosition(Script::POS_END);
	}

	/**
	 * Gets script by position to be included.
	 *
	 * @param int $position
	 * @return array
	 */
	public function getScriptsByPosition(int $position)
	{
		$uniqueScripts = $this->getUniqueScripts($this->getAllScripts());
		$positionScripts = [];
		foreach($uniqueScripts as $script)
		{
			if($script->getPosition() === $position)
			{
				$positionScripts[] = $script;
			}
		}
		return $positionScripts;
	}

	/**
	 * Gets all scripts including duplicates.
	 *
	 * @return Script[]
	 */
	public function getAllScripts(): array
	{
		$scripts = array_values($this->scripts);
		foreach($this->packages as $package)
		{
			$scripts = array_merge($scripts, $package->getScripts());
		}
		return $scripts;
	}

	/**
	 * Gets unique scripts.
	 * Latter scripts override former ones if the sources are the same
	 * which only matters if the POS is different.
	 *
	 * @param Script[] $scripts
	 * @return Script[]
	 */
	public function getUniqueScripts(array $scripts)
	{
		$uniqueScripts = [];
		foreach($scripts as $script)
		{
			$uniqueScripts[$script->getSource()] = $script;
		}
		return array_values($scripts);
	}

	/**
	 * Unregisters all scripts and packages.
	 */
	public function unregisterAll()
	{
		$this->scripts = [];
		$this->packages = [];
	}
}