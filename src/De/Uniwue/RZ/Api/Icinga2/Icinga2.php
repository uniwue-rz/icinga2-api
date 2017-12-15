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
use De\Uniwue\RZ\Api\Exception\ServerNotReachableException;
use De\Uniwue\RZ\Api\Exception\SeverNotAccessibleException;
use Httpful\Request;
use Httpful\Response;

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
     *
     * @throws InvalidConfigurationException
     */
    public function __construct($config, $logger = null)
    {
        $this->logger = $logger;
        $this->config = $config;
        $this->checkConfig();
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
     * @return bool
     *
     * @throws ServerNotReachableException
     */
    public function serverReachable()
    {
        if ($socket = @fsockopen($this->config["host"], $this->config["port"], $errno, $errstr, 30)) {
            fclose($socket);
            return true;
        } else {
            throw new ServerNotReachableException("The Icinga API host " . $this->config["host"] . ":" . $this->config["port"] . " is not available");
        }
    }

    /**
     * Checks if the server can be authenticated.
     *
     * @return bool
     *
     * @throws SeverNotAccessibleException
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function serverAuthenticable()
    {
        $response = $this->sendQuery(true, "v1");
        if ($response->code !== 200) {
            throw new SeverNotAccessibleException("Your authentication is not valid for login or you don't have permissions");
        }
        return true;
    }

    /**
     * Sends the query to the server and return the response
     *
     * @param bool $withHttps If the query sent with https
     * @param string $path The path that should get the query
     * @param array $params The parameters that should be sent to the server
     *
     * @return Response
     *
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function sendQuery($withHttps = true, $path = "", $params = array())
    {
        if (isset($this->config["user"]) === true && isset($this->config["password"]) === true) {
            $this->log("info", "logging in using username and password auth with path '$path'");
            $response = Request::get($this->createUri($withHttps, $path))
                ->authenticateWithBasic($this->config["user"], $this->config["password"])
                ->followRedirects()
                ->send();
            return $response;
        } elseif (isset($this->config["cert"]) === true && isset($this->config["key"]) === true) {
            $this->log("info", "logging in using certificate and keys with path '$path'");
            $response = Request::get($this->createUri($withHttps, $path))
                ->authenticateWithCert($this->config["cert"], $this->config["key"])
                ->followRedirects()
                ->send();
            return $response;
        }

        return null;
    }

    /**
     * Returns the available
     *
     * @param bool $withHttps
     *
     * @return array
     *
     * @throws SeverNotAccessibleException
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function getPermissions($withHttps = true)
    {
        $result = array();
        $authenticable = $this->serverAuthenticable();
        if ($authenticable === true) {
            $response = $this->sendQuery($withHttps, "v1");
            $content = $response->body;
            if ($content !== null) {
                $dom = new \DOMDocument();
                $dom->loadHTML($content);
                $permissionNodes = $dom->getElementsByTagName('li');
                if ($permissionNodes->length > 0) {
                    for ($i = 0; $i < $permissionNodes->length; $i++) {
                        array_push($result, $permissionNodes->item($i)->nodeValue);
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Creates a uri from the given host and port
     *
     * @param bool $withHttps If the uri should get https scheme
     * @param string $path The path that should be used to create the uri
     *
     * @return string
     */
    public function createUri($withHttps = true, $path = "")
    {
        $uri = $this->config["host"] . ":" . $this->config["port"] . "/" . $path;
        if ($withHttps === true) {
            return "https://" . $uri;
        }
        return "http://" . $uri;
    }


    /**
     * Returns all the existing hosts
     *
     * @param bool $withHttps
     *
     * @return array
     *
     * @throws SeverNotAccessibleException
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function getAllHosts($withHttps = true)
    {
        $authenticable = $this->serverAuthenticable();
        if($authenticable == true){
            $response = $this->sendQuery($withHttps, "v1/objects/hosts");
            if($response->code === 200){
                $decodedResponse = json_decode($response,true);
                return $decodedResponse;
            }
        }

        return array();
    }

    /**
     * Returns the list of all Services
     *
     * @param bool $withHttps
     *
     * @return array
     *
     * @throws SeverNotAccessibleException
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function getAllServices($withHttps = true)
    {
        $authenticable = $this->serverAuthenticable();
        if($authenticable == true){
            $response = $this->sendQuery($withHttps, "v1/objects/services");
            if($response->code === 200){
                $decodedResponse = json_decode($response,true);
                return $decodedResponse;
            }
        }

        return array();
    }

    /**
     * Returns the list of hosts that match the given filter array.
     * The filters here will be converted to
     *
     * @param array $filter
     *
     * @return array
     */
    public function getHosts($filter = array())
    {
        $filterJson = json_encode($filter);

    }

    /**
     * Returns the list of services that match the given filter array
     *
     * @param array $filter
     *
     * @return array
     */
    public function getServices($filter = array())
    {

    }
}