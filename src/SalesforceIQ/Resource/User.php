<?php namespace SalesforceIQ\Resource;

use DateTime;
use DateTimeZone;

class User {

    use PropertiesTrait;

    /**
     * The contact's ID.
     *
     * @var string
     */
    public $id;

   
    function __construct(array $properties = array())
    {
        // Set properties to valide
        $this->required = array('email');

        // Set properties
        $this->fill($properties);
    }

    /**
     * Find a contact by its ID.
     *
     * @param  string  $id
     * @return \SalesforceIQ\Resource\User
     */
    public static function find($id)
    {
        $request = new ApiRequest();
        $user = self::handleResponse($request->newGet("users/{$id}"));

        return $user;
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
     * @return \SalesforceIQ\Resource\Contact
     */
    public static function parseResponse($response)
    {
        $properties = array();

        // Create new object
        $user = new static($properties);
        $user->id = $response['id'];
        $user->name = $response['name'];
        $user->email = $response['email'];

        return $user;
    }
}
