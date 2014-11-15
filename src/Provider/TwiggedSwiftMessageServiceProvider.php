<?php

namespace Qck\Silex\Provider;

use Qck\Silex\Service\FormHandler;
use Qck\TwiggedSwiftMessageBuilder\TwiggedSwiftMessageBuilder;
use Silex\Application;
use Silex\Provider\SwiftmailerServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\ServiceProviderInterface;

class TwiggedSwiftMessageServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Application $app)
    {
        if (!isset($app['twig'])) {
            $app->register(new TwigServiceProvider());
        }
        if (!isset($app['mailer'])) {
            $app->register(new SwiftmailerServiceProvider());
        }

        // service creators.
        $app['twigged_message'] = $app->share(function ($app) {
            return new TwiggedSwiftMessageBuilder($app['twig']);
        });
        $app['twigged_message.form_handler'] = $app->share(function ($app) {
            return new FormHandler();
        });
    }

    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
    }
}
