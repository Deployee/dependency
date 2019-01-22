<?php


namespace Deployee\Components\Dependency\Test;

use Symfony\Component\DependencyInjection\ContainerBuilder;

class MegaTestDependantClass
{
    const INT_VALUE = 1337;

    /**
     * @var AwesomeTestDependencyClass
     */
    private $awesome;

    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * @var int
     */
    private $intValue = self::INT_VALUE;

    public function setSomeWeiredSetterName(AwesomeTestDependencyClass $weiredObject)
    {
        $this->awesome = $weiredObject;
    }

    public function setContainer(ContainerBuilder $container)
    {
        $this->container = $container;
    }

    /**
     * @return AwesomeTestDependencyClass
     */
    public function getAwesome()
    {
        return $this->awesome;
    }

    /**
     * @return ContainerBuilder
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return int
     */
    public function getIntValue(): int
    {
        return $this->intValue;
    }

    /**
     * @param int $intValue
     */
    public function setIntValue(int $intValue)
    {
        $this->intValue = $intValue;
    }
}