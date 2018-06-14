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
use De\Uniwue\RZ\Api\Icinga2\Auth\CertificateAuth;
use De\Uniwue\RZ\Api\Icinga2\Auth\PasswordAuth;
use De\Uniwue\RZ\Api\Icinga2\Icinga2Object\Comment;
use De\Uniwue\RZ\Api\Icinga2\Icinga2Object\Host;
use De\Uniwue\RZ\Api\Icinga2\Icinga2Object\Service;
use De\Uniwue\RZ\Api\Icinga2\Query\Query;

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
        $query = new Query($this->config["host"], $this->config["port"], "v1");
        $request = $this->authenticate($query->getRequest());
        $response = $request->send();
        if ($response->code !== 200) {
            throw new SeverNotAccessibleException("Your authentication is not valid for login or you don't have permissions");
        }
        return true;
    }

    /**
     * Authenticates the given request
     *
     * @param Request $request
     *
     * @return Request
     */
    public function authenticate(Request $request)
    {
        if (isset($this->config["user"]) === true && isset($this->config["password"]) === true) {
            $passwordAuth = new PasswordAuth($this->config["username"], $this->config["password"]);
            return $passwordAuth->authenticate($request);
        } elseif (isset($this->config["cert"]) === true && isset($this->config["key"]) === true) {
            $certAuth = new CertificateAuth($this->config["cert"], $this->config["key"]);
            return $certAuth->authenticate($request);
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
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function getPermissions($withHttps = true)
    {
        $result = array();
        $query = new Query($this->config["host"], $this->config["port"], "v1");
        $request = $this->authenticate($query->getRequest());
        $response = $request->send();
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
        return $result;
    }

    /**
     * Returns all the existing hosts
     *
     * @param bool $withHttps
     *
     * @return array
     *
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function getAllHosts($withHttps = true)
    {
        $result = array();
        $query = new Query($this->config["host"], $this->config["port"], "v1/objects/hosts");
        $request = $this->authenticate($query->getRequest());
        $response = $request->send();
        if ($response->code === 200) {
            $result = $this->decodeResult($response, "host");
        }
        return $result;
    }

    /**
     * Returns the list of all existing services
     *
     * @param bool $withHttps
     *
     * @return array
     *
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function getAllServices($withHttps = true)
    {
        $result = array();
        $query = new Query($this->config["host"], $this->config["port"], "v1/objects/services");
        $request = $this->authenticate($query->getRequest());
        $response = $request->send();
        if ($response->code === 200) {
            $result = $this->decodeResult($response, "service");
        }
        return $result;
    }

    /**
     * Returns the list of all comments
     *
     * @param bool $withHttps
     *
     * @return array
     *
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function getAllComments($withHttps = true)
    {
        $result = [];
        $query = new Query($this->config["host"], $this->config["port"], "v1/objects/comments", $withHttps);
        $request = $this->authenticate($query->getRequest());
        $response = $request->send();
        
        if ($response->code === 200) {
            $result = $this->decodeResult($response, "comment");
        }
        
        return $result;
    }

    /**
     * Returns list of comments based on filter provided
     *
     * @param array $filter
     * @param array $attrs
     * @param array $joins
     * @param bool $withHttps
     *
     * @return array
     *
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function getComments($filter = [], $attrs = [], $joins = [], $withHttps = true)
    {
        $result = [];
        $query = new Query($this->config["host"], $this->config["port"], "v1/objects/comments", $withHttps, "POST");
        $query->setFilters($filter);
        $query->setAttributes($attrs);
        $query->setJoins($joins);
        $request = $this->authenticate($query->getRequest());
        $request->addHeaders(array("Accept" => "application/json", "X-HTTP-Method-Override" => "GET"));
        $response = $request->send();
        if ($response->code === 200) {
            $result = $this->decodeResult($response, "comment");
        }
        return $result;
    }

    /**
     * Returns ack of a host
     *
     * @param string $hostName
     * @param bool $withHttps
     *
     * @return array
     *
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function getHostAcknowledgement($hostName = false, $withHttps = true)
    {
        
        $joins = [
            "host.name", 
            "host.acknowledgement", 
            "host.acknowledgement_expiry",
        ];
        
        $filterString = 'comment.entry_type==4 && comment.service_name==""';
        
        if($hostName !== false) {
            $filterString .= ' && host.name=="'.$hostName.'"';
        }
        
        $filter = [
            $filterString
        ];
        
        return $this->getComments($filter, [], $joins, $withHttps);
    }

    /**
     * Returns ack of a service
     *
     * @param string $serviceName
     * @param bool $withHttps
     *
     * @return array
     *
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function getServiceAcknowledgement($serviceName = false, $withHttps = true)
    {
        
        $joins = [
            "service.__name", 
            "service.name", 
            "service.acknowledgement", 
            "service.acknowledgement_expiry"
        ];
        
        $filterString = 'service.acknowledgement!=0 && comment.entry_type==4';
        
        if($serviceName !== false) {
            $filterString .= ' && service.__name=="'.$serviceName.'"';
        }
        
        $filter = [
            $filterString
        ];
        
        return $this->getComments($filter, [], $joins, $withHttps);
    }

    /**
     * Returns the list of hosts that match the given filter and attributes and joins
     *
     * @param array $filter
     * @param array $attrs
     * @param array $joins
     *
     * @return array
     *
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function getHosts($filter = array(), $attrs = array(), $joins = array())
    {
        $result = array();
        $query = new Query($this->config["host"], $this->config["port"], "v1/objects/hosts", true, "POST");
        $query->setFilters($filter);
        $query->setAttributes($attrs);
        $query->setJoins($joins);
        $request = $this->authenticate($query->getRequest());
        $request->addHeaders(array("Accept" => "application/json", "X-HTTP-Method-Override" => "GET"));
        $response = $request->send();
        if ($response->code === 200) {
            $result = $this->decodeResult($response, "host");
        }

        return $result;
    }

    /**
     * Returns the list of services that match the given filter, attributes and joins
     *
     * @param array $filter
     * @param array $attrs
     * @param array $joins
     *
     * @return array
     *
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function getServices($filter = array(), $attrs = array(), $joins = array())
    {
        $result = array();
        $query = new Query($this->config["host"], $this->config["port"], "v1/objects/services", true, "POST");
        $query->setFilters($filter);
        $query->setAttributes($attrs);
        $query->setJoins($joins);
        $request = $this->authenticate($query->getRequest());
        $request->addHeaders(array("Accept" => "application/json", "X-HTTP-Method-Override" => "GET"));
        $response = $request->send();
        if ($response->code === 200) {
            $result = $this->decodeResult($response, "host");
        }

        return $result;
    }

    /**
     * Decodes the response to the internal objects
     *
     * @param Response $response
     * @param $type
     *
     * @return array
     */
    public function decodeResult(Response $response, $type)
    {
        $result = array();
        $decodedResponse = json_decode($response, true);
        if (isset($decodedResponse["results"]) & sizeof($decodedResponse["results"]) > 0) {
            foreach ($decodedResponse["results"] as $icingaRow) {
                switch ($type) {
                    case "host":
                        $icingaObject = new Host(
                            $icingaRow["name"],
                            $icingaRow["type"],
                            $icingaRow["attrs"],
                            $icingaRow["meta"],
                            $icingaRow["joins"]
                        );
                        break;
                    case "service":
                        $icingaObject = new Service(
                            $icingaRow["name"],
                            $icingaRow["type"],
                            $icingaRow["attrs"],
                            $icingaRow["meta"],
                            $icingaRow["joins"]
                        );
                        break;
                    case "comment":
                        $icingaObject = new Comment(
                            $icingaRow["name"],
                            $icingaRow["type"],
                            $icingaRow["attrs"],
                            $icingaRow["meta"],
                            $icingaRow["joins"]
                        );
                        break;
                    default:
                        $icingaObject = null;
                        break;
                }
                if ($icingaObject !== null) {
                    array_push($result, $icingaObject);
                }
            }
        }
        return $result;
    }
}