# TwiggedSwiftMessageServiceProvider

[![Build Status](https://travis-ci.org/ttskch/TwiggedSwiftMessageServiceProvider.svg?branch=master)](https://travis-ci.org/ttskch/TwiggedSwiftMessageServiceProvider)
[![Latest Stable Version](https://poser.pugx.org/ttskch/twigged-swiftmessage-service-provider/v/stable.svg)](https://packagist.org/packages/ttskch/twigged-swiftmessage-service-provider)
[![Total Downloads](https://poser.pugx.org/ttskch/twigged-swiftmessage-service-provider/downloads.svg)](https://packagist.org/packages/ttskch/twigged-swiftmessage-service-provider)

This is a service provider of [TwiggedSwiftMessageBuilder](https://github.com/ttskch/TwiggedSwiftMessageBuilder) for [Silex](http://silex.sensiolabs.org/).

## Requirements

* PHP 5.3+

## Getting started

First add this dependency into your `composer.json`:

```json
{
    "require": {
        "ttskch/twigged-swiftmessage-service-provider": "1.0.*@dev"
    },
    "minimum-stability": "dev"
}
```

And enable this service provider in your application.
Please notice that you must register both `TwigServiceProvider` and `SwiftmailerServiceProvider` on ahead.

```php
$app->register(new TwigServiceProvider());
$app->register(new SwiftmailerServiceProvider());
$app->register(new \Ttskch\Silex\Provider\TwiggedSwiftMessageServiceProvider());
```

Then you can build `Swift_Message` object via twig template.

```php
$message = $app['twigged_message']->buildMessage('email.txt.twig');
$message->setTo('hoge@example.com');
$app['mailer']->send($message);
```

See more detailed documentation [here](https://github.com/ttskch/TwiggedSwiftMessageBuilder/blob/master/README.md).

## Use submitted form data in Twig template

This service provider provides one additional feature to use submitted form data in Twig template easily.
`$app['twigged_swiftmessage.form_handler']` allows you to extract data array from `Form` object.

Labels and values of each fields can be used as below:

 * `{{ form.[field-name].label }}`
 * `{{ form.[field-name].value }}`

For example:

```twig
{# email.txt.twig #}

{% block from %}{{ form.email.value }}{% endblock %}
{% block from_name %}{{ form.name.value }}{% endblock %}
{% block to %}contact@example.com{% endblock %}

{% block subject %}[Contact] {{ form.summary.value }}{% endblock %}

{% block body %}
{% for item in form %}
----------------------------------------------------------------------
{{ item.label }}: {{ item.value }}
{% endfor %}
----------------------------------------------------------------------
{% endblock %}
```

```php
// in your controller.

$form->handleRequest($request);
if ($form->isValid()) {
    $array = $app['twigged_message.form_handler']->getDataArray($form);
    $message = $app['twigged_message']->buildMessage('email.txt.twig', array('form' => $array));
    $app['mailer']->send($message);
}
```

Offcourse you also can access to your custom field type as below:

 * `{{ form.[parent-field].[child-field].label }}`
 * `{{ form.[parent-field].[child-field].value }}`

## Enjoy!

See more detailed documentation [here](https://github.com/ttskch/TwiggedSwiftMessageBuilder/blob/master/README.md).
