<?php


namespace Deployee\Components\Dependency;


use Deployee\Components\Container\Container;
use Deployee\Components\Dependency\Test\AwesomeTestDependencyClass;
use Deployee\Components\Dependency\Test\GreatTestDependantClass;
use Deployee\Components\Dependency\Test\MegaTestDependantClass;
use Deployee\Components\Dependency\Test\SuperTestDependencyClass;
use PHPUnit\Framework\TestCase;

class ContainerResolverTest extends TestCase
{
    public function testCreateInstance()
    {
        $dependencies = [
            SuperTestDependencyClass::class => new SuperTestDependencyClass(),
            AwesomeTestDependencyClass::class => new AwesomeTestDependencyClass()
        ];
        $container = new Container($dependencies);
        $resolver = new ContainerResolver($container);

        /* @var GreatTestDependantClass $object */
        $object = $resolver->createInstance(GreatTestDependantClass::class);

        $this->assertInstanceOf(GreatTestDependantClass::class, $object);
        $this->assertSame($dependencies[SuperTestDependencyClass::class], $object->getSuperTest());
        $this->assertSame($dependencies[AwesomeTestDependencyClass::class], $object->getAwesomeTest());
    }

    public function testCreateInstanceWithConstructorArguments()
    {
        $awesomeTestDep = new AwesomeTestDependencyClass();
        $dependencies = [
            SuperTestDependencyClass::class => new SuperTestDependencyClass()
        ];

        $container = new Container($dependencies);
        $resolver = new ContainerResolver($container);


        /* @var GreatTestDependantClass $object */
        $object = $resolver->createInstance(GreatTestDependantClass::class, [$awesomeTestDep]);
        $this->assertInstanceOf(GreatTestDependantClass::class, $object);
        $this->assertSame($dependencies[SuperTestDependencyClass::class], $object->getSuperTest());
        $this->assertSame($awesomeTestDep, $object->getAwesomeTest());
    }

    public function testCreateInstanceWithNoConstructorAndContainerSetter()
    {
        $container = new Container();
        $resolver = new ContainerResolver($container);

        /* @var MegaTestDependantClass $object */
        $object = $resolver->createInstance(MegaTestDependantClass::class);
        $this->assertSame($container, $object->getContainer());
        $this->assertSame(MegaTestDependantClass::INT_VALUE, $object->getIntValue());
        $this->assertNull($object->getAwesome());
    }

    public function testCreateInstanceWithNonExistantClass()
    {
        $resolver = new ContainerResolver(new Container());
        $this->expectException(\ReflectionException::class);
        $resolver->createInstance('Nobody\Uses\That\Namespace\With\SomeClassThatDoesNotExist');
    }
}