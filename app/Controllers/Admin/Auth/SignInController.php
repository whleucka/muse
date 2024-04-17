<?php

namespace App\Controllers\Admin\Auth;

use Nebula\Framework\Auth\Auth;
use Nebula\Framework\Controller\Controller;
use StellarRouter\{Get, Post};

class SignInController extends Controller
{
    protected function bootstrap(): void
    {
        if (user()) {
            Auth::redirectProfile();
        }
    }

    /**
     * @param array<int,mixed> $data sign-in form data
     */
    private function form(array $data = []): string
    {
        return $this->render("auth/form/sign-in.php", $data);
    }

    #[Get("/sign-in", "sign-in.index", ["Hx-Push-Url=/sign-in"])]
    public function index(): string
    {
        $content = template("auth/sign-in.php", ["form" => $this->form()]);

        return $this->render("layout/base.php", ["main" => $content]);
    }

    #[Post("/sign-in", "sign-in.post")]
    public function post(): string
    {
        $data = $this->validateRequest([
            "remember_me" => [],
            "email" => ["required", "email"],
            "password" => ["required"],
        ]);
        if ($data) {
            $user = Auth::userAuth($data);
            if ($user) {
                Auth::signIn($user, intval($data["remember_me"]) === 1);
            } else {
                $this->request_errors["password"][] =
                    "bad email and/or password";
            }
        }
        return $this->form([
            "email" => $this->request("email"),
        ]);
    }
}
