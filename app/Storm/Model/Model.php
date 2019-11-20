<?php


namespace App\Storm\Model;

use App\Storm\DataDefinition\DataDefinition;
use App\Storm\Model\Info\ModelInfo;

interface Model
{
	public function getDataDefinition(): DataDefinition;
	public function getInfo(): ModelInfo;
	public function getBaseName(): string;
}