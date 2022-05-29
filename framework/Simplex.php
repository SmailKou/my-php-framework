<?php

declare(strict_types=1);

namespace Framework;

use Exception;
use Framework\Event\ArgumentEvent;
use Framework\Event\ControllerEvent;
use Framework\Event\RequestEvent;
use Framework\Event\ResponseEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Contracts\EventDispatcher\Event;

class Simplex
{
    protected UrlMatcherInterface $urlMatcher;
    protected ControllerResolverInterface $controllerResolver;
    protected ArgumentResolverInterface $argumentResolver;
    protected EventDispatcherInterface $dispatcher;

    public function __construct(UrlMatcherInterface         $urlMatcher,
                                ControllerResolverInterface $controllerResolver,
                                ArgumentResolverInterface   $argumentResolver,
                                EventDispatcherInterface    $dispatcher
    )
    {
        $this->urlMatcher = $urlMatcher;
        $this->controllerResolver = $controllerResolver;
        $this->argumentResolver = $argumentResolver;
        $this->dispatcher = $dispatcher;
    }

    public function handle(Request $request)
    {
        $this->urlMatcher->getContext()->fromRequest($request);

        try {
            $request->attributes->add($this->urlMatcher->match($request->getPathInfo()));

            $this->dispatcher->dispatch(new RequestEvent($request), 'kernel.request');

            $controller = $this->controllerResolver->getController($request);

            $this->dispatcher->dispatch(new ControllerEvent($request, $controller), 'kernel.controller');

            $arguments = $this->argumentResolver->getArguments($request, $controller);

            $this->dispatcher->dispatch(new ArgumentEvent($request, $controller, $arguments), 'kernel.argument');

            $response = call_user_func_array($controller, $arguments);

            $this->dispatcher->dispatch(new ResponseEvent($response), 'kernel.response');
        } catch
        (\Symfony\Component\Routing\Exception\ResourceNotFoundException $exception) {
            $response = new Response("La page demandée n'existe pas", 404);
        } catch (Exception $e) {
            $response = new Response("Une erreur est survenue !", 500);
        }

        return $response;
    }
}