<?php
namespace Ttskch\Silex\Provider;

use Silex\Application;

class TwiggedSwiftMessageServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function test_register()
    {
        $app = new Application();

        $provider = new TwiggedSwiftMessageServiceProvider();
        $provider->register($app);

        // providers are registered.
        $this->assertNotNull($app['twig']);
        $this->assertNotNull($app['mailer']);

        // services are registered.
        $this->assertInstanceOf('Tch\TwiggedSwiftMessageBuilder\TwiggedSwiftMessageBuilder', $app['twigged_message']);
        $this->assertInstanceOf('Ttskch\Silex\Service\FormHandler', $app['twigged_message.form_handler']);
    }
}
