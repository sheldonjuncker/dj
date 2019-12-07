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
		return new Package('bootstrap', [
			new Script('popper.min.js', Script::POS_HEAD),
			new Script('jquery.min.js', Script::POS_HEAD),
			new Script('bootstrap.js', Script::POS_HEAD),
			new Script('theme.js', Script::POS_HEAD)
		]);
	}

	public function getDreamQueryPackage(): Package
	{
		return new Package('dreamQuery', [
			new Script('vue.js'),
			new Script('dream-query.js')
		]);
	}
}