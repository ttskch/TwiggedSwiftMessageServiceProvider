<?php
namespace Quartet\Silex;

use Quartet\Silex\Provider\TwigMessageServiceProvider;
use Silex\Application;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\TwigServiceProvider;

class FunctionalTest extends \PHPUnit_Framework_TestCase
{
    protected $app;

    protected function setUp()
    {
        $this->app = new Application();
        $this->app->register(new TwigServiceProvider());
        $this->app->register(new FormServiceProvider());
        $this->app->register(new TwigMessageServiceProvider());
        $this->app['twig.path'] = __DIR__ . '/templates';
    }

    /**
     * @test
     */
    public function create_simple_twig_message()
    {
        /** @var \Swift_Message $message */
        $message = $this->app['twig_message']->buildMessage('simple.txt.twig');

        $this->assertEquals($message->getFrom(), array('from@example.com' => 'from_name'));
        $this->assertEquals($message->getSubject(), 'subject');
        $this->assertEquals($message->getTo(), array('to@example.com' => null));
        $this->assertEquals($message->getCc(), array('cc@example.com' => null));
        $this->assertEquals($message->getBcc(), array('bcc@example.com' => null));
        $this->assertEquals($message->getReplyTo(), array('reply_to@example.com' => null));
        $this->assertEquals($message->getBody(), 'body');
        $this->assertEquals($message->getContentType(), 'text/plain');
    }

    /**
     * @test
     */
    public function create_twig_message_with_extends()
    {
        /** @var \Swift_Message $message */
        $message = $this->app['twig_message']->buildMessage('extended.txt.twig');

        $this->assertEquals($message->getSubject(), 'extended_subject');
    }

    /**
     * @test
     */
    public function create_twig_message_with_vars()
    {
        $vars = array(
            'to' => 'takashi@example.com',
            'name' => 'Takashi',
        );

        /** @var \Swift_Message $message */
        $message = $this->app['twig_message']->buildMessage('vars.txt.twig', $vars);

        $this->assertEquals($message->getTo(), array('takashi@example.com' => null));
        $this->assertEquals($message->getBody(), 'Hi, Takashi.');
    }

    /**
     * @test
     */
    public function create_twig_message_with_form()
    {
        /** @var \Symfony\Component\Form\Form $form */
        $form = $this->app['form.factory']->createBuilder('form')
            ->add('name', 'text', array(
                'label' => 'Name',
                'data' => 'Takashi',
            ))
            ->add('email', 'email', array(
                'label' => 'Email',
                'data' => 'takashi@example.com',
            ))
            ->getForm()
        ;

        /** @var \Swift_Message $message */
        $message = $this->app['twig_message']->buildMessage('form.txt.twig', array(), $form);

        $expectedBody = <<<EOT
Name: Takashi
Email: takashi@example.com

EOT;

        $this->assertEquals($message->getBody(), $expectedBody);
    }

    /**
     * @test
     */
    public function create_html_twig_message()
    {
        /** @var \Swift_Message $message */
        $message = $this->app['twig_message']->buildMessage('simple.html.twig');

        $this->assertEquals($message->getBody(), '<p>test</p>');
        $this->assertEquals($message->getContentType(), 'text/html');
    }

    /**
     * @test
     *
     * You can make inline-styled html from unstyled html and css strings.
     * To allow recipients of your html email to receive it with Gmail, you will have to make inline-styled html body.
     */
    public function create_html_twig_message_with_style_tag()
    {
        /** @var \Swift_Message $message */
        $message = $this->app['twig_message']->buildMessage('simple.html.twig');

        $this->assertEquals($message->getBody(), '<p>test</p>');

        $style = 'p { color: #fff; }';

        $message = $this->app['twig_message']->setInlineStyle($message, $style);

        $this->assertContains('<p style="color: #fff;">test</p>', $message->getBody());
    }

    /**
     * @test
     *
     * You can embed images into message body by following steps.
     *
     * 1. Put `{{ embed_image('/full/path/to/image/file') }}` in your Twig template
     * 2. After do `buildMessage`, execute `$message = finishEmbedImage($message);`
     *
     * Then images are correctly embedded into `$message`.
     */
    public function create_html_twig_message_with_embedded_images()
    {
        /** @var \Swift_Message $message */
        $message = $this->app['twig_message']->buildMessage('embedding.html.twig', array(
            'image_path' => __DIR__ . '/templates/images/silex.png',
        ));

        $message = $this->app['twig_message']->finishEmbedImage($message);

        $this->assertContains('<img src="cid:', $message->getBody());
    }
}
