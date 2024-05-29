<?php

namespace App\Controllers\Errors;

use Nebula\Framework\Controller\Controller;
use StellarRouter\{Get, Post};

class ErrorController extends Controller
{
    #[
        Get("/page-not-found", "errors.page-not-found", [
            "Hx-Push-Url=/page-not-found",
        ])
    ]
    public function page_not_found(): string
    {
        $content = template("errors/page_not_found.php");

        return $this->render("layout/base.php", ["main" => $content]);
    }

    #[
        Get("/permission-denied", "errors.permission-denied", [
            "Hx-Push-Url=/permission-denied",
        ])
    ]
    public function permission_denied(): string
    {
        $content = template("errors/permission_denied.php");

        return $this->render("layout/base.php", ["main" => $content]);
    }

    #[
        Get("/server-error", "errors.server-error", [
            "Hx-Push-Url=/server-error",
        ])
    ]
    public function sever_error(): string
    {
        $content = template("errors/server_error.php");

        return $this->render("layout/base.php", ["main" => $content]);
    }
}
