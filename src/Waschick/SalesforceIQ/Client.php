<?php namespace Waschick\SalesforceIQ;

use Waschick\SalesforceIQ\Resource;

class Client {

    /**
     * RelateIQ lists.
     *
     * @var array
     */
    public $lists = array();

    /**
     * Constructor.
     *
     * @return void
     */
    function __construct($key, $secret, array $listId = array())
    {
        Resource\RiqConfig::setKey($key, $secret);

        $this->listId = $listId;
    }

    /**
     * Get a list.
     *
     * @param  string  $id
     *
     * @return \Torann\RelateIQ\Resource\RiqList
     */
    public function getList($id)
    {
        return Resource\RiqList::find($id);
    }

    /**
     * Get all lists.
     *
     * @return array
     */
    public function getLists()
    {
        if(count($this->lists) < 1) {
            $this->lists = Resource\RiqList::all();
        }

        return $this->lists;
    }

    /**
     * Get specified contact.
     *
     * @param  string  $id
     *
     * @return \Torann\RelateIQ\Resource\RiqContact
     */
    public function getContact($id)
    {
        return Resource\RiqContact::find($id);
    }

    /**
     * Get all contacts.
     *
     * @return array
     */
    public function getContacts()
    {
        return Resource\RiqContact::all();
    }

    /**
     * Create a new contact.
     *
     * @param  array $properties
     *
     * @return \Torann\RelateIQ\Resource\RiqContact
     */
    public function newContact(array $properties = array())
    {
        $contact = new Resource\RiqContact($properties);

        return $contact->save();
    }

    /**
     * Get all list items for a contact.
     *
     * @return array
     */
    public function getAllListItemsForContact($contacts)
    {
        $lists = $this->getLists();

        $listItems = array();

        foreach($lists as $list) {
            $listItems = array_merge($listItems, $list->getListItemsForContacts($contacts));
        }

        return $listItems;
    }
}
