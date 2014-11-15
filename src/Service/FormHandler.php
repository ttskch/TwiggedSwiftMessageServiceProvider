<?php
namespace Qck\Silex\Service;

use Symfony\Component\Form\Form;

class FormHandler
{
    public function getDataArray(Form $form)
    {
        $data = array();

        foreach ($form->getIterator() as $child) {
            /** @var \Symfony\Component\Form\Form $child */

            $value = $child->getData();

            // process two-dimensional array.
            if (is_array($value) && array_values($value) !== $value) {
                $data[$child->getName()] = $this->getData($child);
            } else {
                $label = $child->getConfig()->getOption('label');
                $data[$child->getName()] = array(
                    'label' => $label ?: $this->humanize($child->getName()),
                    'value' => $value,
                );
            }
        }

        return $data;
    }

    private function humanize($text)
    {
        return ucfirst(trim(strtolower(preg_replace(array('/([A-Z])/', '/[_\s]+/'), array('_$1', ' '), $text))));
    }
}
