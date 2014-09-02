<?php

namespace Quartet\Silex\Provider;

class TwigMailerServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TwigMailerServiceProvider
     */
    protected $skeleton;

    protected function setUp()
    {
        $this->skeleton = new TwigMailerServiceProvider;
    }

    public function testNew()
    {
        $actual = $this->skeleton;
        $this->assertInstanceOf('\Quartet\TwigMailerServiceProvider\TwigMailerServiceProvider', $actual);
    }

    /**
     * @expectedException \Quartet\TwigMailerServiceProvider\Exception\LogicException
     */
    public function testException()
    {
        throw new Exception\LogicException;
    }
}
