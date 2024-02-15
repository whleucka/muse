<?php

namespace Nebula\Framework\Controller;

use Symfony\Component\HttpFoundation\Request;

class Controller
{
    public function __construct(protected Request $request)
    {
    }

    public function request(?string $key = null, mixed $default = null): mixed
    {
        if (!$key) {
            return $this->request;
        }
        return $this->request->get($key, $default);
    }
}
