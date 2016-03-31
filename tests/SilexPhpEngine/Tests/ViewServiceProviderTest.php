<?php
namespace SilexPhpEngine\Tests;

use Silex\Application;
use SilexPhpEngine\ViewServiceProvider;
use Symfony\Component\HttpFoundation\Request;

class TestHelper extends \Symfony\Component\Templating\Helper\Helper
{
    public function getName()
    {
        return 'test';
    }
}

class TemplateServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testRegister()
    {
        $app = new Application;
        $app->register(new ViewServiceProvider, array(
            'view.class_path' => __DIR__.'/../../../vendor'
        ));

        $app->get('/', function() use($app) {
            return $app['view']->render(__DIR__.'/../../view.phtml', array(
                'name' => 'Foo'
            ));
        });

        $request = Request::create('/');
        $response = $app->handle($request);

        $this->assertInstanceOf('Symfony\Component\Templating\PhpEngine', $app['view']);
        $this->assertTrue($app['view']->has('slots'));

        $this->assertEquals($response->getContent(), 'Hello, Foo!');
    }

    public function testCustomHelpers()
    {
        $options = array(
            'view.class_path' => __DIR__.'/../../../vendor',
            'view.helpers' => array(
                new TestHelper()
            )
        );

        $app = new Application;
        $app->register(new ViewServiceProvider, $options);

        $app['view'];
        $this->assertTrue($app['view']->has('test'));
        $this->assertTrue($app['view']->has('slots'));
    }
}
