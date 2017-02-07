<?php namespace Waschick\SalesforceIQ\Resource;

use DateTime;
use DateTimeZone;

class RiqContact {

    use PropertiesTrait;

    /**
     * The contact's ID.
     *
     * @var string
     */
    public $id;

    /**
     * The contact's last modified date.
     *
     * @var \DateTime
     */
    public $modifiedDate;

    /**
     * Create a new Contact instance.
     *
     * @param  array  $properties
     * @return void
     */
    function __construct(array $properties = array())
    {
        // Set properties to valide
        $this->required = array('email');

        // Set properties
        $this->fill($properties);
    }

    /**
     * Save the contact.
     *
     * @return mix
     */
    public function save()
    {
        if ($this->isValid() === false) {
            return false;
        }

        // Start new request
        $request = new ApiRequest;

        if($this->id) {
            $result = $request->newPut("contacts/{$this->id}", $this);
        }
        else{
            $result = $request->newPost('contacts', $this);
        }

        if(!$result) {
            return false;
        }

        return self::parseResponse($result);
    }

    /**
     * Find a contact by its ID.
     *
     * @param  string  $id
     * @return \Torann\RelateIQ\Resource\RiqContact
     */
    public static function find($id)
    {
        $request = new ApiRequest();
        $contact = self::handleResponse($request->newGet("contacts/{$id}"));

        return $contact;
    }

    /**
     * Get all of the contacts.
     *
     * @return array
     */
    public static function all()
    {
        $request = new ApiRequest();
        $contacts = self::handleResponse($request->newGet('contacts'));

        return $contacts;
    }

    /**
     * Parse server response.
     *
     * @param  array $response
     * @return object|array
     */
    public static function handleResponse($response)
    {
        if(isset($response['objects']))
        {
            $objects = array();

            foreach($response['objects'] as $object) {
                $objects[] = self::parseResponse($object);
            }

            return $objects;
        }
        else {
            return self::parseResponse($response);
        }
    }

    /**
     * Parse server response properties.
     *
     * @return \Torann\RelateIQ\Resource\RiqContact
     */
    public static function parseResponse($response)
    {
        $properties = array();

        // Set properties
        foreach ($response['properties'] as $key => $values)
        {
            foreach ($values as $value)
            {
                if (count($values) > 1) {
                    $properties[$key][] = $value['value'];
                }
                else {
                    $properties[$key] = $value['value'];
                }
            }
        }

        // Create new object
        $contact = new static($properties);
        $contact->id = $response['id'];
        $contact->modifiedDate = new DateTime("@{$response['modifiedDate']}", new DateTimeZone('UTC'));

        return $contact;
    }
}
