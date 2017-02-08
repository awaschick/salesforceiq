<?php namespace SalesforceIQ\Resource;

Use \SalesforceIQ\Resource\Contact;
Use \SalesforceIQ\resource\Collection;

class ListItem {

    public $id;
    public $modifiedDate;
    public $listId;
    private $list;
    public $accountId;
    public $contactIds = array();
    public $name;
    public $fieldValues = array();
    public $state = array();

    public function json()
    {
        if(!$this->name)       unset($this->name);
        if(!$this->id)         unset($this->id);
        if(!$this->accountId)  unset($this->accountId);

        unset($this->modifiedDate);
        unset($this->createdDate);
        return json_encode($this);
    }

    // Not supporting multiselect yet i guess....
    public function setField($name, $values)
    {
        $field = $this->list->lookupFieldName($name);

        if(!$field) {
            return false;
        }

        // Should check if field is multiSelect to throw an error.
        if(isset($field->id))
        {
            foreach((array)$values as $v) {
                $this->fieldValues[$field->id][] = array('raw' => $field->resolveFieldValue($v) );
            }
        }
    }

    /**
     * Save the list item.
     *
     * @return mix
     */
    public function save()
    {
        $request = new ApiRequest;

        if($this->id) {
            return $request->newPut("lists/{$this->listId}/listitems/{$this->id}", $this);
        }
        else{
            $result = $request->newPost("lists/{$this->listId}/listitems/", $this);
            if($result->id) $this->id = $result->id;
        }
    }

    public function setList(Collection $list)
    {
        $this->list = $list;
        $this->listId = $list->id;
    }

    public function getList()
    {
        return $this->list;
    }

    public function setContact(Contact $contact)
    {
        $this->contactIds = array($contact->id);
    }

    public static function find($list, $listItemId)
    {
        $request = new ApiRequest();
        $listItem = self::handleResponse($list, $request->newGet("lists/{$list->id}/listitems/{$listItemId}"));

        return $listItem;
    }

    /**
     * Get all of the list items.
     *
     * @return array
     */
    public static function all($list)
    {
        $request = new ApiRequest();
        $listItems = self::handleResponse($list, $request->newGet("lists/{$list->id}/listitems/"));

        return $listItems;
    }

    public static function getContacts($list, $contacts)
    {
        $request = new ApiRequest();
        $contactIds = array();

        if(is_array($contacts))
        {
            foreach((array) $contacts as $contact) {
                $contactIds[] = (is_object($contact)) ? $contact->id : $contact;
            }
        }
        else{
            $contactIds[] = (is_object($contacts)) ? $contacts->id : $contacts;
        }

        $listItems = self::handleResponse($list, $request->newGet("lists/{$list->id}/listitems/?_ids=".implode(',', $contactIds)));

        return $listItems;
    }

    /**
     * Parse server response.
     *
     * @param  array $response
     * @return object|array
     */
    public static function handleResponse($list, $response)
    {
        if(isset($response['objects']))
        {
            $objects = array();

            xdebug_break();

            foreach($response['objects'] as $object) {
                $objects[] = self::parseResponse($list, $object);
            }

            return $objects;
        }
        else {
            return self::parseResponse($list, $response);
        }
    }

    /**
     * Parse server response properties.
     *
     * @return \SalesforceIQ\Resource\ListItem
     */
    public static function parseResponse($list, $response)
    {
        $listItem = new self;
        $listItem->id = $response['id'];
        $listItem->modifiedDate = $response['modifiedDate'];
        $listItem->createdDate = $response['createdDate'];
        $listItem->accountId = $response['accountId'];
        $listItem->contactIds = $response['contactIds'];
        $listItem->name = $response['name'];
        $fieldDefinitions = $list->getProperty('list')['fields'];

        // put the values into a convenient name/value associative array 
        foreach($fieldDefinitions as $fieldDefinition) {
            if (array_key_exists($fieldDefinition->id, $response['fieldValues'])) {
                $stateItem = [
                    'name' => $fieldDefinition->name,
                    'field_id' => $fieldDefinition->id,
                    'dataType' => $fieldDefinition->dataType,
                    'value' => []
                ];
                $fieldValue = $response['fieldValues'][$fieldDefinition->id];
                foreach ($fieldValue as $fieldValueElement) {
                    if ($fieldDefinition->dataType == 'List') {
                        $listOptionIndex = $fieldValueElement['raw'];
                        $stateItem['value'][] = $fieldDefinition->listOptions[$listOptionIndex]['display'];
                    } else {
                        $stateItem['value'][] = $fieldValueElement['raw'];
                    }
                }
                $listItem->state[] = $stateItem;
            }
        }
        
        // copy the raw field data, in case you need that for some bizarre reason  
        foreach($response['fieldValues'] as $key => $fieldValue){
            $listItem->fieldValues[$key] = $fieldValue;            
        }

        $listItem->setList($list);
        return $listItem;
    }
}
