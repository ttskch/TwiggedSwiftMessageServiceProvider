# TwigMessageServiceProvider

[![Build Status](https://travis-ci.org/qckanemoto/TwigMessageServiceProvider.svg?branch=master)](https://travis-ci.org/qckanemoto/TwigMessageServiceProvider)
[![Latest Stable Version](https://poser.pugx.org/qckanemoto/twig-message-service-provider/v/stable.svg)](https://packagist.org/packages/qckanemoto/twig-message-service-provider)
[![Total Downloads](https://poser.pugx.org/qckanemoto/twig-message-service-provider/downloads.svg)](https://packagist.org/packages/qckanemoto/twig-message-service-provider)

This is a service provider for [Silex](http://silex.sensiolabs.org/) which allows you following things:

 * to create Twig templated Swift_Message
 * to use submitted form data in Twig template easily
 * to create inline styled html email from unstyled html and css strings
 * to embed some image files into message body

## Getting started

First add this dependency into your `composer.json`:

```json
{
    "require": {
        "qckanemoto/twig-message-service-provider": "dev-master"
    }
}
```

And enable this service provider in your application.
Please notice that you must register both `TwigServiceProvider` and `SwiftmailerServiceProvider` on ahead.

```php
$app->register(new TwigServiceProvider());
$app->register(new SwiftmailerServiceProvider());
$app->register(new Qck\Silex\Provider\TwigMessageServiceProvider());
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

$message = $app['twig_message']->buildMessage('email.txt.twig');
$message->setTo('hoge@example.com');
$app['mailer']->send($message);
```

In Twig template you can define many things by using `{% block [field-name] %}{% endblock %}`.
These fields can be defined.

 * from
 * from_name
 * to
 * cc
 * bcc
 * reply_to
 * subject
 * body

## Use variables in Twig template

Offcourse you can pass variables and use them in Twig template with `{{ vars }}` as below:

```twig
{# email.txt.twig #}

{% block subject %}Welcome to {{ vars.site_title }}!{% endblock %}
```

```php
// in your controller.

$message = $app['twig_message']->buildMessage('email.txt.twig', array(
    'site_title' => 'FooBar Service',
));
$message->setTo('hoge@example.com');
$app['mailer']->send($message);
```

## Use submitted form data in Twig template

You also can use submitted form data in Twig template easily.
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
    $message = $app['twig_message']->buildMessage('email.txt.twig', array(), $form);
    $app['mailer']->send($message);
}
```

Offcourse you also can access to your custom field type as below:

 * `{{ vars.[parent-field].[child-field].label }}`
 * `{{ vars.[parent-field].[child-field].value }}`

## Use inline-styled html email

You can make inline-styled html from unstyled html and css strings.
To allow recipients of your html email to receive it with Gmail, you will have to make inline-styled html body.

```php
// in your controller.

$message = $app['twig_message']->buildMessage('email.html.twig');

$style = file_get_contents('/path/to/style.css');

$message = $app['twig_message']->setInlineStyle($message, $style);
```

> **Note**
> This functionality is using `mb_convert_encoding()` with `'auto'` internally. So if you use this you **must** set `mbstring.language` in php.ini or call `mb_language('your_language')` on ahead.
>
> **注意**
> この機能は内部的に `mb_convert_encoding()` に `'auto'` を渡して実行します。なので、php.ini で `mbstring.language` を設定するか、`mb_language('Japanese')` を事前に実行しておく必要があります。

## Embed some image files into message body

You can embed images into message body as below:

```twig
{# email.html.twig #}

{% block body %}
<img src="{{ embed_image(image_path) }}"/>
{% endblock %}
```

```php
// in your controller.

$message = $app['twig_message']->buildMessage('email.html.twig', array(
    'image_path' => '/path/to/image/file',
));

$message = $app['twig_message']->finishEmbedImage($message);
```

## Enjoy!

See also [functional tests](tests/FunctionalTest.php) to understand basic usages.
