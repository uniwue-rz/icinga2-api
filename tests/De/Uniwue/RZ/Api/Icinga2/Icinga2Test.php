<?php
/**
 * Created by PhpStorm.
 * User: poa32kc
 * Date: 14.12.17
 * Time: 11:47
 */

namespace De\Uniwue\RZ\Api\Icinga2;

use \PHPUnit\Framework\TestCase;

class Icinga2Test extends TestCase
{
    private $config;

    public function setUp(){
        global $apiConfig;
        $this->config =$apiConfig;
    }

    /**
     * Tests the init of the given class
     */
    public function testInit(){
        $icinga2 = new Icinga2($this->config);
        $this->assertEquals(get_class($icinga2), "De\Uniwue\RZ\Api\Icinga2\Icinga2");
    }
}