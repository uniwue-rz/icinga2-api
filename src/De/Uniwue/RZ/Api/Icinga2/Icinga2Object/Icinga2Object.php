<?php
/**
 * Created by PhpStorm.
 * User: poa32kc
 * Date: 18.12.17
 * Time: 09:28
 */

namespace De\Uniwue\RZ\Api\Icinga2\Icinga2Object;


use De\Uniwue\RZ\Api\Icinga2\Icinga2;

class Icinga2Object
{
    /**
     * @var array
     */
    private $attributes;

    /**
     * @var array
     */
    private $meta;

    /**
     * @var array
     */
    private $joins;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * @var \De\Uniwue\RZ\Api\Icinga2\Icinga2 
     */
    protected $icinga2;

    /**
     * Icinga2Object constructor.
     *
     * @param string $name The name of the given Icinga2Object
     * @param string $type The type of the given Icinga2Object
     * @param \De\Uniwue\RZ\Api\Icinga2\Icinga2 $icinga2
     * @param array $attributes The attributes for the given Icinga2Object
     * @param array $meta The meta information for the given Icinga2Object
     * @param array $joins The joins for the given query result
     */
    public function __construct($name, $type, Icinga2 $icinga2, $attributes = array(), $meta = array(), $joins = array())
    {
        $this->name = $name;
        $this->type = $type;
        $this->icinga2 = $icinga2;
        $this->attributes = $attributes;
        $this->meta = $meta;
        $this->joins = $joins;
    }

    /**
     * Returns the list of attributes for the given Icinga2Object
     *
     * @return array
     */
    public function getAttributes()
    {

        return $this->attributes;
    }

    /**
     * Sets the attributes for the given Icinga2Object
     *
     * @param array $attributes The attributes that should be set for the given Icinga2Object
     */
    public function setAttributes($attributes = array())
    {

        $this->attributes = attributes;
    }

    /**
     * Returns the meta information of the given Icinga2Object
     *
     * @return array
     */
    public function getMeta()
    {

        return $this->meta;
    }

    /**
     * Sets the meta information for the given Icinga2Object
     *
     * @param array $meta The meta information to be set
     */
    public function setMeta($meta = array())
    {
        $this->meta = $meta;
    }

    /**
     * Returns the joins in the query
     *
     * @return array
     */
    public function getJoins()
    {

        return $this->joins;
    }

    /**
     * Sets the Joins for the query
     *
     * @param array $joins The joins to be set
     */
    public function setJoins($joins = array())
    {
        $this->joins = $joins;
    }

    /**
     * Returns the name of the given Icinga2Object
     *
     * @return string
     */
    public function getName()
    {

        return $this->name;
    }

    /**
     * Sets the name of the given Icinga2Object
     *
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the type of Icinga2Object
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the type of the given Icinga2Object
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Returns the attribute for the given Icinga2Object
     *
     * @param $name
     *
     * @return mixed|null
     */
    public function getAttribute($name)
    {
        if ($this->attributeExists($name) === true) {

            return $this->attributes[$name];
        }

        return null;
    }

    /**
     * Checks if the given attribute exists.
     *
     * @param $name
     *
     * @return boolean
     */
    public function attributeExists($name)
    {

        return isset($this->attributes[$name]);
    }
}