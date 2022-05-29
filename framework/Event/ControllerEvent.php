<?php

declare(strict_types=1);

namespace Framework\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

class ControllerEvent extends Event
{
    protected Request $request;
    protected $controller;

    public function __construct(Request $request, $controller)
    {
        $this->request = $request;
        $this->controller = $controller;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getController()
    {
        return $this->controller;
    }
}