<?php
/**
 * Created by PhpStorm.
 * User: poa32kc
 * Date: 14.12.17
 * Time: 11:47
 */

namespace De\Uniwue\RZ\Api\Icinga2;
include("Logger.php");

use De\Uniwue\RZ\Api\Exception\InvalidConfigurationException;
use PHPUnit\Framework\TestCase;
use De\Uniwue\RZ\Api\Icinga2\Logger;

class Icinga2Test extends TestCase
{
    private $config;

    private $hostData;

    /**
     * Setup for the Test
     *
     */
    public function setUp()
    {
        global $apiConfig;
        global $hostData;
        $this->config = $apiConfig;
        $this->hostData = $hostData;
    }

    /**
     * Tests the init of the given class
     */
    public function testInit()
    {
        $icinga2 = new Icinga2($this->config);
        $this->assertEquals(get_class($icinga2), "De\Uniwue\RZ\Api\Icinga2\Icinga2");
    }

    /**
     * Tests the server reachable with good data
     *
     * @throws \De\Uniwue\RZ\Api\Exception\ServerNotReachableException
     */
    public function testServerReachable()
    {
        $icinga2 = new Icinga2($this->config);
        $this->assertTrue($icinga2->serverReachable());
    }

    /**
     * Test the server reachability with invalid configuration
     *
     * @expectedException \De\Uniwue\RZ\Api\Exception\InvalidConfigurationException
     */
    public function testServerReachableInvalidConfig()
    {
        $config = $this->config;
        $config["host"] = null;
        $icinga2 = new Icinga2($config);
        $icinga2->serverReachable();
    }

    /**
     *
     * Test the server reachability with unreachable server
     *
     * @expectedException \De\Uniwue\RZ\Api\Exception\ServerNotReachableException
     */
    public function testServerReachavleServerNotReachable()
    {
        $config = $this->config;
        $config["host"] = "no-reachablehost.localhost";
        $icinga2 = new Icinga2($config);
        $icinga2->serverReachable();
    }

    /**
     * Tests the if the server is authenticable.
     *
     * @throws \De\Uniwue\RZ\Api\Exception\SeverNotAccessibleException
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function testServerAuthenticable()
    {
        $logger = new Logger();
        $icinga2 = new Icinga2($this->config, $logger);
        $this->assertTrue($icinga2->serverAuthenticable());
    }

    /**
     * Tests the get permissions
     *
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function testGetPermissions()
    {
        $logger = new Logger();
        $icinga2 = new Icinga2($this->config, $logger);
        $this->assertEquals($icinga2->getPermissions(), array("*"));
    }

    /**
     * Tests get all hosts
     *
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function testGetAllHosts()
    {
        $logger = new Logger();
        $icinga2 = new Icinga2($this->config, $logger);
        $this->assertTrue(sizeof($icinga2->getAllHosts()) > 0);
    }

    /**
     * Tests the get all Services
     *
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function testGetAllServices()
    {
        $logger = new Logger();
        $icinga2 = new Icinga2($this->config, $logger);
        $this->assertTrue(sizeof($icinga2->getAllServices()) > 0);
    }

    /**
     * Test the get hosts
     *
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function testGetHostsWithFilter()
    {
        $logger = new Logger();
        $icinga2 = new Icinga2($this->config, $logger);
        $this->assertEquals(sizeof($icinga2->getHosts(array("match(\"" . $this->hostData["hostname"] . "*\",host.name)"))), 1);
    }

    /**
     * Tests the get hosts with attributes
     *
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function testGetHostsWithAttrs(){
        $logger = new Logger();
        $icinga2 = new Icinga2($this->config, $logger);
        $result = $icinga2->getHosts(array("match(\"" . $this->hostData["hostname"] . "*\",host.name)"), array("display_name", "name"));
        $this->assertEquals($result[0]->getAttribute("display_name"), $this->hostData["display_name"]);
    }
}