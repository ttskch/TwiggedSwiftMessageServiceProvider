<?php
namespace Quartet\Silex\Provider;

use Quartet\Silex\Service\ImageEmbedder\Embedder;
use Quartet\Silex\Twig\Extension\TwigMessageExtension;
use Silex\Application;

class TwigMessageServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    /** @var TwigMessageExtension */
    private $extension;

    protected function setUp()
    {
        $this->extension = new TwigMessageExtension(new Embedder());
    }

    public function test_register()
    {
        $app = new Application();
        $app->register(new TwigMessageServiceProvider());

        $this->assertInstanceOf('Quartet\Silex\Service\TwigMessageService', $app['twig_message']);
        $this->assertInstanceOf('Twig_Environment', $app['twig']);
        $this->assertInstanceOf('Swift_Mailer', $app['mailer']);

        $extensions = $app['twig']->getExtensions();
        $this->assertContains($this->extension->getName(), array_keys($extensions));

        $this->assertInstanceOf(get_class($this->extension), $extensions[$this->extension->getName()]);
    }
}
