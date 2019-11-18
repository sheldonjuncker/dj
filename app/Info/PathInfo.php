<?php

namespace App\Info;

use Nette\DI\Container;
use Nette\DI\InvalidConfigurationException;

/**
 * Class PathInfo
 *
 * Provides globally accessible path info as not everything can be setup via DI
 * without massive headaches and crappy code.
 *
 * @package App\Info
 */
class PathInfo
{
	/** @var  self $instance */
	protected static $instance;

	/** @var  array $info */
	protected $info;

	public function __construct(array $info)
	{
		$this->info = $info;
	}

	public function getTemplatePath(): string
	{
		return $this->info['parameters']['templatePath'] ?? '';
	}


	public static function setInstance(self $instance)
	{
		self::$instance = $instance;
	}

	public static function getInstance(): self
	{
		if(!self::$instance)
		{
			throw new InvalidConfigurationException('PathInfo not setup correctly.');
		}
		return self::$instance;
	}
}