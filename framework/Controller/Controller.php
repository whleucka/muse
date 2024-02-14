<?php

namespace Nebula\Framework\Controller;

use Symfony\Component\HttpFoundation\Request;

class Controller
{
	public function __construct(protected Request $request)
	{
	}
}
