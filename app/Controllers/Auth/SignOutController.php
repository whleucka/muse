<?php

namespace App\Controllers\Auth;

use Nebula\Framework\Auth\Auth;
use Nebula\Framework\Controller\Controller;
use StellarRouter\{Group, Get};

#[Group(prefix: "/auth")]
class SignOutController extends Controller
{
	#[Get("/sign-out", "sign-out.index")]
	public function index(): void
	{
		Auth::signOut();
	}
}

