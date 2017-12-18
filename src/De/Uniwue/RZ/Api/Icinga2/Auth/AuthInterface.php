<?php
/**
 * Created by PhpStorm.
 * User: poa32kc
 * Date: 18.12.17
 * Time: 11:41
 */

namespace De\Uniwue\RZ\Api\Icinga2\Auth;

use Httpful\Request;

interface AuthInterface
{
    /**
     * Authenticate to the given server. Returns the request
     *
     * @param Request $request
     *
     * @return Request
     */
    public function authenticate(Request $request);
}