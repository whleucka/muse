<?php

namespace Nebula\Framework\Http;

use Error;
use Exception;
use Nebula\Framework\Middleware\Middleware;
use Nebula\Framework\System\Interface\Kernel as NebulaInterface;
use Nebula\Framework\System\Kernel as SystemKernel;
use Nebula\Framework\Traits\Singleton;
use StellarRouter\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Kernel extends SystemKernel implements NebulaInterface
{
    use Singleton;

    protected array $middleware = [];

    /**
     * Main kernel method
     */
    public function main(): void
    {
        $this->bootstrap();
    }

    public function response(): void
    {
        $request = $this->request();
        $route = $this->routing($request);
        $request->attributes->add(["route" => $route]);
        $response = $this->middleware()
            ->layer($this->middleware)
            ->handle($request, function () use ($request, $route) {
                return $this->resolve($request, $route);
            });
        $response->prepare($request);
        $response->send();
    }

    /**
     * Initializes the framework, sets up essential configurations,
     * and prepares the environment for the application to run.
     */
    protected function bootstrap(): void
    {
        $this->environment();
        $this->registerMiddleware();
    }

    /**
     * Handle URL routing, mapping incoming requests to the appropriate
     * controllers or actions within the application.
     * @param Request $request
     */
    protected function routing(Request $request): ?Route
    {
        $router = $this->router();
        return $router->handleRequest(
            $request->getMethod(),
            $request->getPathInfo()
        );
    }

    /**
     * Resolve a controller endpoint
     * @param Request $request
     * @param Route $route
     */
    protected function resolve(Request $request, ?Route $route): mixed
    {
        if ($route) {
            try {
                $content = null;
                $headers = [];
                $handlerClass = $route->getHandlerClass();
                $handlerMethod = $route->getHandlerMethod();
                $routeParameters = $route->getParameters();
                $routeMiddleware = $route->getMiddleware();
                $routePayload = $route->getPayload();
                if ($handlerClass) {
                    $class = new $handlerClass($request);
                    $content = $class->$handlerMethod(...$routeParameters);
                } elseif ($routePayload) {
                    $content = $routePayload(...$routeParameters);
                }
                if (in_array("api", $routeMiddleware)) {
                    return $content;
                }
                return new Response($content, 200, $headers);
            } catch (Exception $ex) {
                error_log(
                    print_r(
                        [
                            "type" => "Exception",
                            "message" => $ex->getMessage(),
                            "file" => $ex->getFile() . ":" . $ex->getLine(),
                        ],
                        true
                    )
                );
                header("Location: /server-error", response_code: 500);
                exit;;
            } catch (Error $err) {
                error_log(
                    print_r(
                        [
                            "type" => "Error",
                            "message" => $err->getMessage(),
                            "file" => $err->getFile() . ":" . $err->getLine(),
                        ],
                        true
                    )
                );
                header("Location: /server-error", response_code: 500);
                exit;
            }
        } else {
            header("Location: /page-not-found", response_code: 302);
            exit;
        }
    }

    /**
     * Get framework middleware class
     */
    protected function middleware(): Middleware
    {
        return new Middleware();
    }

    /**
     * Register middleware to filter HTTP requests entering the application.
     */
    protected function registerMiddleware(): void
    {
        foreach ($this->middleware as $i => $class) {
            $this->middleware[$i] = new $class();
        }
    }

    /**
     * Handle incoming HTTP requests, parse them, and prepare them
     * for further processing.
     */
    protected function request(): Request
    {
        return Request::createFromGlobals();
    }
}
