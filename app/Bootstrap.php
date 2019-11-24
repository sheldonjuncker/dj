<?php

declare(strict_types=1);

namespace App;

use App\Info\PathInfo;
use Nette\Configurator;
use Nette\Neon\Neon;


class Bootstrap
{
	public static function boot(): Configurator
	{
		PathInfo::setInstance(
			new PathInfo(Neon::decode(file_get_contents(__DIR__ . '/config/path_info.neon')))
		);

		$configurator = new Configurator;

		//$configurator->setDebugMode('23.75.345.200'); // enable for your remote IP
		$configurator->enableTracy(__DIR__ . '/../log');

		$configurator->setTimeZone('America/New_York');
		$configurator->setTempDirectory(__DIR__ . '/../temp');

		//Why create your own autoloader when you have composer and it's literally the same thing???
		/*$configurator->createRobotLoader()
			->addDirectory(__DIR__)
			->register();*/

		$configurator->addConfig(__DIR__ . '/config/common.neon');
		$configurator->addConfig(__DIR__ . '/config/local.neon');
		unset($configurator->defaultExtensions['cache']);

		return $configurator;
	}
}
