<?php
namespace Quartet\Silex\Service;

use Quartet\Silex\Exception\RuntimeException;
use Quartet\Silex\Service\ImageEmbedder\Embedder;

class TwigMessageServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @var TwigMessageService */
    protected $service;

    protected function setUp()
    {
        $this->service = new TwigMessageService(new \Twig_Environment(), new Embedder());
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
}
