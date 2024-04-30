<?php

namespace Nebula\Framework\System;

use Composer\ClassMapGenerator\ClassMapGenerator;
use Dotenv\Dotenv;
use Exception;
use StellarRouter\Router;

class Kernel
{
    /**
     * Get framework router class and register controllers
     */
    public function router(): Router
    {
        $router = new Router();
        $controller_path = config("path.controllers");
        $this->registerControllers(
            $router,
            $this->controllerMap($controller_path)
        );
        return $router;
    }

    /**
     * Register controller routes
     * @param Router $router
     * @param array $map controller class map
     */
    protected function registerControllers(Router $router, array $map): void
    {
        foreach ($map as $controller => $path) {
            $router->registerClass($controller);
        }
    }

    /**
     * Get a controller class map
     * @param string $controller_path application controller path
     * @return array<class-string,non-empty-string>
     */
    protected function controllerMap(string $controller_path): array
    {
        if (!file_exists($controller_path)) {
            throw new Exception("controller path doesn't exist");
        }
        return ClassMapGenerator::createMap($controller_path);
    }

    /**
     * Load environment variables
     * @param string $path path to .env
     */
    protected function environment(): void
    {
        $path = config("path.root");
        if (!file_exists($path)) {
            error_log("warning: your .env path: '$path' doesn't exist");
        }
        $dotenv = Dotenv::createImmutable($path);
        $dotenv->safeLoad();
    }
}
