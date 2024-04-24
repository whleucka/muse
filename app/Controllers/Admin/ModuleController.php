<?php

namespace App\Controllers\Admin;

use Nebula\Framework\Admin\Module;
use Nebula\Framework\Controller\Controller;
use StellarRouter\{Delete, Get, Group, Patch, Post};
use Symfony\Component\HttpFoundation\Response;
use Exception;
use Nebula\Framework\Auth\Auth;

#[Group(prefix: "/admin", middleware: ["auth"])]
class ModuleController extends Controller
{
	private Module $module;

	protected function bootstrap(): void
	{
		$user = user();
		$module = $this->init();
		if (is_null($module)) {
			$this->moduleNotFound();
		}
		if (!is_null($module->max_permission_level) && $user->type()->permission_level > $module->max_permission_level) {
			$this->permissionDenied();
		}
		$class = $module->class_name;
		$this->module = new $class($module);
	}

	private function init(): ?object
	{
		$route = $this->request()->get("route");
		// If the route is admin, redirect
		if ($route->getPath() === "/admin/") {
			$this->admin();
		}

		$parameters = $route->getParameters() ?? [];
		if (key_exists("path", $parameters)) {
			$module_path = $parameters["path"];
			$module = db()->fetch("SELECT * FROM modules WHERE path = ?", $module_path);
			if ($module) {
				return $module;
			}
		}
		return null;
	}

	private function moduleNotFound(): void
	{
		$response = new Response("Module not found", 404);
		$response->send();
		die;
	}

	private function permissionDenied(): void
	{
		$response = new Response("Permission denied", 403);
		$response->send();
		die;
	}

	#[Get("/", "module.admin")]
	public function admin(): void
	{
		Auth::redirectProfile();
	}

	#[Get("/{path}", "module.index")]
	public function index($path): string
	{
		$data = $this->validateRequest([
			"page" => ["min|0"],
			"per_page" => ["min|0"],
			"term" => ["non_empty_string"],
			"filter_link" => ["min|0"],
		]);
		if ($data) {
			$this->module->processRequest($data);
		}
		return $this->render("layout/admin.php", [
			"module_title" => $this->module->getTitle(),
			"sidebar" => $this->module->getSidebar(),
			"content" => $this->module->viewIndex()
		]);
	}


	#[Get("/{path}/link-count", "module.link-count")]
	public function link_count($path): string
	{
		$data = $this->validateRequest([
			"index" => [],
		]);
		if ($data) {
			try {
				$count = $this->module->getFilterLinkCount($data["index"]);
				return $count > 1000 ? "1000+" : $count;
			} catch (Exception $ex) {
				return "??";
			}
		}
		return 0;
	}

	#[Get("/{path}/create", "module.create")]
	public function create($path)
	{
		if (!$this->module->hasCreatePermission()) $this->permissionDenied();
		dump("create");
	}

	#[Get("/{path}/{id}/edit", "module.edit")]
	public function edit($path, $id)
	{
		if (!$this->module->hasEditPermission()) $this->permissionDenied();
		dump("edit");
	}

	#[Get("/{path}/{id}", "module.show")]
	public function show($path, $id)
	{
		dump("show");
	}

	#[Post("/{path}", "module.store")]
	public function store($path)
	{
		if (!$this->module->hasCreatePermission()) $this->permissionDenied();
		dump("store");
	}

	#[Patch("/{path}/{id}", "module.update")]
	public function update($path, $id)
	{
		if (!$this->module->hasEditPermission()) $this->permissionDenied();
		dump("update");
	}

	#[Delete("/{path}/{id}", "module.destroy")]
	public function destroy($path, $id)
	{
		if (!$this->module->hasDeletePermission()) $this->permissionDenied();
		dump("destroy");
	}
}
