<?php namespace SalesforceIQ\Resource;

use DateTime;
use DateTimeZone;

class Collection {

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

    // public $title;
    // public $listType;
    // public $fields = array();

    /**
     * Create a new List instance.
     *
     * @param  array $properties
     * @return void
     */
    function __construct(array $properties = array())
    {
        // Set properties
        $this->fill($properties);
    }

    public function listItemContainer()
    {
        $listItemContainer = new ListItem;
        $listItemContainer->setList($this);

        return $listItemContainer;
    }

    public function getListItem($listItemId)
    {
        return ListItem::find($this, $listItemId);
    }

    public function getListItems()
    {
        //TODO:: paginate?
        return ListItem::all($this);
    }

    public function getListItemsForContacts($contacts)
    {
        return ListItem::getContacts($this, $contacts);
    }

    public function lookupFieldName($name)
    {
        foreach($this->fields as $field)
        {
            if($field->name == $name) {
                return $field;
            }
        }

        return false;
    }

    /**
     * Find a list by its ID.
     *
     * @param  string  $id
     * @return \SalesforceIQ\Resource\Collection
     */
    public static function find($id)
    {
        $request = new ApiRequest();
        $list = self::handleResponse($request->newGet("lists/{$id}"));

        return $list;
    }

    /**
     * Get all of the lists.
     *
     * @return array
     */
    public static function all()
    {
        $request = new ApiRequest();
        $lists = self::handleResponse($request->newGet('lists'));

        return $lists;
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
            $listObjects = array();

            foreach($response['objects'] as $object)
            {
                $list = self::parseResponse($object);
                $listObjects[$list->id] = $list;
            }

            return $listObjects;
        }
        else {
            return self::parseResponse($response);
        }
    }

    /**
     * Parse server response properties.
     *
     * @return \SalesforceIQ\Resource\Collection
     */
    public static function parseResponse($response)
    {
        $properties = array(
            'title' => $response['title'],
            'listType' => $response['listType']
        );

        // Set field properties
        foreach ($response['fields'] as $fieldResponse)
        {
            $properties['list']['fields'][] = new ListField($fieldResponse);
        }

        // Create new object
        $list = new static($properties);
        $list->id = $response['id'];
        $list->modifiedDate = new DateTime("@{$response['modifiedDate']}", new DateTimeZone('UTC'));

        return $list;
    }
}
