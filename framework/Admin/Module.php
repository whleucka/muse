<?php

namespace Nebula\Framework\Admin;

use App\Models\Session;
use Exception;
use Carbon\Carbon;

class Module
{
	/** Module */
	// Route path
	protected string $path = '';
	// SQL table
	protected string $sql_table = '';
	// Primary key of SQL table
	protected string $primary_key = '';
	// Module title
	protected string $title = '';
	// Edit mode enabled
	protected bool $edit = true;
	// Create mode enabled
	protected bool $create = true;
	// Delete mode enabled
	protected bool $delete = true;

	/** Table */
	// SQL columns
	protected array $table_columns = [];
	// GROUP BY clause
	protected string $table_group_by = '';
	// ORDER BY clause
	protected string $table_order_by = '';
	// Sort order
	protected string $table_sort = "DESC";
	// Table column format
	protected array $table_format = [];
	// Number of pagination side links
	protected int $side_links = 1;
	// WHERE clause conditions/params
	private array $table_where = [];
	// HAVING clause conditions/params
	private array $table_having = [];
	// OFFSET clause
	private int $page = 1;
	// LIMIT clause
	private int $per_page = 20;
	// Total number of pages
	private int $total_pages = 1;
	// Total row count
	private int $total_results = 0;
	// Per page option values
	private array $per_page_options = [
		10,
		20,
		50,
		100,
		200,
		500,
		750,
		1000,
		2000,
		5000
	];

	/** Filters */
	// Table filter links
	protected array $filter_links = [];
	// Searchable columns
	protected array $search_columns = [];

	public function __construct(
		object $config
	) {
		$this->title = $config->title;
		$this->path = $config->path;
		$this->sql_table = $config->sql_table;
		$this->primary_key = $config->primary_key ?? "id";
	}

	/**
	 * Process a module request
	 * Setting page number, search term, other filters, etc
	 * @param array $request the validated request
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
	 * Recursively build sidebar data struct
	 */
	private function buildLinks(?int $parent_module_id = null): array
	{
		$user = user();
		if (is_null($parent_module_id)) {
			$modules = db()->fetchAll("SELECT *
				FROM modules
				WHERE parent_module_id IS NULL
				ORDER BY item_order");
		} else {
			$modules = db()->fetchAll("SELECT *
				FROM modules
				WHERE parent_module_id = ?
				ORDER BY item_order", $parent_module_id);
		}
		$sidebar_links = [];
		foreach ($modules as $module) {
			if (!is_null($module->max_permission_level) && $user->type()->permission_level > $module->max_permission_level) continue;
			$link = [
				"id" => $module->id,
				"label" => $module->title,
				"link" => "/admin/{$module->path}",
				"children" => $this->buildLinks($module->id),
			];
			$sidebar_links[] = $link;
		}
		// Add sign out link
		if ($parent_module_id == 2) {
			$link = [
				"id" => null,
				"label" => "Sign out",
				"link" => "/admin/sign-out",
				"children" => [],
			];
			$sidebar_links[] = $link;
		}
		return $sidebar_links;
	}

	/**
	 * Get the sidebar template
	 */
	public function getSidebar(): string
	{
		$sidebar_links = $this->buildLinks();
		return template("layout/sidebar.php", ["links" => $sidebar_links]);
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
		$format = function (string $column, mixed $value) {
			return $this->format($column, $value);
		};
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
				"format" => $format,
			]),
			"pagination" => template("module/index/pagination.php", [
				"show" => $this->per_page > $this->total_results || $this->total_pages > 1,
				"current_page" => $this->page,
				"total_results" => $this->total_results,
				"total_pages" => $this->total_pages,
				"per_page" => $this->per_page,
				"per_page_options" => array_filter($this->per_page_options, fn ($value) => $value <= $this->total_results),
				"side_links" => $this->side_links,
			])
		]);
	}

	/**
	 * Format the table query condtions as 'AND' delimited
	 * @param array $conditions where or having clause
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
	 * @return int[]|string[]
	 */
	private function getTableHeaders(): array
	{
		return array_keys($this->table_columns);
	}

	/**
	 * Get extract query params
	 * These are replacement values for the '?' in the query
	 * @param array $target condition array (for where and having)
	 * @return array|<missing>
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
	 * @param bool $limit_query there exists a limit, offset clause
	 */
	private function getTableQuery(bool $limit_query = true): string
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
	private function addWhere(string $clause, int|string ...$replacements): void
	{
		$this->table_where[] = [$clause, [...$replacements]];
	}

	/**
	 * Add a table having clause
	 */
	private function addHaving(string $clause, int|string ...$replacements): void
	{
		$this->table_having[] = [$clause, [...$replacements]];
	}

	/**
	 * Override a table value
	 * The data table row may be modifed before output.
	 * Do not style the value here, that is the job of a format method
	 * @param &$row current data table row.
	 */
	protected function tableValueOverride(object &$row): void
	{
	}

	/**
	 * Template formatting function
	 */
	protected function format(string $column, mixed $value): mixed
	{
		if (is_null($value)) return '';
		// Deal with table formatting
		if (isset($this->table_format[$column])) {
			$format = $this->table_format[$column];
			// Using a formatting callback
			if (is_callable($format)) {
				return $format($column, $value);
			}
			// Using a pre-defined format method
			$method_name = "format$format";
			if (method_exists($this, $method_name)) {
				return $this->$method_name($column, $value);
			}
		}
		return $value;
	}

	/**
	 * Format IP value
	 */
	protected function formatIP(string $column, mixed $value): string
	{
		return template("format/span.php", [
			"column" => $column,
			"value" => long2ip($value)
		]);
	}

	/**
	 * Format human readable timestamp as ago diff
	 */
	protected function formatAgo(string $column, mixed $value): string
	{
		return template("format/span.php", [
			"column" => $column,
			"value" => Carbon::parse($value)->diffForHumans()
		]);
	}

	/**
	 * Print a nice error to logs
	 */
	private function pdoException(string $sql, array $params, Exception $ex): void
	{
		$out = print_r([
			"sql" => $sql,
			"params" => $params,
			"message" => $ex->getMessage(),
			"file" => $ex->getFile() . ':' . $ex->getLine(),
		], true);
		error_log($out);
	}

	/**
	 * Get the total results count, without a limit or offset
	 */
	private function getTotalCount(): int
	{
		if (!$this->sql_table || !$this->table_columns) return 0;
		$sql = $this->getTableQuery(false);
		$where_params = $this->getParams($this->table_where);
		$having_params = $this->getParams($this->table_having);
		$params = [...$where_params, ...$having_params];
		try {
			$stmt = db()->run($sql, $params);
			return $stmt->rowCount();
		} catch (Exception $ex) {
			$this->pdoException($sql, $params, $ex);
			return 0;
		}
	}

	/**
	 * Run the table query and return the dataset
	 * This dataset is for module.index
	 */
	protected function getTableData(): array|bool
	{
		if (!$this->sql_table || !$this->table_columns) return [];
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
			$this->pdoException($sql, $params, $ex);
			return [];
		}
	}
}
