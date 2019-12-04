<?php

namespace App\Gui\JS;

/**
 * Class PackageStore
 *
 * Factory class for creating necessary packages.
 *
 * @package App\Gui\JS
 */
class PackageStore
{
	public function getBootstrapPackage(): Package
	{
		return new Package('', []);
	}

	public function getDreamQueryPackage(): Package
	{
		return new Package('dreamQuery', [
			new Script('vue.js'),
			new Script('dream-query.js')
		]);
	}
}