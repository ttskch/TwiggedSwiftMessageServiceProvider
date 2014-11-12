<?php
namespace Qck\Silex\Service\ImageEmbedder;

use Qck\Silex\Exception\RuntimeException;

class Embedder
{
    private $placeholderPattern = ';qck_silex_twig_message_extension;%s;';

    public function getPlaceholder($imagePath)
    {
        if (!is_string($imagePath)) {
            throw new RuntimeException('image path is must be passed as a string.');
        }

        return new Placeholder(sprintf($this->placeholderPattern, $imagePath), $imagePath);
    }

    public function extractPlaceholders($body)
    {
        $placeholders = array();

        $pattern = sprintf($this->placeholderPattern, '([^;]*)');
        preg_match_all("/{$pattern}/", $body, $matches);

        for ($i = 0; isset($matches[0][$i]); $i++) {
            $placeholder = $matches[0][$i];
            $filePath = $matches[1][$i];
            $placeholders[] = new Placeholder($placeholder, $filePath);
        }

        return $placeholders;
    }

    /**
     * @return string
     */
    public function getPlaceholderPattern()
    {
        return $this->placeholderPattern;
    }
}
