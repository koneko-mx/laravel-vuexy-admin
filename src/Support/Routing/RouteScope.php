<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Routing;

use Closure;
use Illuminate\Support\Facades\Route;
use Koneko\VuexyAdmin\Application\Bootstrap\Registry\KonekoModuleRegistry;

class RouteScope
{
    protected string $uriPrefix;
    protected string $namePrefix;

    public function __construct(string $uriPrefix, string $namePrefix)
    {
        $this->uriPrefix  = trim($uriPrefix, '/');
        $this->namePrefix = rtrim($namePrefix, '.') . '.';
    }

    public static function auto(
        string $filePath,
        string|Closure $prefixOrCallback,
        null|string|Closure $nameOrCallback = null,
        ?Closure $callback = null
    ): void {
        $module = KonekoModuleRegistry::discoverModule(dirname($filePath, 2));

        $routeName = $module->componentNamespace;

        $uriPrefix  = "admin";
        $namePrefix = "admin.{$routeName}.";

        if(is_string($prefixOrCallback)){
            $uriPrefix .= "/{$prefixOrCallback}";
        }

        if(is_string($nameOrCallback)){
            $namePrefix .= rtrim($nameOrCallback, '.') . '.';
        }

        if($prefixOrCallback instanceof Closure) {
            $callback = $prefixOrCallback;
        }

        if($nameOrCallback instanceof Closure){
            $callback = $nameOrCallback;
        }

        $scope = new self($uriPrefix, $namePrefix);

        Route::prefix($scope->uriPrefix)
            ->name($scope->namePrefix)
            ->group(function () use ($scope, $callback) {
                $callback($scope);
            });
    }

    public function route(
        string $prefix,
        string|Closure $nameOrController,
        null|string|Closure $controllerOrCallback = null,
        ?Closure $callback = null
    ): void {
        $uri = trim(trim($prefix, '/'), '/');

        // Caso: route('prefix', fn)
        if (
            $nameOrController instanceof Closure
            && $controllerOrCallback === null
            && $callback === null
        ) {
            Route::prefix($uri)
                ->group($nameOrController);
            return;
        }

        // Caso: route('prefix', Controller::class, fn)
        if (
            $this->isController($nameOrController)
            && $controllerOrCallback instanceof Closure
            && $callback === null
        ) {
            Route::prefix($uri)
                ->controller($nameOrController)
                ->group($controllerOrCallback);
            return;
        }

        // Caso: route('prefix', 'name.', fn)
        if (
            is_string($nameOrController)
            && $controllerOrCallback instanceof Closure
            && $callback === null
        ) {
            $name = rtrim($nameOrController, '.') . '.';

            Route::prefix($uri)
                ->name($name)
                ->group($controllerOrCallback);
            return;
        }

        // Caso: route('prefix', 'name.', Controller::class, fn)
        if (
            is_string($nameOrController)
            && $this->isController($controllerOrCallback)
            && $callback instanceof Closure
        ) {
            $name = rtrim($nameOrController, '.') . '.';

            Route::prefix($uri)
                ->name($name)
                ->controller($controllerOrCallback)
                ->group($callback);
            return;
        }

        // Caso: route(')
        throw new \InvalidArgumentException('Parámetros inválidos para RouteScope::route().');
    }

    // Función que comprueba si el String reresenta un Controller
    static function isController(string $controller): bool
    {
        return is_string($controller)
            && class_exists($controller)
            && str_ends_with($controller, 'Controller');
    }
}
