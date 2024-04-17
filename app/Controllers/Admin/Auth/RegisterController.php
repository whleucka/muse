<?php

namespace App\Controllers\Admin\Auth;

use Nebula\Framework\Auth\Auth;
use Nebula\Framework\Controller\Controller;
use StellarRouter\{Get, Post};

class RegisterController extends Controller
{
    protected function bootstrap(): void
    {
        if (user()) {
            Auth::redirectProfile();
        }
    }

    /**
     * @param array<int,mixed> $data register form data
     */
    private function form(array $data = []): string
    {
        return $this->render("auth/form/register.php", $data);
    }

    #[Get("/register", "register.index", ["Hx-Push-Url=/register"])]
    public function index(): string
    {
        $content = template("auth/register.php", ["form" => $this->form()]);

        return $this->render("layout/base.php", ["main" => $content]);
    }

    #[Post("/register", "register.post")]
    public function post(): string
    {
        // Override the default request error messages
        $this->error_messages["unique"] = "address already in use";
        $this->error_messages["minlength"] =
            "must be at least 10 characters long";
        $data = $this->validateRequest([
            "email" => ["required", "email", "unique|users"],
            "name" => ["required"],
            "password" => ["required", "minlength|8", "symbol"],
            "password_match" => ["required", "match|password"],
        ]);
        if ($data) {
            unset($data["password_match"]);
            $user = Auth::registerUser($data);
            if ($user) {
                Auth::signIn($user);
            }
        }
        return $this->form([
            "email" => $this->request("email"),
            "name" => $this->request("name"),
        ]);
    }
}
