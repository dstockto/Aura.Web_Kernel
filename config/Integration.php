<?php
namespace Aura\Web_Kernel\_Config;

use Aura\Di\Config;
use Aura\Di\Container;

class Integration extends Config
{
    public function define(Container $di)
    {
        $di->params['Aura\Web_Kernel\WebKernel']['responder'] = $di->lazyNew(
            'Aura\Web_Kernel\IntegrationResponder'
        );
    }

    public function modify(Container $di)
    {
        $logger = $di->get('logger');
        $request = $di->get('web_request');
        $response = $di->get('web_response');
        $router = $di->get('web_router');

        $logger->pushHandler($di->newInstance('Monolog\Handler\NullHandler'));
         
        $router->add(null, '/aura/web-kernel/integration/hello')
            ->addValues(array(
                'controller' => function () use ($request, $response) {
                    $response->headers->set('X-Hello', 'World');
                    $response->cookies->set('hello', 'world');
                    $response->content->set('Hello World!');
                },
            ));

        $router->add(null, '/aura/web-kernel/integration/missing-controller')
            ->addValues(array(
                'controller' => 'no-such-controller',
            ));

        $router->add(null, '/aura/web-kernel/integration/throw-exception')
            ->addValues(array(
                'controller' => function () {
                    throw new \Exception('Mock exception');
                },
            ));
    }
}
