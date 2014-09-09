<?php
namespace Quartet\Silex\Twig\Extension;

use Silex\Application;

class TwigMessageExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('embed_image', array($this, 'embedImage')),
        );
    }

    public function embedImage($imagePath)
    {
        $identifier = $this->getName();

        return "%{$identifier}%{$imagePath}%";
    }

    public function getName()
    {
        return 'quartet_silex_twig_message_extension';
    }
}
