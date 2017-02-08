<?php namespace SalesforceIQ;

use SalesforceIQ\Resource;

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
        Resource\Config::setKey($key, $secret);

        $this->listId = $listId;
    }

    /**
     * Get a list.
     *
     * @param  string  $id
     *
     * @return \SalesforceIQ\Resource\Collection
     */
    public function getList($id)
    {
        return Resource\Collection::find($id);
    }

    /**
     * Get all lists.
     *
     * @return array
     */
    public function getLists()
    {
        if(count($this->lists) < 1) {
            $this->lists = Resource\Collection::all();
        }

        return $this->lists;
    }

    /**
     * Get specified contact.
     *
     * @param  string  $id
     *
     * @return \SalesforceIQ\Resource\Contact
     */
    public function getContact($id)
    {
        return Resource\Contact::find($id);
    }

    /**
     * Get all contacts.
     *
     * @return array
     */
    public function getContacts()
    {
        return Resource\Contact::all();
    }

    /**
     * Create a new contact.
     *
     * @param  array $properties
     *
     * @return \SalesforceIQ\Resource\Contact
     */
    public function newContact(array $properties = array())
    {
        $contact = new Resource\Contact($properties);

        return $contact->save();
    }


    // get all list items for a specific list ID. 
    public function getAllListItems($listId)
    {
        $sourceList = $this->getList($listId);
        return $sourceList->getlistItems();
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
