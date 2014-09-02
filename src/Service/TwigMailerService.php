<?php

namespace Quartet\Silex\Service;

use Silex\Application;
use Symfony\Component\Form\Form;

class TwigMailerService
{
    private $twig;
    private $mailer;
    private $options;

    /**
     * @param \Twig_Environment $twig
     * @param \Swift_Mailer $mailer
     * @param array $options
     */
    public function __construct(\Twig_Environment $twig, \Swift_Mailer $mailer, array $options)
    {
        $this->twig = $twig;
        $this->mailer = $mailer;
        $this->options = $options;
    }

    /**
     * @param string $templatePath
     * @param Form $form
     * @return \Swift_Mime_MimePart
     */
    public function buildMessage($templatePath, Form $form = null)
    {
        /** @var $template \Twig_Template */
        $template = $this->twig->loadTemplate($templatePath);
        $type = preg_match('/\.html(\.twig)?$/', $templatePath) ? 'text/html' : 'text/plain';

        $message = \Swift_Message::newInstance();

        // build vars hashtable from Form.
        $vars = $form ? $this->getVars($form) : array();
        $vars = compact('vars');

        // build message from twig template.
        if ($from = $template->renderBlock('from', $vars)) {
            if ($fromName = $template->renderBlock('from_name', $vars)) {
                $message->setFrom($from, $fromName);
            } else {
                $message->setFrom($from);
            }
        }
        if ($to = $template->renderBlock('to', $vars)) {
            $message->setTo($to);
        }
        if ($cc = $template->renderBlock('cc', $vars)) {
            $message->setCc($cc);
        }
        if ($bcc = $template->renderBlock('bcc', $vars)) {
            $message->setBcc($bcc);
        }
        if ($replyTo = $template->renderBlock('reply_to', $vars)) {
            $message->setReplyTo($replyTo);
        }
        if ($subject = $template->renderBlock('subject', $vars)) {
            $message->setSubject($subject);
        }
        if ($body = $template->renderBlock('body', $vars)) {
            $message->setBody($body, $type);
        }

        return $message;
    }

    /**
     * @param \Swift_Mime_Message $message
     * @param null $failedRecipients
     * @return int
     */
    public function send(\Swift_Mime_Message $message, &$failedRecipients = null)
    {
        if ($this->options['debug'] && $this->options['debug_email_destination']) {
            $message
                ->setTo($this->options['debug_email_destination'])
                ->setCc(array())
                ->setBcc(array())
            ;
        }
        return $this->mailer->send($message, $failedRecipients);
    }

    private function getVars(Form $form)
    {
        $vars = array();

        foreach ($form->getIterator() as $child) {
            /** @var $child \Symfony\Component\Form\Form */
            $value = $child->getData();

            // process hashtable recursively.
            if (is_array($value) && array_values($value) !== $value) {
                $vars[$child->getName()] = $this->getVars($child);
            } else {
                $label = $child->getConfig()->getOption('label');
                $vars[$child->getName()] = array(
                    'label' => $label ?: $this->humanize($child->getName()),
                    'value' => $value,
                );
            }
        }

        return $vars;
    }

    private function humanize($text)
    {
        return ucfirst(trim(strtolower(preg_replace(array('/([A-Z])/', '/[_\s]+/'), array('_$1', ' '), $text))));
    }
}
