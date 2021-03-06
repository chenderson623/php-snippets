<?php

class ListCompare {

    /**
     * @var array 
     */
    protected $list; //should be an array of single values. keys don't matter: array(key1=>value1, key2=>value2....)
    protected $checked_in_list     = array(); //generated by self::check($value)
    protected $checked_not_in_list = array(); //generated by self::check($value)

    public function __construct($list) {
        $this->list = $list;
    }

    public function is_in_list($value) {
        return in_array($value, $this->list);
    }

    public function check($value) {
        if ($this->is_in_list($value)) {
            if(!in_array($value, $this->checked_in_list)) {
                $this->checked_in_list[] = $value;
            }
            return true;
        } else {
            if(!in_array($value, $this->checked_not_in_list)) {
                $this->checked_not_in_list[] = $value;
            }
            return false;
        }
    }

    public function getCheckedInList() {
        return $this->checked_in_list;
    }
    
    public function getCheckedNotInList() {
        return $this->checked_not_in_list;
    }
    
    public function getListNotChecked() {
        return array_diff($this->list, $this->checked_in_list);
    }
}