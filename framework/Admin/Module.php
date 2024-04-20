<?php

namespace Nebula\Framework\Admin;

use App\Models\Session;
use Exception;

class Module
{
	/** Module */
	protected string $sql_table = ''; // SQL table
	protected string $primary_key = ''; // Primary key of SQL table
	protected string $title = ''; // Module title -- appears top of module
	protected bool $edit = true; // Edit mode enabled
	protected bool $create = true; // Create mode enabled
	protected bool $delete = true; // Delete mode enabled

	/** Table */
	protected array $table_columns = []; // SQL columns
	protected string $table_group_by = ''; // GROUP BY clause
	protected string $table_order_by = ''; // ORDER BY clause
	protected string $table_sort = "DESC"; // Sort order
	protected int $side_links = 1; // Number of pagination side links
	private array $table_where = []; // WHERE clause conditions/params
	private array $table_having = []; // HAVING clause conditions/params
	private int $page = 1; // OFFSET clause
	private int $per_page = 25; // LIMIT clause
	private int $total_pages = 1; // Total number of pages
	private int $total_results = 0; // Total row count

	/** Filters */
	protected array $filter_links = []; // Table filter links
	protected array $search_columns = []; // Searchable columns

	public function __construct(
		private string $path, // Module route path
	) {
	}

	/**
	 * Process a module request
	 * Setting page number, search term, other filters, etc
	 */
	public function processRequest(array $request): void
	{
		if (isset($request["page"])) {
			$this->setPage(intval($request["page"]));
		}
		if (isset($request["per_page"])) {
			$this->setPerPage(intval($request["per_page"]));
		}
		if (isset($request["term"])) {
			$this->setSearch($request["term"]);
		}
		if (isset($request["filter_link"])) {
			$this->setPage(1);
			$this->setFilterLink(intval($request["filter_link"]));
		}
	}

	/**
	 * Record a user session
	 */
	private function recordSession(): void
	{
		Session::new([
			"request_uri" => $_SERVER["REQUEST_URI"],
			"ip" => ip2long(user_ip()),
			"user_id" => user()->id
		]);
	}

	/**
	 * Handle all the filters, which add where clauses to the main query
	 */
	private function filters(bool $filter_links = true): void
	{
		if ($filter_links) {
			$this->handleFilterLinks();
		}
		$this->handleSearch();
		$this->handlePerPage();
		$this->handlePage();
	}

	/**
	 * Does this module have DELETE permission
	 */
	public function hasDeletePermission(): bool
	{
		return $this->delete;
	}

	/**
	 * Does this module have EDIT permission
	 */
	public function hasEditPermission(): bool
	{
		return $this->edit;
	}

	/**
	 * Does this module have CREATE permission
	 */
	public function hasCreatePermission(): bool
	{
		return $this->create;
	}

	/**
	 * Get the module path
	 * How the module route is resolved (ie, /admin/users)
	 */
	public function getPath(): string
	{
		return $this->path;
	}

	/**
	 * Get the module title
	 * The title at the top of a module
	 */
	public function getTitle(): string
	{
		return $this->title;
	}

	/**
	 * Get the filter link row count
	 */
	public function getFilterLinkCount(int $index): int
	{
		// Get 0-indexed array
		$filters = array_values($this->filter_links);
		// Set the filter according to the index
		$filter = $filters[$index];
		// Add the filter having clause (aliases work)
		$this->addHaving($filter);
		// Update filters for proper counts
		$this->filters(false);
		// Get the rowCount
		$this->page = 1;
		$this->per_page = 1001;

		$sql = $this->getTableQuery();
		$where_params = $this->getParams($this->table_where);
		$having_params = $this->getParams($this->table_having);
		$params = [...$where_params, ...$having_params];
		$stmt = db()->run($sql, $params);
		return $stmt->rowCount();
	}

	/**
	 * Handle the current page
	 */
	private function handlePage(): void
	{
		$this->total_results = $this->getTotalCount();
		$this->total_pages = ceil($this->total_results / $this->per_page);
		$path = $this->path;
		$page = intval(session()->get($path . "_page"));
		if ($page > 0 && $page <= $this->total_pages) {
			$this->page = $page;
		} else {
			if ($page < 1) {
				session()->set($path . "_page", 1);
				$this->page = 1;
			} else if ($page > $this->total_pages) {
				session()->set($path . "_page", $this->total_pages);
				$this->page = $this->total_pages;
			}
		}
	}

	/**
	 * Handle the results per page
	 */
	private function handlePerPage(): void
	{
		$path = $this->path;
		$per_page = intval(session()->get($path . "_per_page"));
		if ($per_page > 0) {
			$this->per_page = $per_page;
		}
	}

	/**
	 * Handle the search term, add where clause
	 */
	private function handleSearch(): void
	{
		$path = $this->path;
		$search_term = session()->get($path . "_search_term");
		if ($search_term) {
			$conditions = array_map(fn ($column) => "($column LIKE ?)", $this->search_columns);
			$this->addWhere(implode(" OR ", $conditions), ...array_fill(0, count($this->search_columns), "%$search_term%"));
		}
	}

	/**
	 * Handle the filter links, add where clause
	 */
	private function handleFilterLinks(): void
	{
		if (count($this->filter_links) === 0) return;
		$path = $this->path;
		$index = session()->get($path . "_filter_link");
		// The first filter link is the default
		if (is_null($index)) {
			$index = 0;
			session()->set($path . "_filter_link", 0);
		}
		$filters = array_values($this->filter_links);
		$filter = $filters[$index];
		// Use having so that aliases work
		$this->addHaving($filter);
	}

	/**
	 * Set the session page
	 */
	private function setPage(int $page): void
	{
		$path = $this->path;
		session()->set($path . "_page", $page);
		$this->page = $page;
	}

	/**
	 * Set the results per page
	 */
	private function setPerPage(int $per_page): void
	{
		$path = $this->path;
		session()->set($path . "_per_page", $per_page);
		session()->set($path . "_page", 1);
		$this->per_page = $per_page;
	}

	/**
	 * Set the session search term
	 */
	private function setSearch(string $term): void
	{
		$path = $this->path;
		if (trim($term) !== '') {
			session()->set($path . "_search_term", $term);
		} else {
			session()->delete($path . "_search_term");
		}
	}

	/**
	 * Set the session search term
	 */
	private function setFilterLink(int $index): void
	{
		$path = $this->path;
		session()->set($path . "_filter_link", $index);
	}

	/**
	 * Render the view module.index
	 */
	public function viewIndex(): string
	{
		$this->recordSession();
		$this->filters();
		$path = $this->path;
		return template("module/index/index.php", [
			"filters" => [
				"search" => template("module/index/search.php", [
					"show" => !empty($this->search_columns),
					"term" => session()->get($path . "_search_term"),
				]),
				"link" => template("module/index/filter_links.php", [
					"action" => "/admin/$path/link-count",
					"show" => !empty($this->filter_links),
					"current" => session()->get($path . "_filter_link"),
					"links" => array_keys($this->filter_links),
				]),
			],
			"table" => template("module/index/table.php", [
				"headers" => $this->getTableHeaders(),
				"data" => $this->getTableData(),
			]),
			"pagination" => template("module/index/pagination.php", [
				"show" => $this->per_page > $this->total_results || $this->total_pages > 1,
				"current_page" => $this->page,
				"total_results" => $this->total_results,
				"total_pages" => $this->total_pages,
				"per_page" => $this->per_page,
				"per_page_options" => [
					5,
					10,
					25,
					50,
					100,
					200,
					500,
					750,
					1000,
					2000,
					5000
				],
				"side_links" => $this->side_links,
			])
		]);
	}

	/**
	 * Format the table query condtions as 'AND' delimited
	 */
	protected function formatConditions(array $conditions): string
	{
		$out = [];
		foreach ($conditions as $item) {
			[$clause, $params] = $item;
			// Add parens to clause for order of ops
			$out[] = '(' . $clause . ')';
		}
		return sprintf("%s", implode(" AND ", $out));
	}

	/**
	 * Get the table columns headers
	 */
	private function getTableHeaders(): array
	{
		return array_keys($this->table_columns);
	}

	/**
	 * Get extract query params
	 * These are replacement values for the '?' in the query
	 */
	private function getParams(array $target): array
	{
		if (!$target) return [];
		$params = [];
		foreach ($target as $item) {
			[$clause, $param_array] = $item;
			$params = [...$params, ...$param_array];
		}
		return $param_array;
	}

	/**
	 * Get the table query
	 * This is the query for module.index
	 */
	private function getTableQuery($limit_query = true): string
	{
		$columns = $this->table_columns ? implode(", ", $this->table_columns) : '*';
		$where = $this->table_where ? "WHERE " . $this->formatConditions($this->table_where) : '';
		$group_by = $this->table_group_by ? "GROUP BY " . $this->table_group_by : '';
		$having = $this->table_having ? "HAVING " . $this->formatConditions($this->table_having) : '';
		$order_by = $this->table_order_by ? "ORDER BY " . $this->table_order_by . ' ' . $this->table_sort : '';
		$page = max(($this->page - 1) * $this->per_page, 0);
		$limit = $limit_query ? "LIMIT " . $page . ", " . $this->per_page : '';
		return sprintf("SELECT %s FROM %s %s %s %s %s %s", ...[
			$columns,
			$this->sql_table,
			$where,
			$group_by,
			$having,
			$order_by,
			$limit,
		]);
	}

	/**
	 * Add a table where clause
	 */
	protected function addWhere(string $clause, int|string ...$replacements)
	{
		$this->table_where[] = [$clause, [...$replacements]];
	}

	/**
	 * Add a table having clause
	 */
	protected function addHaving(string $clause, int|string ...$replacements)
	{
		$this->table_having[] = [$clause, [...$replacements]];
	}

	/**
	 * Override a table value
	 */
	protected function tableValueOverride(&$row): void
	{
	}

	/**
	 * Get the total results count, without a limit or offset
	 */
	private function getTotalCount(): int
	{
		if (!$this->sql_table) return [];
		$sql = $this->getTableQuery(false);
		$where_params = $this->getParams($this->table_where);
		$having_params = $this->getParams($this->table_having);
		$params = [...$where_params, ...$having_params];
		try {
			$stmt = db()->run($sql, $params);
			return $stmt->rowCount();
		} catch (Exception $ex) {
			error_log($ex->getMessage());
			throw new Exception("total count query error");
		}
	}

	/**
	 * Run the table query and return the dataset
	 * This dataset is for module.index
	 */
	protected function getTableData(): array
	{
		if (!$this->sql_table) return [];
		$sql = $this->getTableQuery();
		$where_params = $this->getParams($this->table_where);
		$having_params = $this->getParams($this->table_having);
		$params = [...$where_params, ...$having_params];
		try {
			$stmt = db()->run($sql, $params);
			$results = $stmt->fetchAll();
			foreach ($results as $data) {
				$this->tableValueOverride($data);
			}
			return $results;
		} catch (Exception $ex) {
			error_log($ex->getMessage());
			throw new Exception("table data query error");
		}
	}
}
