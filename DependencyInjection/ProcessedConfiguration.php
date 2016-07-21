<?php

namespace Becklyn\BugsnagBundle\DependencyInjection;

/**
 *
 */
class ProcessedConfiguration
{
    /**
     * @var array
     */
    private $configuration = [];



    /**
     * @param array $configuration
     */
    public function setConfiguration (array $configuration)
    {
        $this->configuration = $configuration;
    }



    /**
     * @return string|null
     */
    public function getApiKey ()
    {
        return $this->getValue("api_key");
    }



    /**
     * @param $key
     *
     * @return mixed|null
     */
    private function getValue ($key)
    {
        return isset($this->configuration[$key])
            ? $this->configuration[$key]
            : null;
    }
}
