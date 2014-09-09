<?php

namespace Quartet\Silex\Provider;

use Quartet\Silex\Service\TwigMessageService;
use Quartet\Silex\Twig\Extension\TwigMessageExtension;
use Silex\Application;
use Silex\Provider\SwiftmailerServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\ServiceProviderInterface;

class TwigMessageServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Application $app)
    {
        $app['twig_message'] = $app->share(function ($app) {
            if (!isset($app['twig'])) {
                $app->register(new TwigServiceProvider());
            }
            if (!isset($app['mailer'])) {
                $app->register(new SwiftmailerServiceProvider());
            }

            $app['twig'] = $app->share($app->extend('twig', function ($twig, $app) {
                $twig->addExtension(new TwigMessageExtension());
                return $twig;
            }));

            return new TwigMessageService($app['twig']);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
    }
}
