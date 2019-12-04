<?php


namespace App\Storm\Query;


use App\Storm\DataFormatter\UuidDataFormatter;
use App\Storm\Model\Model;
use App\Storm\Model\DreamModel;
use Nette\Database\Context;
use Nette\Database\Table\Selection;

class DreamQuery extends SqlQuery
{
	protected $scopeId;
	protected $scopeSearchText;

	/** @var array $orderBy An array of order by conditions */
	protected $orderBy = [];

	/**
	 * Primary key scope.
	 *
	 * @param string $id UUID
	 */
	public function id($id): self
	{
		$uuidFormatter = new UuidDataFormatter();
		$this->scopeId = $uuidFormatter->formatToDataSource($id);
		return $this;
	}

	public function search(string $searchText): self
	{
		$this->scopeSearchText = $searchText;
		return $this;
	}

	/**
	 * Orders by any field on the Model.
	 *
	 * @param string $field
	 * @param string $direction
	 */
	public function orderBy(string $field, string $direction = 'ASC'): self
	{
		$direction = strtoupper($direction);
		$this->orderBy[] = "$field $direction";
		return $this;
	}

	public function getModel(): Model
	{
		return new DreamModel();
	}

	protected function buildQuery(): Selection
	{
		$dreams = $this->connection->table('dream');

		foreach ($this->orderBy as $orderBy)
		{
			$dreams->order($orderBy);
		}

		if($this->scopeId !== NULL)
		{
			$dreams->where('id = ?', $this->scopeId);
		}

		if($this->scopeSearchText)
		{
			$searchText = "%" . $this->scopeSearchText . "%";
			$dreams->where('title LIKE ? OR description LIKE ?', $searchText, $searchText);
		}

		return $dreams;
	}


	# Only added for type hinting

	/**
	 * @return DreamModel[]
	 */
	public function find(): \Iterator
	{
		yield from parent::find();
	}

	/**
	 * @return DreamModel|null
	 */
	public function findOne(): ?Model
	{
		return parent::findOne();
	}

	/**
	 * @return DreamModel[]
	 */
	public function findAll(): array
	{
		return parent::findAll();
	}

	public static function create(Context $connection): self
	{
		return new DreamQuery($connection);
	}
}