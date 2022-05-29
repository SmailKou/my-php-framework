<?php

declare(strict_types=1);

use Framework\Simplex;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

class IndexTest extends TestCase
{
    protected Simplex $framework;

    protected function setUp(): void
    {
        $routes = require __DIR__ . '/../src/routes.php';

        $urlMatcher = new \Symfony\Component\Routing\Matcher\UrlMatcher($routes, new \Symfony\Component\Routing\RequestContext());

        $controllerResolver = new \Symfony\Component\HttpKernel\Controller\ControllerResolver();
        $argumentResolver = new \Symfony\Component\HttpKernel\Controller\ArgumentResolver();
        $dispatcher = new EventDispatcher();

        $this->framework = new Simplex($urlMatcher, $controllerResolver, $argumentResolver, $dispatcher);
    }

    public function testHello()
    {
        $request = \Symfony\Component\HttpFoundation\Request::create('/hello/Smail');

        $response = $this->framework->handle($request);

        $this->assertEquals('Hello Smail', $response->getContent());
    }

    public function testBye()
    {
        $request = \Symfony\Component\HttpFoundation\Request::create('/bye');

        $response = $this->framework->handle($request);

        $this->assertEquals('<h1>GoodBye !</h1>', $response->getContent());
    }
}