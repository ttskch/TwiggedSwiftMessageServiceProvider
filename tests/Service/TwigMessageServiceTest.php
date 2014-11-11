<?php
namespace Quartet\Silex\Service;

use Phake;
use Quartet\Silex\Exception\RuntimeException;
use Quartet\Silex\Service\ImageEmbedder\Placeholder;

class TwigMessageServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @var TwigMessageService */
    protected $service;

    protected function setUp()
    {
        $twig = $this->getMockTwigEnvironment();
        $embedder = $this->getMockEmbedder();

        $this->service = new TwigMessageService($twig, $embedder);
    }

    public function test_buildMessage()
    {
        /** @var \Swift_Message $message */
        $message = $this->service->buildMessage('/path/to/template');

        $this->assertEquals(array('from@test.com' => 'from_name'), $message->getFrom());
        $this->assertEquals(array('to@test.com' => null), $message->getTo());
        $this->assertEquals(array('cc@test.com' => null), $message->getCc());
        $this->assertEquals(array('bcc@test.com' => null), $message->getBcc());
        $this->assertEquals(array('reply_to@test.com' => null), $message->getReplyTo());
        $this->assertEquals('subject', $message->getSubject());
        $this->assertEquals('body', $message->getBody());
    }

    public function test_setInlineStyle()
    {
        $body = '<p>test</p>';
        $style = 'p { color: #fff; }';
        $expectedBody = '<p style="color: #fff;">test</p>';

        $message = new \Swift_Message();

        $message->setBody($body, 'text/html');
        $this->service->setInlineStyle($message, $style);

        $this->assertContains($expectedBody, $message->getBody());
    }

    public function test_setInlineStyle_error_for_plain_text()
    {
        $body = '<p>test</p>';
        $style = 'p { color: #fff; }';

        $message = new \Swift_Message();

        $message->setBody($body);

        $this->setExpectedException(get_class(new RuntimeException()));
        $this->service->setInlineStyle($message, $style);
    }

    public function test_finishEmbedImage()
    {
        // todo: Phake version problem? I don't know why but can't mock 'Swift_Message' class with Phake...
        /*
        $message = Phake::mock('Swift_Message');

        Phake::when($message)->getBody()->thenReturn('placeholder');
        Phake::when($message)->embed(Phake::anyParameters())->thenReturn('replacement');

        $this->service->finishEmbedImage($message);

        Phake::verify($message)->setBody('replacement');
        */

        $twig = $this->getMockBuilder('Twig_Environment')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $embedder = $this->getMock('Quartet\Silex\Service\ImageEmbedder\Embedder');
        $embedder
            ->expects($this->once())
            ->method('extractPlaceholders')
            ->with($this->anything())
            ->will($this->returnValue(array(new Placeholder('placeholder', '/path/to/image'))))
        ;

        $service = new TwigMessageService($twig, $embedder);

        $message = $this->getMockBuilder('Swift_Message')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $message
            ->expects($this->once())
            ->method('getBody')
            ->will($this->returnValue('placeholder'))
        ;
        $message
            ->expects($this->once())
            ->method('embed')
            ->with($this->anything())
            ->will($this->returnValue('replacement'))
        ;
        $message
            ->expects($this->once())
            ->method('setBody')
            ->with($this->equalTo('replacement'))
        ;

        $service->finishEmbedImage($message);
    }

    public function test_renderBody()
    {
        // todo: want to use Phake::mock('Swift_Message')

        $twig = $this->getMockBuilder('Twig_Environment')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $embedder = $this->getMock('Quartet\Silex\Service\ImageEmbedder\Embedder');
        $embedder
            ->expects($this->once())
            ->method('extractPlaceholders')
            ->with($this->anything())
            ->will($this->returnValue(array($placeholder = new Placeholder('placeholder', __DIR__ . '/../templates/images/silex.png'))))
        ;

        $service = new TwigMessageService($twig, $embedder);

        $message = $this->getMockBuilder('Swift_Message')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $message
            ->expects($this->once())
            ->method('getBody')
            ->will($this->returnValue('placeholder'))
        ;

        $body = $service->renderBody($message);

        $base64 = 'data:image/png;base64,' . base64_encode(file_get_contents($placeholder->getImagePath()));
        $this->assertEquals($base64, $body);
    }

    private function getMockTwigEnvironment()
    {
        $template = Phake::mock('\Twig_Template');

        $params = array(
            'vars' => array(),
            'form' => array(),
        );

        Phake::when($template)->renderBlock('from', $params)->thenReturn('from@test.com');
        Phake::when($template)->renderBlock('from_name', $params)->thenReturn('from_name');
        Phake::when($template)->renderBlock('to', $params)->thenReturn('to@test.com');
        Phake::when($template)->renderBlock('cc', $params)->thenReturn('cc@test.com');
        Phake::when($template)->renderBlock('bcc', $params)->thenReturn('bcc@test.com');
        Phake::when($template)->renderBlock('reply_to', $params)->thenReturn('reply_to@test.com');
        Phake::when($template)->renderBlock('subject', $params)->thenReturn('subject');
        Phake::when($template)->renderBlock('body', $params)->thenReturn('body');

        $twig = Phake::mock('Twig_Environment');
        Phake::when($twig)->loadTemplate(Phake::anyParameters())->thenReturn($template);

        return $twig;
    }

    private function getMockEmbedder()
    {
        $placeholders = array(new Placeholder('placeholder', '/path/to/image'));

        $embedder = Phake::mock('Quartet\Silex\Service\ImageEmbedder\Embedder');
        Phake::when($embedder)->extractPlaceholders(Phake::anyParameters())->thenReturn($placeholders);
        Phake::when($embedder)->extractPlaceholders(Phake::anyParameters())->thenReturn($placeholders);

        return $embedder;
    }
}
