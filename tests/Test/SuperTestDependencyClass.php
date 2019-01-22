<?php


namespace Deployee\Components\Dependency\Test;


use Symfony\Component\DependencyInjection\ContainerBuilder;

class SuperTestDependencyClass
{
    public function returnClassName(ContainerBuilder $containerBuilder): string
    {
        return get_class($containerBuilder);
    }
}