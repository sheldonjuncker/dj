<?php

namespace App\DreamJournal;

use App\Storm\Model\Model;

abstract class RelatedData
{
	protected $loaded = false;
	protected $data;
	protected $deleted;

	public function __construct()
	{
		$this->data = new \SplObjectStorage();
		$this->deleted = new \SplObjectStorage();
	}

	/**
	 * Adds related data.
	 *
	 * @param Model $data
	 */
	public function add(Model $data)
	{
		$this->data->attach($data);

		//Make sure we don't delete it if we've added it back
		$this->deleted->detach($data);
	}

	/**
	 * Removes related data and optionally schedules for deletion.
	 *
	 * @param bool $delete Schedules for deletion
	 * @param $data
	 */
	public function remove(Model $data, bool $delete = true)
	{
		$this->data->detach($data);

		if($delete)
		{
			$this->deleted->attach($data);
		}
	}

	/**
	 * Checks to see if the model exists in the related data.
	 *
	 * @param $data
	 * @return bool
	 */
	public function has(Model $data): bool
	{
		return $this->data->offsetExists($data);
	}

	/**
	 * Gets all of the loaded data.
	 *
	 * @return Model[]
	 */
	public function get(): array
	{
		if(!$this->loaded)
		{
			$this->load();
		}

		$data = [];
		foreach($this->data as $key => $value)
		{
			$data[] = $value;
		}
		return $data;
	}

	/**
	 * Removes all related data and optionally schedules for deletion.
	 *
	 * @param bool $delete
	 */
	public function removeAll(bool $delete = true)
	{
		if($delete)
		{
			$this->deleted->addAll($this->data);
		}

		$this->data->removeAll($this->data);
	}

	/**
	 * Clears out all loaded and deleted data.
	 */
	public function clear()
	{
		$this->data->removeAll($this->data);
		$this->deleted->removeAll($this->deleted);
	}

	abstract public function load();
	abstract public function save();
}