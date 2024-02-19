<?php

namespace Nebula\Framework\Auth;

use App\Models\User;

class Auth
{
	public static function user(): User
	{
		$id = session()->get("user_id");
		return new User($id);
	}

	public static function redirectSignIn(): void
	{
		header("Location: /sign-in");
		exit;
	}

	public static function redirectHome(): void
	{
		header("Location: /home");
		exit;
	}

	public static function signIn(User $user): void
	{
		session()->set("user_id", $user->id);
		self::redirectHome();
	}

	public static function signOut(): void
	{
		session()->destroy();
		self::redirectSignIn();
	}

	public static function hashPassword(string $password): string|bool|null
	{
		return password_hash($password, PASSWORD_ARGON2I);
	}

	public static function registerUser(array $data): User
	{
		$data["password"] = self::hashPassword($data["password"]);
		$user = User::new($data);
		return $user;
	}
}
