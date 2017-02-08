<?php namespace SalesforceIQ\Resource;

class RiqListField {

    public $id;
    public $name;
    public $listOptions = array();

    function __construct($field)
    {
        $this->id = $field['id'];
        $this->name = $field['name'];
        $this->isMultiSelect = $field['isMultiSelect'];
        $this->isEditable = $field['isEditable'];
        $this->dataType = $field['dataType'];
        $this->listOptions = $field['listOptions'];
    }

    public function resolveFieldValue($input)
    {
        if(!isset($this->listOptions) || count($this->listOptions) < 1) {
            return $input;
        }

        foreach($this->listOptions as $option)
        {
            if($option->display == $input) {
                return (string)$option->id;
            }
        }
    }
}
