<?php
namespace Ttskch\Silex\Service;

use Ttskch\Silex\Provider\TwiggedSwiftMessageServiceProvider;
use Silex\Application;
use Silex\Provider\FormServiceProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

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
                'label' => 'NAME',
                'data' => 'Takashi',
            ))
            ->add('email', 'email', array(
                'data' => 'takashi@example.com',
            ))
            ->add('gender', 'choice', array(
                'choices' => array(
                    'male' => 'MALE',
                    'female' => 'FEMALE',
                ),
                'data' => 'male',
            ))
            ->getForm()
        ;

        $array = $this->app['twigged_message.form_handler']->getDataArray($form);

        $expected = array(
            'name' => array(
                'label' => 'NAME',
                'value' => 'Takashi',
            ),
            'email' => array(
                'label' => 'Email', // will be humanized field name automatically.
                'value' => 'takashi@example.com',
            ),
            'gender' => array(
                'label' => 'Gender',
                'value' => 'male',
            ),
        );

        $this->assertEquals($expected, $array);
    }

    public function test_getDataArray_with_custom_type()
    {
        $form = $this->app['form.factory']->createBuilder('form')
            ->add('name', 'text', array(
                'data' => 'Takashi',
            ))
            ->add('email', 'email', array(
                'data' => 'takashi@example.com',
            ))
            ->add('options', new SampleCustomFormType())
            ->getForm()
        ;

        $array = $this->app['twigged_message.form_handler']->getDataArray($form);

        $expected = array(
            'name' => array(
                'label' => 'Name',
                'value' => 'Takashi',
            ),
            'email' => array(
                'label' => 'Email',
                'value' => 'takashi@example.com',
            ),
            'options' => array(
                'age' => array(
                    'label' => 'Age',
                    'value' => '30',
                ),
                'hobby' => array(
                    'label' => 'Hobby',
                    'value' => 'programing',
                ),
            )
        );

        $this->assertEquals($expected, $array);
    }
}

class SampleCustomFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('age', 'integer', array(
                'data' => '30',
            ))
            ->add('hobby', 'text', array(
                'data' => 'programing',
            ))
        ;
    }

    public function getName()
    {
        return 'tch_silex_sample_type';
    }
}
