<?php

namespace Nebula\Framework\Auth;

class Auth
{
	public static function redirectSignIn(): void
	{
		header("Location: /sign-in");
		exit;
	}

	public static function signOut(): void
	{
		session()->destroy();
		self::redirectSignIn();
	}
}
