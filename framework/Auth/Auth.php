<?php

namespace Nebula\Framework\Auth;

class Auth
{
	public static function signOut(): void
	{
		session()->destroy();
		header("Location: /auth/sign-in");
		exit;
	}
}
