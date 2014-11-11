<?php
namespace Quartet\Silex\Provider;

use Silex\Application;

class TwigMessageServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function test_register()
    {
        $app = new Application();

        $provider = new TwigMessageServiceProvider();
        $provider->register($app);

        // providers are registered.
        $this->assertNotNull($app['twig']);
        $this->assertNotNull($app['mailer']);

        // TwigExtension is added.
        $isExtensionRegistered = false;
        $extensions = $app['twig']->getExtensions();
        foreach (array_values($extensions) as $extension) {
            if (get_class($extension) === 'Quartet\Silex\Twig\Extension\TwigMessageExtension') {}
            $isExtensionRegistered = true;
        }
        $this->assertTrue($isExtensionRegistered);

        // service is registered.
        $this->assertInstanceOf('Quartet\Silex\Service\TwigMessageService', $app['twig_message']);
    }
}
