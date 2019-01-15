<?php


namespace Deployee\Components\Dependency\Test;


class GreatTestDependantClass
{
    /**
     * @var AwesomeTestDependencyClass
     */
    private $awesomeTest;

    /**
     * @var SuperTestDependencyClass
     */
    private $superTest;

    /**
     * @param AwesomeTestDependencyClass $awesomeTest
     */
    public function __construct(AwesomeTestDependencyClass $awesomeTest)
    {
        $this->awesomeTest = $awesomeTest;
    }

    /**
     * @param SuperTestDependencyClass $superTest
     */
    public function setSuperTest(SuperTestDependencyClass $superTest)
    {
        $this->superTest = $superTest;
    }

    /**
     * @return AwesomeTestDependencyClass
     */
    public function getAwesomeTest(): AwesomeTestDependencyClass
    {
        return $this->awesomeTest;
    }

    /**
     * @return SuperTestDependencyClass
     */
    public function getSuperTest(): SuperTestDependencyClass
    {
        return $this->superTest;
    }
}