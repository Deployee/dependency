<?php


namespace Deployee\Components\Dependency\Test;


use Deployee\Components\Container\ContainerInterface;

class MegaTestDependantClass
{
    const INT_VALUE = 1337;

    /**
     * @var AwesomeTestDependencyClass
     */
    private $awesome;

    /**
     * @var ContainerInterface
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

    public function setContainer(ContainerInterface $container)
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
     * @return ContainerInterface
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