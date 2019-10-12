<?php


namespace App\Storm\Model;

use App\Storm\DataDefinition\DataDefinition;

interface Model
{
	public function getDataDefinition(): DataDefinition;
}