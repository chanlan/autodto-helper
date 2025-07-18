<?php

namespace AutoDTO\Helper\Http\Middleware;

use AutoDTO\Helper\Model\DTO\DTO;
use Closure;
use Illuminate\Http\Request;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Symfony\Component\HttpFoundation\Response;

/**
 * 自动将请求数据绑定到控制器方法的DTO参数
 */
class AutoDTOBinderMiddleware
{
    /**
     * 处理传入请求
     *
     * @throws ReflectionException
     */
    public function handle(Request $request, Closure $next): Response
    {
        $controller = $this->resolveController($request);
        if ($controller) {
            $this->bindDtoToControllerMethods($controller, $request);
        }

        return $next($request);
    }

    /**
     * 从请求中解析控制器实例
     */
    private function resolveController(Request $request): ?object
    {
        return $request->route()?->controller;
    }

    /**
     * 将DTO绑定到控制器方法
     *
     * @throws ReflectionException
     */
    private function bindDtoToControllerMethods(object $controller, Request $request): void
    {
        $controllerReflection = new ReflectionClass($controller);
        $action = $request->route()?->getActionMethod();
        foreach ($this->getPublicMethods($controllerReflection) as $method) {
            if ($method->getName() == $action) {
                $this->processMethodParameters($method, $controller, $request);
            }
        }
    }

    /**
     * 获取控制器的公共方法
     */
    private function getPublicMethods(ReflectionClass $reflection): array
    {
        return $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
    }

    /**
     * 处理方法参数并绑定DTO
     *
     * @throws ReflectionException
     */
    private function processMethodParameters(
        ReflectionMethod $method,
        object $controller,
        Request $request
    ): void {
        foreach ($method->getParameters() as $parameter) {
            if ($this->isDtoParameter($parameter)) {
                $instance = $this->createDtoFromRequest(
                    $parameter->getType()->getName(),
                    $request
                );
                app()->instance($parameter->getType()->getName(), $instance);
                $method->invoke($controller, $instance);
            }
        }
    }

    /**
     * 判断参数是否为DTO类型
     */
    private function isDtoParameter(\ReflectionParameter $parameter): bool
    {
        $type = $parameter->getType();
        return $type && is_a($type->getName(), DTO::class, true);
    }

    /**
     * 根据请求数据创建DTO实例
     *
     * @throws ReflectionException
     */
    private function createDtoFromRequest(string $clazz, Request $request): object
    {
        $reflection = new ReflectionClass($clazz);
        $constructorArgs = [];

        foreach ($reflection->getProperties() as $property) {
            $constructorArgs[$property->getName()] = $request->input($property->getName());
        }

        return $reflection->newInstanceArgs($constructorArgs);
    }
}
