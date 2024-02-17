<?php

namespace App\Controllers\Auth;

use Nebula\Framework\Auth\Auth;
use Nebula\Framework\Controller\Controller;
use StellarRouter\Get;

class SignOutController extends Controller
{
	#[Get("/sign-out", "sign-out.index")]
	public function index(): void
	{
		Auth::signOut();
	}
}

