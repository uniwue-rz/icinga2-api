<?php
/**
 * Created by PhpStorm.
 * User: poa32kc
 * Date: 18.12.17
 * Time: 11:16
 */

namespace De\Uniwue\RZ\Api\Icinga2\Auth;


use Httpful\Request;

class CertificateAuth implements AuthInterface
{

    /**
     * @var string
     */
    private $certPath;

    /**
     * @var string
     */
    private $keyPath;

    /**
     * @var string
     */
    private $keyPassword;

    /**
     * @var string
     */
    private $caCertPath;

    /**
     * CertificateAuth constructor.
     *
     * @param string $certPath
     * @param string $keyPath
     * @param string $keyPassword
     * @param string $caCertPath
     */
    public function __construct($certPath, $keyPath, $keyPassword = "", $caCertPath = "")
    {
        $this->certPath = $certPath;
        $this->keyPath = $keyPath;
        $this->keyPassword = $keyPassword;
        $this->caCertPath = $caCertPath;
    }

    /**
     * Returns the path to the given certificate
     *
     * @return string
     */
    public function getCertPath()
    {
        return $this->certPath;
    }

    /**
     * Sets the path to the given certificate
     *
     * @param string $certPath
     */
    public function setCertPath($certPath)
    {
        $this->certPath = $certPath;
    }

    /**
     * Returns the key path
     *
     * @return string
     */
    public function getKeyPath()
    {

        return $this->keyPath;
    }

    /**
     * Sets the path to the given key
     *
     *
     * @param string $keyPath
     */
    public function setKeyPath($keyPath)
    {
        $this->keyPath = $keyPath;
    }

    /**
     * Returns the password for the given key
     *
     * @return string
     */
    public function getKeyPassword()
    {
        return $this->keyPassword;
    }

    /**
     * Sets the password for the given key
     *
     * @param string $keyPassword
     */
    public function setKeyPassword($keyPassword = "")
    {
        $this->keyPassword = $keyPassword;
    }

    /**
     * Sets the ca Cert for the given path
     *
     * @param string $caCertPath
     */
    public function setCaCertPath($caCertPath = "")
    {
        $this->caCertPath = $caCertPath;
    }

    /**
     * Returns the ca Cert for the given query
     *
     * @return string
     */
    public function getCaCertPath()
    {
        return $this->caCertPath;
    }

    /**
     * Adds the authentication to the given server
     *
     * @param Request $request
     *
     * @return Request
     */
    public function authenticate(Request $request)
    {
        $request->authenticateWithCert($this->certPath, $this->keyPath, $this->keyPassword);
        return $request;
    }
}