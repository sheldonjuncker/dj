<?php


namespace App\Storm\Saver;


class DreamSaver extends SqlSaver
{
	public function getPrimaryKey(): array
	{
		return ['id'];
	}

	public function getTableName(): string
	{
		return 'dreams';
	}
}