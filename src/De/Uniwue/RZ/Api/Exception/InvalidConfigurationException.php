<?php
/**
 * Created by PhpStorm.
 * User: poa32kc
 * Date: 14.12.17
 * Time: 12:07
 */

namespace De\Uniwue\RZ\Api\Exception;

use Throwable;

class InvalidConfigurationException extends \Exception
{
    /**
     * InvalidConfigurationException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}