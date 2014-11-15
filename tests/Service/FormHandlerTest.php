<?php
namespace Qck\Silex\Service;

use Qck\Silex\Provider\TwiggedSwiftMessageServiceProvider;
use Silex\Application;
use Silex\Provider\FormServiceProvider;

class FormHandlerTest extends \PHPUnit_Framework_TestCase
{
    private $app;

    protected function setUp()
    {
        $this->app = new Application();
        $this->app->register(new FormServiceProvider());
        $this->app->register(new TwiggedSwiftMessageServiceProvider());
    }

    public function test_getDataArray()
    {
        $form = $this->app['form.factory']->createBuilder('form')
            ->add('name', 'text', array(
                'label' => 'Name',
                'data' => 'Takashi',
            ))
            ->add('email', 'email', array(
                'data' => 'takashi@example.com',
            ))
            ->getForm()
        ;

        $array = $this->app['twigged_message.form_handler']->getDataArray($form);
        $expected = array(
            'name' => array(
                'label' => 'Name',
                'value' => 'Takashi',
            ),
            'email' => array(
                'label' => 'Email', // will be humanized field name automatically.
                'value' => 'takashi@example.com',
            ),
        );

        $this->assertEquals($expected, $array);
    }
}
