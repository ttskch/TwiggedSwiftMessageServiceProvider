<?php
namespace Qck\Silex\Service\ImageEmbedder;

use Qck\Silex\Exception\RuntimeException;

class EmbedderTest extends \PHPUnit_Framework_TestCase
{
    /** @var Embedder */
    private $embedder;

    protected function setUp()
    {
        $this->embedder = new Embedder();
    }

    public function test_getPlaceholder()
    {
        $placeholder = $this->embedder->getPlaceholder('/path/to/image');
        $this->assertInstanceOf('\Qck\Silex\Service\ImageEmbedder\Placeholder', $placeholder);
    }

    public function test_getPlaceholder_error_for_non_string()
    {
        $this->setExpectedException(get_class(new RuntimeException()));
        $this->embedder->getPlaceholder(new \stdClass());
        $this->embedder->getPlaceholder(1);
    }

    public function test_extractPlaceholders()
    {
        $placeholderString = sprintf($this->embedder->getPlaceholderPattern(), '/path/to/image');
        $body = $placeholderString . $placeholderString;

        $placeholders = $this->embedder->extractPlaceholders($body);

        $this->assertCount(2, $placeholders);
        $this->assertInstanceOf('\Qck\Silex\Service\ImageEmbedder\Placeholder', $placeholders[0]);
    }
}
