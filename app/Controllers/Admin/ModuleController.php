<?php

namespace App\Controllers\Admin;

use Nebula\Framework\Admin\Module;
use Nebula\Framework\Alerts\Flash;
use Nebula\Framework\Controller\Controller;
use StellarRouter\{Delete, Get, Group, Patch, Post};
use Symfony\Component\HttpFoundation\Response;
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
        if (
            !is_null($module->max_permission_level) &&
            $user->type()->permission_level > $module->max_permission_level
        ) {
            $this->permissionDenied();
        }
        $class = $module->class_name;
        $this->module = new $class($module, $this);
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
            $module = db()->fetch(
                "SELECT * FROM modules WHERE path = ?",
                $module_path
            );
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
        die();
    }

    private function permissionDenied(): void
    {
        $response = new Response("Permission denied", 403);
        $response->send();
        die();
    }

    #[Get("/", "module.admin")]
    public function admin(): void
    {
        Auth::redirectProfile();
    }

    #[Get("/{path}", "module.index")]
    public function index($path): string
    {
        header("Hx-Push-Url: /admin/$path");

        $data = $this->validateRequest([
            "page" => ["min|0"],
            "per_page" => ["min|0"],
            "term" => ["non_empty_string"],
            "filter_link" => ["min|0"],
            "filter_count" => ["min|0"],
        ]);
        if ($data) {
            $response = $this->module->processRequest($data);
            if (!is_null($response)) {
                return $response;
            }
        } else {
            Flash::add("warning", "Validation failed");
        }

        return $this->module->render("index");
    }

    #[Get("/{path}/create", "module.create")]
    public function create($path): string
    {
        header("Hx-Push-Url: /admin/$path/create");

        if (!$this->module->hasCreatePermission()) {
            $this->permissionDenied();
        }

        return $this->module->render("create");
    }

    #[Get("/{path}/{id}", "module.edit")]
    public function edit($path, $id): string
    {
        header("Hx-Push-Url: /admin/$path/$id");

        if (!$this->module->hasEditPermission()) {
            $this->permissionDenied();
        }

        return $this->module->render("edit", $id);
    }

    #[Post("/{path}/create", "module.store")]
    public function store($path): string
    {
        if (!$this->module->hasCreatePermission()) {
            $this->permissionDenied();
        }
        $data = $this->validateRequest($this->module->getValidationRules());
        if ($data) {
            $id = $this->module->processCreate($data);
            if ($id) {
                Flash::add("success", "Record successfully created");
                return $this->edit($path, $id);
            } else {
                Flash::add("warning", "Create failed");
            }
        } else {
            Flash::add("warning", "Validation failed");
        }
        return $this->create($path);
    }

    #[Patch("/{path}/{id}", "module.update")]
    public function update($path, $id): string
    {
        if (!$this->module->hasEditPermission()) {
            $this->permissionDenied();
        }
        $data = $this->validateRequest($this->module->getValidationRules());
        if ($data) {
            $result = $this->module->processUpdate($id, $data);
            if ($result) {
                Flash::add("success", "Record successfully updated");
            } else {
                Flash::add("warning", "Update failed");
            }
        } else {
            Flash::add("warning", "Validation failed");
        }
        return $this->edit($path, $id);
    }

    #[Delete("/{path}/{id}", "module.destroy")]
    public function destroy($path, $id): string
    {
        if (!$this->module->hasDeletePermission()) {
            $this->permissionDenied();
        }
        $result = $this->module->processDestroy($id);
        if ($result) {
            Flash::add("success", "Record successfully deleted");
        } else {
            Flash::add("warning", "Delete failed");
        }
        return $this->index($path);
    }
}
