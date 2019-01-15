<?php


namespace Deployee\Components\Dependency;

use Deployee\Components\Container\ContainerException;
use Deployee\Components\Container\ContainerInterface;

class ContainerResolver
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $class
     * @param array $arguments
     * @return object
     * @throws \ReflectionException
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
     * @param \ReflectionMethod $method
     * @return array
     */
    private function getMethodInstanceArgs(\ReflectionMethod $method): array
    {
        $parameterList = [];
        foreach($method->getParameters() as $parameter){
            $type = (string)$parameter->getType();
            if(($value = $this->getContainerValue($type)) === null || (!class_exists($type) && !interface_exists($type))){
                return [];
            }

            $parameterList[] = $value;
        }

        return $parameterList;
    }

    /**
     * @param string $id
     * @return mixed
     */
    private function getContainerValue(string $id)
    {
        try{
            return $this->container->get($id);
        }
        catch(ContainerException $e){
            return $id === ContainerInterface::class ? $this->container : null;
        }
    }
}