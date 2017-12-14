<?php

/**
 * Icinga2 Class is the main API interface for the icinga library. At the moment only reads from the server is
 * possible.
 *
 * Created by PhpStorm.
 * User: poa32kc
 * Date: 14.12.17
 * Time: 11:40
 */

namespace De\Uniwue\RZ\Api\Icinga2;

use De\Uniwue\RZ\Api\Exception\InvalidConfigurationException;

class Icinga2
{
    /**
     * Placeholder for the logger
     *
     * @var MonoLog/LoggerInterface
     */
    private $logger;

    /**
     * Placeholder for the array
     *
     * @var array
     */
    private $config;

    /**
     * Icinga2 constructor.
     * @param array $config The configuration for the given Icinga2
     * @param MonoLog/LoggerInterface $logger The logger for the operation
     */
    public function __construct($config, $logger = null)
    {
        $this->logger = $logger;
        $this->config = $config;
    }

    /**
     * @param string $level The log level for the given log
     * @param $message $message    The message that should be logged
     * @param array $context The context of the given log
     */
    public function log($level, $message, $context = array())
    {
        if ($this->logger !== null) {
            $this->logger->log($level, $message, $context);
        }
    }

    /**
     * Checks if the configuration is valid
     *
     * @throws InvalidConfigurationException
     */
    public function checkConfig()
    {
        if (isset($this->config["host"]) === false) {
            throw new InvalidConfigurationException("Host is not set in configuration");
        }
        if (isset($this->config["port"]) === false) {
            throw new InvalidConfigurationException("Port is not set in configuration");
        }
    }

    /**
     * Checks if the given server is reachable
     *
     * @throws ServerNotReachableException
     */
    public function serverReachable()
    {

    }

    /**
     * Checks if the given server can be authenticated
     *
     *
     * @throws ServerNotAuthenticableException
     */
    public function serverAuthenticable(){

    }

    /**
     * Returns the list of all Hosts
     *
     * @return array
     */
    public function getAllHosts(){

    }

    /**
     * Returns the list of all Services
     *
     * @return array
     */
    public function getAllServices(){

    }

    /**
     * Returns the list of hosts that match the given filter array
     *
     * @param array $fitler
     *
     * @return array
     */
    public function getHosts($fitler = array()){

    }

    /**
     * Returns the list of services that match the given filter array
     *
     * @param array $filter
     *
     * @return array
     */
    public function getServices($filter = array()){

    }
}