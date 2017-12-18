<?php
/**
 * Created by PhpStorm.
 * User: poa32kc
 * Date: 18.12.17
 * Time: 11:16
 */

namespace De\Uniwue\RZ\Api\Icinga2\Auth;

use Httpful\Request;

class PasswordAuth implements AuthInterface
{

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * PasswordAuth constructor.
     *
     * @param string $username
     * @param string $password
     */
    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Sets the username for the given Authentication
     *
     * @param $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Returns the username for the given authentication
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Sets the password for the given authentication
     *
     * @param $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Returns the password for the given authentication
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Authenticates to the server
     *
     * @param Request $request
     *
     * @return Request|void
     */
    public function authenticate(Request $request)
    {
        $request->authenticateWithBasic($this->username, $this->password);
        return $request;
    }
}