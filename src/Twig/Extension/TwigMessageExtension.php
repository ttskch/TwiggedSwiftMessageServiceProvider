<?php
namespace Qck\Silex\Twig\Extension;

use Qck\Silex\Service\ImageEmbedder\Embedder;
use Silex\Application;

class TwigMessageExtension extends \Twig_Extension
{
    private $embedder;

    public function __construct(Embedder $embedder)
    {
        $this->embedder = $embedder;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('embed_image', array($this, 'embedImage')),
        );
    }

    public function embedImage($imagePath)
    {
        return $this->embedder->getPlaceholder($imagePath)->getPlaceholder();
    }

    public function getName()
    {
        return 'qck_silex_twig_message_extension';
    }
}
