<?php

namespace Quartet\Silex\Provider;

use Quartet\Silex\Service\TwigMailerService;
use Silex\Application;
use Silex\ServiceProviderInterface;

class TwigMailerServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Application $app)
    {
        $app['twig_mailer.options'] = array(
            'debug' => false,
            'debug_email_destination' => '',
        );

        $app['twig_mailer'] = $app->share(function ($app) {
            if (!isset($app['twig']) || !isset($app['mailer'])) {
                throw new \LogicException('twig and mailer services are must registered on ahead.');
            }
            return new TwigMailerService($app['twig'], $app['mailer'], $app['twig_mailer.options']);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
    }
}
