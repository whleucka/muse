<?php

namespace App\Controllers\Admin\Auth;

use Nebula\Framework\Auth\Auth;
use Nebula\Framework\Controller\Controller;
use StellarRouter\Get;

class SignOutController extends Controller
{
    #[Get("/sign-out", "sign-out.index", ["Hx-Push-Url=/sign-in"])]
    public function index(): void
    {
        Auth::signOut();
    }
}
