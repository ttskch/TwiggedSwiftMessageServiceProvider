<?php

namespace Qck\Silex\Provider;

use Qck\Silex\Service\ImageEmbedder\Embedder;
use Qck\Silex\Service\TwigMessageService;
use Qck\Silex\Twig\Extension\TwigMessageExtension;
use Silex\Application;
use Silex\Provider\SwiftmailerServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\ServiceProviderInterface;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

class TwigMessageServiceProvider implements ServiceProviderInterface
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

        // add twig extension.
        $app['twig'] = $app->share($app->extend('twig', function ($twig, $app) {
            $twig->addExtension(new TwigMessageExtension(new Embedder()));
            return $twig;
        }));

        // service creator.
        $app['twig_message'] = $app->share(function ($app) {
            return new TwigMessageService($app['twig'], new Embedder(), new CssToInlineStyles());
        });
    }

    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
    }
}
