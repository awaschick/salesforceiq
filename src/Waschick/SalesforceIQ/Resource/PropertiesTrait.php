<?php namespace Waschick\SalesforceIQ\Resource;

trait PropertiesTrait
{
    /**
     * The contact's properties.
     *
     * @var array
     */
    protected $properties = array();

    /**
     * The required contact properties.
     *
     * @var array
     */
    protected $required = array();

    /**
     * Validation errors.
     *
     * @var array
     */
    protected $errors = array();

    /**
     * Get an property from the contact.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getProperty($key)
    {
        if (array_key_exists($key, $this->properties))
        {
            return $this->properties[$key];
        }
    }

    /**
     * Set a given property on the contact.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function setProperty($key, $value)
    {
        $this->properties[$key] = $value;
    }

    /**
     * Fill the model with an array of properties.
     *
     * @param  array  $properties
     * @return $this
     *
     * @throws MassAssignmentException
     */
    public function fill(array $properties)
    {
        foreach ($properties as $key => $value)
        {
            $this->setProperty($key, $value);
        }

        return $this;
    }

    /**
     * Return errors.
     *
     * @return bool
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Validate contact properties.
     *
     * @return bool
     */
    public function isValid()
    {
        foreach ($this->required as $key)
        {
            if(!isset($this->properties[$key]) ||
                (isset($this->properties[$key]) && ! $this->properties[$key])
            )
            {
                $this->errors[] = $key;
            }
        }

        return (count($this->errors) === 0);
    }

    /**
     * Convert the contact instance to JSON.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Convert the contact's properties to an array.
     *
     * @return array
     */
    public function toArray()
    {
        $data = array(
            'properties' => array()
        );

        // Formate properties
        foreach ($this->properties as $key => $value)
        {
            $data['properties'][$key][] = array(
                'value' => $value
            );
        }

        // Include contact ID
        if($this->id) {
            $data['id'] = $this->id;
        }

        return $data;
    }

    /**
     * Dynamically retrieve properties on the contact.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getProperty($key);
    }

    /**
     * Dynamically set properties on the contact.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->setProperty($key, $value);
    }

    /**
     * Determine if an attribute exists on the contact.
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->properties[$key]);
    }

    /**
     * Unset an attribute on the contact.
     *
     * @param  string  $key
     * @return void
     */
    public function __unset($key)
    {
        unset($this->properties[$key]);
    }
}
