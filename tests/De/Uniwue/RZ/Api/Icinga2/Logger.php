<?php
/**
 * Created by PhpStorm.
 * User: poa32kc
 * Date: 15.12.17
 * Time: 13:52
 */

namespace De\Uniwue\RZ\Api\Icinga2;

/**
 * Class Logger
 * This is a simple log system that is used for debuging the application. This replicate
 * the monolog logger.
 *
 * @package De\Uniwue\RZ\Api\Icinga2
 */

class Logger
{

    /**
     * Logger constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param string $level The level of logging should be used (info, warning...)
     * @param string $message The message that should be logged
     * @param array $context The context of the log
     */
    public function log($level, $message, $context = array())
    {
        echo "Logging: Level: $level The following message $message" . "\n";
    }
}