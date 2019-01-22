<?php


namespace Deployee\Components\Dependency;

use Symfony\Component\DependencyInjection\ContainerBuilder;

class ObjectDependencyResolver
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * @param ContainerBuilder $container
     */
    public function __construct(ContainerBuilder $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $class
     * @param array $arguments
     * @return object
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function createInstance(string $class, array $arguments = [])
    {
        $refl = new \ReflectionClass($class);
        $instanceArgs = count($arguments) === 0
            && $refl->getConstructor()
            && $refl->getConstructor()->isPublic()
            && $refl->getConstructor()->getNumberOfParameters() > 0
            ? $this->getMethodInstanceArgs($refl->getConstructor())
            : $arguments;

        $object = $refl->newInstanceArgs($instanceArgs);


        return $this->autowireObject($object);
    }

    /**
     * @param object $object
     * @return mixed
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function autowireObject($object)
    {
        if(!is_object($object)){
            throw new \InvalidArgumentException('Argument is not an object');
        }

        $refl = new \ReflectionClass($object);
        foreach($refl->getMethods(\ReflectionMethod::IS_PUBLIC) as $method){
            if($method->isConstructor()
                || $method->isDestructor()
                || !$method->isPublic()
                || $method->isStatic()
                || $method->getNumberOfParameters() !== 1
                || strpos($method->getName(), 'set') !== 0){
                continue;
            }

            $parameter = $this->getMethodInstanceArgs($method);
            if(count($parameter) > 0){
                $method->invoke($object, ...$parameter);
            }
        }

        return $object;
    }

    /**
     * @param object $object
     * @param string $method
     * @return mixed
     * @throws \ReflectionException
     */
    public function invokeMethod($object, string $method)
    {
        $refl = new \ReflectionMethod(get_class($object), $method);
        return call_user_func_array([$object, $method], $this->getMethodInstanceArgs($refl));
    }

    /**
     * @param \ReflectionMethod $method
     * @return array
     * @throws \Exception
     */
    private function getMethodInstanceArgs(\ReflectionMethod $method): array
    {
        $parameterList = [];
        foreach($method->getParameters() as $parameter){
            $type = (string)$parameter->getType();
            if($type !== ContainerBuilder::class && $this->container->has($type) === false){
                return [];
            }

            $parameterList[] = $type === ContainerBuilder::class
                ? $this->container
                : $this->container->get($type);
        }

        return $parameterList;
    }
}