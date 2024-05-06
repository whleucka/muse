<?php

namespace Nebula\Framework\Admin;

use App\Models\Session;
use Exception;
use Carbon\Carbon;
use Nebula\Framework\Alerts\Flash;
use Nebula\Framework\Controller\Controller;

class Module
{
    /** Module */
    // Route path
    protected string $path = "";
    // SQL table
    protected string $sql_table = "";
    // Primary key of SQL table
    protected string $primary_key = "";
    // Module title
    protected string $title = "";
    // Edit mode enabled
    protected bool $edit = false;
    // Create mode enabled
    protected bool $create = false;
    // Delete mode enabled
    protected bool $delete = false;
    // Validation rules
    protected array $validation_rules = [];

    /** Table */
    // SQL columns
    protected array $table_columns = [];
    // GROUP BY clause
    protected string $table_group_by = "";
    // ORDER BY clause
    protected string $table_order_by = "";
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
        5000,
    ];
    protected bool $show_row_actions = true;

    /** Filters */
    // Table filter links
    protected array $filter_links = [];
    // Searchable columns
    protected array $search_columns = [];

    /** Form */
    protected array $form_columns = [];
    protected array $form_control = [];

    public function __construct(
        private object $config,
        private Controller $controller
    ) {
        $this->title = $config->title;
        $this->path = $config->path;
        $this->sql_table = $config->sql_table;
        $this->primary_key = $config->primary_key ?? "id";
        $this->recordSession();
        $this->definition();
    }

    /**
     * Provide module definition here
     */
    public function definition(): void
    {
    }

    public function render(string $type, ?string $id = null): string
    {
        $content = match ($type) {
            "index" => $this->viewIndex(),
            "create" => $this->viewCreate(),
            "edit" => $this->viewEdit($id),
        };
        return $this->controller->render("layout/admin.php", [
            "module_title" => $this->getTitle(),
            "sidebar" => $this->getSidebar(),
            "content" => $content,
        ]);
    }

    /**
     * Process a module request
     * Setting page number, search term, other filters, etc
     * @param array $request the validated request
     */
    public function processRequest(array $request)
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
        if (isset($request["export_csv"])) {
            $this->filters();
            $this->exportCsv();
        }
        if (isset($request["filter_count"])) {
            $count = $this->getFilterLinkCount(
                intval($request["filter_count"])
            );
            return $count > 1000 ? "1000+" : $count;
        }
    }

    protected function exportCsv(): void
    {
        header("Content-Type: text/csv");
        header('Content-Disposition: attachment; filename="csv_export.csv"');
        $fp = fopen("php://output", "wb");
        $titles = array_keys($this->table_columns);
        fputcsv($fp, $titles);
        $this->per_page = 1000;
        $this->page = 1;
        $this->total_results = $this->getTotalCount();
        $this->total_pages = ceil($this->total_results / $this->per_page);
        while ($this->page <= $this->total_pages) {
            $data = $this->getIndexData();
            foreach ($data as $item) {
                $this->tableValueOverride($item);
                $values = array_values((array)$item);
                fputcsv($fp, $values);
            }
            $this->page++;
        }
        fclose($fp);
        exit();
    }

    /**
     * Record a user session
     */
    private function recordSession(): void
    {
        Session::new([
            "request_uri" => $_SERVER["REQUEST_URI"],
            "ip" => ip2long(user_ip()),
            "user_id" => user()->id,
        ]);
    }

    /**
     * Format the table query condtions as 'AND' delimited
     * @param array $conditions where or having clause
     */
    protected function formatAnd(array $conditions): string
    {
        $out = [];
        foreach ($conditions as $item) {
            [$clause, $params] = $item;
            // Add parens to clause for order of ops
            $out[] = "(" . $clause . ")";
        }
        return sprintf("%s", implode(" AND ", $out));
    }

    /**
     * Format the query condtions as ',' delimited
     * @param array $conditions
     */
    protected function formatComma(array $conditions): string
    {
        return sprintf("%s", implode(", ", $conditions));
    }

    /**
     * Get extract query params
     * These are replacement values for the '?' in the query
     * @param array $target condition array (for where and having)
     * @return array|<missing>
     */
    private function getParams(array $target): array
    {
        if (!$target) {
            return [];
        }
        $params = [];
        foreach ($target as $item) {
            [$clause, $param_array] = $item;
            $params = [...$params, ...$param_array];
        }
        return $param_array;
    }

    public function getFormColumns()
    {
        return $this->form_columns;
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

    public function getValidationRules(): array
    {
        return $this->validation_rules;
    }

    /**
     * Recursively build sidebar data struct
     * @return array<int,array<string,mixed>>
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
            $modules = db()->fetchAll(
                "SELECT *
				FROM modules
				WHERE parent_module_id = ?
				ORDER BY item_order",
                $parent_module_id
            );
        }
        $sidebar_links = [];
        foreach ($modules as $module) {
            // Skip the modules that the user doesn't have permission to
            if (
                !is_null($module->max_permission_level) &&
                $user->type()->permission_level > $module->max_permission_level
            ) {
                continue;
            }
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
                "label" => "Sign Out",
                "link" => "/sign-out",
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
            } elseif ($page > $this->total_pages) {
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
            $conditions = array_map(
                fn($column) => "($column LIKE ?)",
                $this->search_columns
            );
            $this->addHaving(
                implode(" OR ", $conditions),
                ...array_fill(0, count($this->search_columns), "%$search_term%")
            );
        }
    }

    /**
     * Handle the filter links, add where clause
     */
    private function handleFilterLinks(): void
    {
        if (count($this->filter_links) === 0) {
            return;
        }
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
        if (trim($term) !== "") {
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
     * Get the table query
     * This is the query for module.index
     * @param bool $limit_query there exists a limit, offset clause
     */
    private function getTableQuery(bool $limit_query = true): string
    {
        $columns = $this->table_columns
            ? implode(", ", $this->table_columns)
            : "*";
        $where = $this->table_where
            ? "WHERE " . $this->formatAnd($this->table_where)
            : "";
        $group_by = $this->table_group_by
            ? "GROUP BY " . $this->table_group_by
            : "";
        $having = $this->table_having
            ? "HAVING " . $this->formatAnd($this->table_having)
            : "";
        $order_by = $this->table_order_by
            ? "ORDER BY " . $this->table_order_by . " " . $this->table_sort
            : "";
        $page = max(($this->page - 1) * $this->per_page, 0);
        $limit = $limit_query ? "LIMIT " . $page . ", " . $this->per_page : "";
        return sprintf(
            "SELECT %s FROM %s %s %s %s %s %s",
            ...[
                $columns,
                $this->sql_table,
                $where,
                $group_by,
                $having,
                $order_by,
                $limit,
            ]
        );
    }

    /**
     * Get the edit query
     * This is the query for module.edit
     */
    private function getEditQuery(): string
    {
        $columns = $this->form_columns
            ? implode(", ", $this->form_columns)
            : "*";
        $where = $this->table_where
            ? "WHERE " . $this->formatAnd($this->table_where)
            : "";
        return sprintf(
            "SELECT %s FROM %s %s",
            ...[$columns, $this->sql_table, $where]
        );
    }

    /**
     * Get the update query
     * This is the query for module.update
     */
    private function getUpdateQuery(array $request): string
    {
        $map = array_map(fn($column) => "$column = ?", array_keys($request));
        $set_stmt = "SET " . $this->formatComma($map);
        return sprintf(
            "UPDATE %s %s WHERE %s = ?",
            ...[$this->sql_table, $set_stmt, $this->primary_key]
        );
    }

    /**
     * Get the update query
     * This is the query for module.update
     */
    private function getDeleteQuery(): string
    {
        return sprintf(
            "DELETE FROM %s WHERE %s = ?",
            ...[$this->sql_table, $this->primary_key]
        );
    }

    /**
     * Get the create query
     * This is the query for module.update
     */
    private function getCreateQuery(array $request): string
    {
        $map = array_map(fn($column) => "$column = ?", array_keys($request));
        $set_stmt = "SET " . $this->formatComma($map);
        return sprintf("INSERT INTO %s %s", ...[$this->sql_table, $set_stmt]);
    }

    /**
     * Add a table where clause
     * Aliases cannot be used here
     */
    protected function addWhere(
        string $clause,
        int|string ...$replacements
    ): void {
        $this->table_where[] = [$clause, [...$replacements]];
    }

    /**
     * Add a table having clause
     * Aliases can be used here
     */
    protected function addHaving(
        string $clause,
        int|string ...$replacements
    ): void {
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

    protected function editValueOverride(object &$row): void
    {
    }

    private function stripAlias(string $column): mixed
    {
        $arr = explode(" as ", $column);
        return end($arr);
    }
    /**
     * @return array<<missing>,mixed>
     */
    private function normalizeColumns(): array
    {
        $columns = [];
        foreach ($this->table_columns as $title => $column) {
            $columns[$title] = $this->stripAlias($column);
        }
        return $columns;
    }

    private function getColumnTitle(string $column): int|string|bool
    {
        // This is annoying, but we must deal with aliases here
        $column = $this->stripAlias($column);
        return array_search($column, $this->normalizeColumns());
    }

    protected function hasRowEdit(object $row): bool
    {
        return $this->edit;
    }

    protected function hasRowDelete(object $row): bool
    {
        return $this->delete;
    }

    protected function control(string $column, mixed $value)
    {
        // Deal with form control
        if (isset($this->form_control[$column])) {
            $control = $this->form_control[$column];
            // Using a formatting callback
            if (is_callable($control)) {
                return $control($column, $value);
            }
            // Using a pre-defined control method (ie, controlName)
            $method_name = "control$control";
            if (method_exists($this, $method_name)) {
                return $this->$method_name($column, $value);
            }
        }
        return template("control/input.php", [
            "column" => $column,
            "value" => $value,
            "title" => $this->getColumnTitle($column),
        ]);
    }

    /**
     * Template formatting function
     */
    protected function format(string $column, mixed $value): mixed
    {
        if (is_null($value)) {
            return "";
        }

        // Deal with table formatting
        if (isset($this->table_format[$column])) {
            $format = $this->table_format[$column];
            // Using a formatting callback
            if (is_callable($format)) {
                return $format($column, $value);
            }
            // Using a pre-defined format method (ie, formatName)
            $method_name = "format$format";
            if (method_exists($this, $method_name)) {
                return $this->$method_name($column, $value);
            }
        }
        return template("format/span.php", [
            "column" => $column,
            "value" => $value,
            "title" => $this->getColumnTitle($column),
        ]);
    }

    /**
     * Format IP value
     */
    protected function formatIP(string $column, mixed $value): mixed
    {
        $value = long2ip(intval($value));
        return template("format/span.php", [
            "column" => $column,
            "value" => $value,
            "title" => "IP",
        ]);
    }

    /**
     * Format human readable timestamp as ago diff
     */
    protected function formatAgo(string $column, mixed $value): string
    {
        $carbon = Carbon::parse($value)->diffForHumans();
        return template("format/span.php", [
            "column" => $column,
            "value" => $carbon,
            "title" => $value,
        ]);
    }

    /**
     * Print a nice error to logs
     * @param array<int,mixed> $params
     */
    private function pdoException(
        string $sql,
        array $params,
        Exception $ex
    ): void {
        $out = print_r(
            [
                "sql" => $sql,
                "params" => $params,
                "message" => $ex->getMessage(),
                "file" => $ex->getFile() . ":" . $ex->getLine(),
            ],
            true
        );
        error_log($out);
    }

    /**
     * Get the total results count, without a limit or offset
     */
    private function getTotalCount(): int
    {
        if (!$this->sql_table || !$this->table_columns) {
            return 0;
        }
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
     * Update a record
     * @param string $id record ID
     * @param array $request validated request
     */
    public function processUpdate(string $id, array $request): mixed
    {
        if (!$this->sql_table || !$this->form_columns) {
            return [];
        }
        $request = array_filter(
            $request,
            fn($key) => in_array($key, $this->form_columns),
            ARRAY_FILTER_USE_KEY
        );
        $sql = $this->getUpdateQuery($request);
        // Empty string is null
        $mapped = array_map(fn($r) => trim($r) === "" ? null : $r, $request);
        $params = [...array_values($mapped), $id];
        try {
            $result = db()->query($sql, ...$params);
            return $result;
        } catch (Exception $ex) {
            $this->pdoException($sql, $params, $ex);
            return null;
        }
    }

    /**
     * Create a new record
     * @param array $request validated request
     */
    public function processCreate(array $request): mixed
    {
        if (!$this->sql_table || !$this->form_columns) {
            return [];
        }
        $request = array_filter(
            $request,
            fn($key) => in_array($key, $this->form_columns),
            ARRAY_FILTER_USE_KEY
        );
        $sql = $this->getCreateQuery($request);
        // Empty string is null
        $mapped = array_map(fn($r) => trim($r) === "" ? null : $r, $request);
        $params = array_values($mapped);
        try {
            $result = db()->query($sql, ...$params);
            return $result ? db()->lastInsertId() : null;
        } catch (Exception $ex) {
            $this->pdoException($sql, $params, $ex);
            return null;
        }
    }

    /**
     * Delete a new record
     * @param array $request validated request
     */
    public function processDestroy(string $id): mixed
    {
        if (!$this->sql_table || !$this->form_columns) {
            return [];
        }
        $sql = $this->getDeleteQuery();
        $params = [$id];
        try {
            $result = db()->query($sql, ...$params);
            return $result;
        } catch (Exception $ex) {
            $this->pdoException($sql, $params, $ex);
            return null;
        }
    }

    /**
     * Run the table query and return the dataset
     * This dataset is for module.index
     */
    protected function getIndexData(): array|bool
    {
        if (!$this->sql_table || !$this->table_columns) {
            return [];
        }
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

    protected function getEditData(string $id): ?array
    {
        if (!$this->sql_table || !$this->form_columns) {
            return [];
        }
        $this->addWhere("{$this->primary_key} = ?", $id);
        $sql = $this->getEditQuery();
        $params = $this->getParams($this->table_where);
        try {
            $stmt = db()->run($sql, $params);
            $result = $stmt->fetch();
            $map = array_map(
                function ($title, $column, $value) {
                    $row = (object) [
                        "title" => $title,
                        "column" => $column,
                        "value" => $value,
                    ];
                    $this->editValueOverride($row);
                    return $row;
                },
                array_keys($this->form_columns),
                array_values($this->form_columns),
                (array) $result
            );
            return $map;
        } catch (Exception $ex) {
            $this->pdoException($sql, $params, $ex);
            return null;
        }
    }

    protected function getCreateData(): ?array
    {
        if (!$this->sql_table || !$this->form_columns) {
            return [];
        }
        $sql = $this->getEditQuery();
        $params = $this->getParams($this->table_where);
        try {
            $stmt = db()->run($sql, $params);
            $result = $stmt->fetch();
            $map = array_map(
                function ($title, $column, $value) {
                    $row = (object) [
                        "title" => $title,
                        "column" => $column,
                        "value" => $value,
                    ];
                    $this->editValueOverride($row);
                    return $row;
                },
                array_keys($this->form_columns),
                array_values($this->form_columns),
                (array) $result
            );
            return $map;
        } catch (Exception $ex) {
            $this->pdoException($sql, $params, $ex);
            return null;
        }
    }

    /**
     * Render the view module.index
     */
    public function viewIndex(): string
    {
        $this->filters();
        $path = $this->path;
        $format = function (string $column, mixed $value) {
            return $this->format($column, $value);
        };
        $has_row_edit = function (object $row) {
            return $this->hasRowEdit($row);
        };
        $has_row_delete = function (object $row) {
            return $this->hasRowDelete($row);
        };
        return template("module/index/index.php", [
            "module" => $path,
            "messages" => template("components/flash.php", [
                "flash" => Flash::get(),
            ]),
            "actions" => [
                "show_create_action" => $this->create,
            ],
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
                "module" => $path,
                "primary_key" => $this->primary_key,
                "headers" => array_keys($this->table_columns),
                "data" => $this->getIndexData(),
                "show_row_actions" => $this->show_row_actions,
                "has_row_edit" => $has_row_edit,
                "has_row_delete" => $has_row_delete,
                "format" => $format,
            ]),
            "pagination" => template("module/index/pagination.php", [
                "show" =>
                    $this->per_page > $this->total_results ||
                    $this->total_pages > 1,
                "current_page" => $this->page,
                "total_results" => $this->total_results,
                "total_pages" => $this->total_pages,
                "per_page" => $this->per_page,
                "per_page_options" => array_filter(
                    $this->per_page_options,
                    fn($value) => $value <= $this->total_results
                ),
                "side_links" => $this->side_links,
            ]),
        ]);
    }

    /**
     * Render the view module.edit
     * @param string $id record ID
     */
    public function viewEdit(string $id): string
    {
        $path = $this->path;
        $request_errors = fn(
            string $field
        ) => $this->controller->getRequestError($field);
        $has_errors = fn(string $field) => $this->controller->hasRequestError(
            $field
        );
        $control = function (string $column, mixed $value) {
            return $this->control($column, $value);
        };
        return template("module/edit/index.php", [
            "id" => $id,
            "messages" => template("components/flash.php", [
                "flash" => Flash::get(),
            ]),
            "form" => template("module/edit/form.php", [
                "control" => $control,
                "data" => $this->getEditData($id),
                "module" => $path,
                "request_errors" => $request_errors,
                "has_errors" => $has_errors,
            ]),
        ]);
    }

    /**
     * Render the view module.create
     */
    public function viewCreate(): string
    {
        $path = $this->path;
        $request_errors = fn(
            string $field
        ) => $this->controller->getRequestError($field);
        $has_errors = fn(string $field) => $this->controller->hasRequestError(
            $field
        );
        $control = function (string $column, mixed $value) {
            return $this->control($column, $value);
        };
        return template("module/create/index.php", [
            "messages" => template("components/flash.php", [
                "flash" => Flash::get(),
            ]),
            "form" => template("module/create/form.php", [
                "control" => $control,
                "data" => $this->getCreateData(),
                "module" => $path,
                "request_errors" => $request_errors,
                "has_errors" => $has_errors,
            ]),
        ]);
    }
}
