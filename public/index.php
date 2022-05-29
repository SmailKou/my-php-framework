<?php

declare(strict_types=1);

use Framework\Event\RequestEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouteCollection;

require __DIR__ . "/../vendor/autoload.php";

$request = Request::createFromGlobals();

$routes = require __DIR__ . '/../src/routes.php';

$context = new \Symfony\Component\Routing\RequestContext();

$urlMatcher = new \Symfony\Component\Routing\Matcher\UrlMatcher($routes, $context);

$controllerResolver = new \Symfony\Component\HttpKernel\Controller\ControllerResolver();
$argumentResolver = new \Symfony\Component\HttpKernel\Controller\ArgumentResolver();

$dispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher();

$dispatcher->addListener('kernel.request', function (RequestEvent $e) {
    $e->getRequest()->attributes->set('prenom', 'Smail');
});

$dispatcher->addListener('kernel.controller', function () {
    dump("Controller");
});

$framework = new Framework\Simplex($urlMatcher, $controllerResolver, $argumentResolver, $dispatcher);

$response = $framework->handle($request);

$response->send();