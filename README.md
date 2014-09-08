# TwigMailerServiceProvider

[![Build Status](https://travis-ci.org/qckanemoto/TwigMailerServiceProvider.svg?branch=master)](https://travis-ci.org/qckanemoto/TwigMailerServiceProvider)
[![Latest Stable Version](https://poser.pugx.org/qckanemoto/twigmailer-service-provider/v/stable.svg)](https://packagist.org/packages/qckanemoto/twigmailer-service-provider)
[![Total Downloads](https://poser.pugx.org/qckanemoto/twigmailer-service-provider/downloads.svg)](https://packagist.org/packages/qckanemoto/twigmailer-service-provider)

This is a service provider for [Silex](http://silex.sensiolabs.org/) which allows you following things:

 * to create Twig templated Swift_Message
 * to use submitted form data in Twig template

## Getting started

You **must** register both `TwigServiceProvider` and `SwiftmailerServicePorvider` on ahead.

```php
$app->register(new TwigServiceProvider());
$app->register(new SwiftmailerServiceProvider());
$app->register(new Quartet\Silex\Provider\TwigMailerServiceProvider());
```

Then you can send Twig templated email as below:

```twig
{# email.txt.twig #}

{% block from %}no-reply@example.com{% endblock %}
{% block from_name %}[Example]{% endblock %}
{% block subject %}Welcome to [Example]!{% endblock %}

{% block body %}
Hello [Example] World!
{% endblock %}
```

```php
// in your controller.

$message = $app['twig_mailer']->buildMessage('email.txt.twig');
$app['twig_meiler']->send($message);
```

In Twig tempalte you can define many things by using `{% block [field-name] %}{% endblock %}`.
These fields can be defined.

 * from
 * from_name
 * to
 * cc
 * bcc
 * reply_to
 * subject
 * body


## Use submitted form data in Twig template

You also can use submitted form data in Twig template easily.
Labels and values of each fields can be used as below:

 * `{{ vars.[field-name].label }}`
 * `{{ vars.[field-name].value }}`

For example:

```twig
{% block from %}{{ vars.email.value }}{% endblock %}
{% block from_name %}{{ vars.name.value }}{% endblock %}
{% block to %}contact@example.com{% endblock %}

{% block subject %}[Contact] {{ vars.summary.value }}{% endblock %}

{% block body %}
{% for var in vars %}
----------------------------------------------------------------------
{{ var.label }}：{{ var.value }}
{% endfor %}
----------------------------------------------------------------------
{% endblock %}
```

```php
// in your controller.

$form->handleRequest($request);
if ($form->isValid()) {
    $message = $app['twig_mailer']->buildMessage('email.txt.twig', array(), $form);
    $app['twig_mailer']->send($message);
}
```

Offcourse you also can access to your custom field type as below:

 * `{{ vars.[parent-field].[child-field].label }}`
 * `{{ vars.[parent-field].[child-field].value }}`

## Options for debugging

Using not so much `$app['mailer']` but `$app['twig_mailer']` allows you a debugging feature.

You can change destination of all of emails sent with `$app['twig_mailer']->send()` to your own email address coercively with following settings.

```php
$app['twig_mailer.options'] = array(
    'debug' => true,
    'debug_email_destination' => 'your_email_address_here',
);
```
