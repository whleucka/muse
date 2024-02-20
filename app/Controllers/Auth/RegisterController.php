<?php

namespace App\Controllers\Auth;

use Nebula\Framework\Auth\Auth;
use Nebula\Framework\Controller\Controller;
use StellarRouter\{Get, Post};

class RegisterController extends Controller
{
	protected function bootstrap(): void
	{
		if (Auth::user()) {
			Auth::redirectHome();
		}
	}

    /**
     * @param array<int,mixed> $data sign-in form data
     */
    private function form(array $data = []): string
	{
		return $this->render("auth/form/register.php", $data);
	}

	#[Get("/register", "sign-in.index")]
	public function index(): string
	{
		return $this->render("auth/register.php", [
			"form" => $this->form()
		]);
	}

	#[Post("/register", "sign-in.post")]
	public function post(): string
	{
		// Override the default request error messages
		$this->error_messages["unique"] = "address already in use";
		$this->error_messages["minlength"] = "must be at least 10 characters long";
		$data = $this->validateRequest([
			"email" => ["required", "email", "unique|users"],
			"name" => ["required"],
			"password" => ["required", "minlength|8", "symbol"],
			"password_match" => ["required", "match|password"]
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
