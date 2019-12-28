<?php


namespace App\Storm\Query;


use App\Storm\DataFormatter\UuidDataFormatter;
use App\Storm\Model\Model;
use App\Storm\Model\DJ\Dream;
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
		return new Dream();
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

	/**
 * Gets all dream counts on each day of the week for analysis/charts.
 *
 * @return array
 */
	public function getDreamCountByDayOfWeek(int $userId = NULL): array
	{
		$sql = "
			SELECT
				DAYNAME(dream.dreamt_at) AS 'day_of_week',
				COUNT(dream.id) AS 'count'
			FROM
				dj.dream dream
			GROUP BY
				DAYNAME(dream.dreamt_at)
			ORDER BY
				DAYOFWEEK(dream.dreamt_at) ASC
			;
		";
		$dreamCountData = $this->connection->fetchAll($sql);
		$dreamCountDataByDay = [];
		foreach($dreamCountData as $row)
		{
			$dreamCountDataByDay[$row['day_of_week']] = $row['count'];
		}
		return $dreamCountDataByDay;
	}

	/**
	 * Gets all dream counts on each day of the week for analysis/charts.
	 *
	 * @return array
	 */
	public function getDreamCountByMonth(int $userId = NULL): array
	{
		$sql = "
			SELECT
				CONCAT(MONTHNAME(dream.dreamt_at), ', ', YEAR(dream.dreamt_at)) AS 'year-month',
				COUNT(dream.id) AS 'count'
			FROM
				dj.dream dream
			GROUP BY
				YEAR(dream.dreamt_at), MONTHNAME(dream.dreamt_at)
			ORDER BY
				YEAR(dream.dreamt_at), MONTH(dream.dreamt_at) ASC
			;
		";
		$dreamCountData = $this->connection->fetchAll($sql);
		$dreamCountDataByDay = [];
		foreach($dreamCountData as $row)
		{
			$dreamCountDataByDay[$row['year-month']] = $row['count'];
		}
		return $dreamCountDataByDay;
	}

	public function getDreamCountByCategory(int $userId = null): array
	{
		$sql = "
			SELECT
				cat.name AS 'name',
				COUNT(dream.id) AS 'count'
			FROM
				dj.dream dream
			INNER JOIN
				dj.dream_to_dream_category d2cat ON d2cat.dream_id = dream.id
			INNER JOIN
				dj.dream_category cat ON cat.id = d2cat.category_id
			GROUP BY
				cat.id
			ORDER BY
				cat.name ASC
			;
		";
		$dreamCountData = $this->connection->fetchAll($sql);
		$dreamCountDataByDay = [];
		foreach($dreamCountData as $row)
		{
			$dreamCountDataByDay[$row['name']] = $row['count'];
		}
		return $dreamCountDataByDay;
	}

	# Only added for type hinting

	/**
	 * @return Dream[]
	 */
	public function find(): \Iterator
	{
		yield from parent::find();
	}

	/**
	 * @return Dream|null
	 */
	public function findOne(): ?Model
	{
		return parent::findOne();
	}

	/**
	 * @return Dream[]
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