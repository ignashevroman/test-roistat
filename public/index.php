<?php

use App\EventListener\ExceptionListener;
use App\EventListener\JsonRequestTransformerListener;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/helpers.php';

$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/../.env');

$locator = new FileLocator(
    [
        __DIR__ . '/../config',
    ]
);
$loader = new YamlFileLoader($locator);

$routes = $loader->load('routes.yaml');
$matcher = new UrlMatcher($routes, new RequestContext());

$dispatcher = new EventDispatcher();
$dispatcher->addSubscriber(new RouterListener($matcher, new RequestStack()));
$dispatcher->addSubscriber(new JsonRequestTransformerListener());
$dispatcher->addSubscriber(new ExceptionListener());

$controllerResolver = new ControllerResolver();
$argumentResolver = new ArgumentResolver();
$kernel = new HttpKernel($dispatcher, $controllerResolver, new RequestStack(), $argumentResolver);

$request = Request::createFromGlobals();

$response = $kernel->handle($request);

$response->send();
$kernel->terminate($request, $response);
