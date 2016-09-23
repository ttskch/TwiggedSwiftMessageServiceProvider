<?php
namespace Ttskch\Silex\Service;

use Symfony\Component\Form\Form;

class FormHandler
{
    public function getDataArray(Form $form)
    {
        $data = array();

        foreach ($form->getIterator() as $child) {
            /** @var \Symfony\Component\Form\FormInterface $child */

            $value = $child->getData();

            // process custom field recursively.
            if (is_null($value) || (is_array($value) && array_values($value) !== $value)) {
                $data[$child->getName()] = $this->getDataArray($child);

            } else {
                $label = $child->getConfig()->getOption('label');
                $data[$child->getName()] = array(
                    'label' => $label ?: $this->humanize($child->getName()),
                    'value' => $child->getData(),
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
