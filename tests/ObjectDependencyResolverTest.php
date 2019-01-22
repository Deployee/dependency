<?php


namespace Deployee\Components\Dependency;


use Deployee\Components\Dependency\Test\AwesomeTestDependencyClass;
use Deployee\Components\Dependency\Test\GreatTestDependantClass;
use Deployee\Components\Dependency\Test\MegaTestDependantClass;
use Deployee\Components\Dependency\Test\SuperTestDependencyClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ObjectDependencyResolverTest extends TestCase
{
    public function testCreateInstance()
    {
        $container = new ContainerBuilder();
        $container->register(SuperTestDependencyClass::class, SuperTestDependencyClass::class);
        $container->register(AwesomeTestDependencyClass::class, AwesomeTestDependencyClass::class);

        $resolver = new ObjectDependencyResolver($container);

        /* @var GreatTestDependantClass $object */
        $object = $resolver->createInstance(GreatTestDependantClass::class);

        $this->assertInstanceOf(GreatTestDependantClass::class, $object);
        $this->assertSame($container->get(SuperTestDependencyClass::class), $object->getSuperTest());
        $this->assertSame($container->get(AwesomeTestDependencyClass::class), $object->getAwesomeTest());
    }

    public function testCreateInstanceWithConstructorArguments()
    {
        $awesomeTestDep = new AwesomeTestDependencyClass();

        $container = new ContainerBuilder();
        $container->register(SuperTestDependencyClass::class, SuperTestDependencyClass::class);
        $resolver = new ObjectDependencyResolver($container);


        /* @var GreatTestDependantClass $object */
        $object = $resolver->createInstance(GreatTestDependantClass::class, [$awesomeTestDep]);
        $this->assertInstanceOf(GreatTestDependantClass::class, $object);
        $this->assertSame($container->get(SuperTestDependencyClass::class), $object->getSuperTest());
        $this->assertSame($awesomeTestDep, $object->getAwesomeTest());
    }

    public function testCreateInstanceWithNoConstructorAndContainerSetter()
    {
        $container = new ContainerBuilder();
        $resolver = new ObjectDependencyResolver($container);

        /* @var MegaTestDependantClass $object */
        $object = $resolver->createInstance(MegaTestDependantClass::class);
        $this->assertSame($container, $object->getContainer());
        $this->assertSame(MegaTestDependantClass::INT_VALUE, $object->getIntValue());
        $this->assertNull($object->getAwesome());
    }

    public function testCreateInstanceWithNonExistantClass()
    {
        $resolver = new ObjectDependencyResolver(new ContainerBuilder());
        $this->expectException(\ReflectionException::class);
        $resolver->createInstance('Nobody\Uses\That\Namespace\With\SomeClassThatDoesNotExist');
    }

    public function testAutowireObjectFail()
    {
        $container = new ContainerBuilder();
        $resolver = new ObjectDependencyResolver($container);
        $this->expectException(\InvalidArgumentException::class);
        $resolver->autowireObject('ThisIsNotAnObject');
    }

    public function testInvokeMethod()
    {
        $resolver = new ObjectDependencyResolver(new ContainerBuilder());
        $superTestDep = new SuperTestDependencyClass();

        $this->assertSame(ContainerBuilder::class, $resolver->invokeMethod($superTestDep, 'returnClassName'));
    }
}