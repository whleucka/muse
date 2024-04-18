<?php

namespace App\Controllers\Admin;

use Composer\ClassMapGenerator\ClassMapGenerator;
use Nebula\Framework\Admin\Module;
use Nebula\Framework\Controller\Controller;
use StellarRouter\{Delete, Get, Group, Patch, Post};
use Symfony\Component\HttpFoundation\Response;

#[Group(prefix: "/admin", middleware: ["auth"])]
class ModuleController extends Controller
{
	private Module $module;

	protected function bootstrap(): void
	{
		$module = $this->init();
		if (is_null($module)) {
			$this->moduleNotFound();
		}
		$this->module = $module;
	}

	private function init(): ?Module
	{
		$route = $this->request()->get("route");
		$parameters = $route->getParameters() ?? [];
		if (key_exists("path", $parameters)) {
			$module_path = $parameters["path"];
			$path = config("path.modules");
			$modules = ClassMapGenerator::createMap($path);
			foreach ($modules as $class => $filepath) {
				$module = new $class;
				if ($module->getPath() === $module_path) {
					return $module;
				}
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

	#[Get("/{path}", "module.index")]
	public function index($path): string
	{
		$data = $this->validateRequest([
			"page" => [],
			"term" => [],
			"filter_link" => [],
		]);
		// TODO find out why you cannot use rules here
		if ($data) {
			$this->module->processRequest($data);
		}
		return $this->render("layout/admin.php", [
			"module_title" => $this->module->getTitle(),
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
			return $this->module->getFilterLinkCount($data["index"]);
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
