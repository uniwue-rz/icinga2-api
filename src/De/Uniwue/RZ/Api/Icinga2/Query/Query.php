<?php
/**
 * Created by PhpStorm.
 * User: poa32kc
 * Date: 18.12.17
 * Time: 10:20
 */

namespace De\Uniwue\RZ\Api\Icinga2\Query;

use Httpful\Request;

class Query
{
    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $port;

    /**
     * @var string
     */
    private $path;

    /**
     * @var bool
     */
    private $withHttps;

    /**
     * @var string
     */
    private $method;

    /**
     * @var array
     */
    private $filters;

    /**
     * @var array
     */
    private $attributes;

    /**
     * @var array
     */
    private $templates;

    /**
     * @var array
     */
    private $joins;

    /**
     * @var bool
     */
    private $followRedirects;

    /**
     * Query constructor.
     *
     * @param string $host The host that should get the query
     * @param string $port The port that should get the query
     * @param string $path The path for the given query
     * @param bool $withHttps Should the query send with https
     * @param string $method The method of the given query
     */
    public function __construct($host, $port, $path = "", $withHttps = true, $method = "GET")
    {
        $this->host = $host;
        $this->port = $port;
        $this->path = $path;
        $this->withHttps = $withHttps;
        $this->method = $method;
        $this->followRedirects = false;
        $this->joins = array();
        $this->attributes = array();
        $this->templates = array();
        $this->filters = array();
    }

    /**
     * Returns the joins array for the query
     *
     * @return array
     */
    public function getJoins()
    {
        return $this->joins;
    }

    /**
     * Sets the josin array for the query
     *
     * @param $joins
     */
    public function setJoins($joins = array())
    {
        $this->joins = $joins;
    }

    /**
     * Redirect the status of follow redirects
     *
     * @return bool
     */
    public function getFollowRedirects()
    {
        return $this->followRedirects;
    }

    /**
     * Sets the follow redirects
     *
     * @param bool $followRedirects
     */
    public function setFollowRedirects($followRedirects)
    {
        $this->followRedirects = $followRedirects;
    }

    /**
     * Returns the template used to create icinga2 objects
     *
     * @return array
     */
    public function getTemplates()
    {
        return $this->templates;
    }

    /**
     * Sets the template used to create icinga2 object
     *
     * @param $template
     */
    public function setTemplate($template)
    {
        $this->templates = $template;
    }

    /**
     * Return the host for the given query
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Sets the host for the given query
     *
     * @param string $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * Returns the port for the given query
     *
     * @return string
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Sets the port for the given query
     *
     * @param string $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * Returns the path for the given query
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Sets the path for the given query
     *
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Should the query use https
     *
     * @return bool
     */
    public function getWithHttps()
    {
        return $this->withHttps;
    }

    /**
     * Set if the query should use https
     *
     * @param bool $withHttps
     */
    public function setWithHttps($withHttps = true)
    {
        $this->withHttps = $withHttps;
    }

    /**
     * Returns the method for the given query
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Sets the method for the given query
     *
     * @param string $method
     */
    public function setMethod($method = "GET")
    {
        $this->method = $method;
    }

    /**
     * Returns the filters used for the given query
     *
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Sets the filter for the given query
     *
     * @param array $filters
     */
    public function setFilters($filters = array())
    {
        $this->filters = $filters;
    }

    /**
     * Returns the wanted attributes in the given query
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Set the wanted attributes in the given query
     *
     * @param array $attributes
     */
    public function setAttributes($attributes = array())
    {
        $this->attributes = $attributes;
    }

    /**
     * Creates the URI for the given query
     *
     * @return string
     */
    public function getUri()
    {
        $uri = $this->host . ":" . $this->port . "/" . $this->path;
        if ($this->method === "GET") {
            $uri = $uri . "?" . $this->getQueryData(false);
        }
        if ($this->withHttps === true) {
            return "https://" . $uri;
        }
        return "http://" . $uri;
    }

    /**
     * Creates the request object
     *
     * @return Request
     */
    public function getRequest()
    {
        switch ($this->method) {
            case "GET":
                $request = Request::get($this->getUri());
                break;
            case "POST":
                $request = Request::post($this->getUri(), $this->getQueryData(true, "application/json"));
                break;
            case "PUT":
                $request = Request::put($this->getUri(), $this->getInitData());
                break;
            default:
                $request = Request::get($this->getUri());
                break;
        }
        if ($this->followRedirects === true) {
            $request->followRedirects();
        }

        return $request;
    }

    /**
     * Creates the data parameters in JSON or HTML_ENCODED Format
     *
     * @param bool $inJson If the data should be in JSON
     *
     * @return string
     */
    public function getQueryData($inJson = false)
    {
        $data = array();
        if (sizeof($this->filters) > 0) {
            $data["filter"] = $this->filters;
        }
        if (sizeof($this->attributes) > 0) {
            $data["attrs"] = $this->attributes;
        }
        if (sizeof($this->joins) > 0) {
            $data["joins"] = $this->joins;
        }
        if ($inJson) {
            return json_encode($data);
        }
        return http_build_query($data);
    }

    /**
     * Returns the data that is used to create new configurations in icinga
     *
     * @return string
     */
    public function getInitData()
    {
        $data = array();
        if (sizeof($this->attributes) > 0) {
            $data["attrs"] = $this->attributes;
        }
        if (sizeof($this->templates) > 0) {
            $data["templates"] = $this->templates;
        }
        return json_encode($data);
    }
}