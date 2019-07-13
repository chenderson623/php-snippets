<?php

class TemplateVars {

    protected $begin_delimiter;
    protected $end_delimeter;
    protected $template_vars = array(); //array of TemplateVar
    protected $values        = array();

    public function __construct($begin_delimiter, $end_delimiter) {
        $this->begin_delimiter = $begin_delimiter;
        $this->end_delimeter   = $end_delimiter;
    }

    public function addValue($key, $value) {
        $this->values[$key] = $value;
    }

    public function addValues(array $values) {
        if (!is_array($values)) {
            throw new Exception('Values needs to be an array');
        }
        foreach ($values as $key => $value) {
            $this->addValue($key, $value);
        }
    }

	public function hasValue($key) {
		return isset($this->values[$key]);
	}

    public function getValue($key) {
        return ($this->hasValue($key)) ? $this->values[$key] : null;
    }

    /**
     * @param type $key can be string or array
     */
    public function isEmpty($key) {
        if (is_string($key)) {
            $value = $this->getValue($key);
            return empty($value);
        }
        if (is_array($key)) {
            foreach ($key as $test_key) {
                $value = $this->getValue($key);
                if (empty($value))
                    return true;
            }
            return false;
        }
        throw new Exception("Bad value passed to isEmpty");
    }

    /**
     *
     * @param type $template_tag
     * @param type $substitute_empty
     * @param type $substitute_value
     * @param type $empty_check
     * @return \TemplateVar
     */
    public function addTemplateVarDef($template_tag, $substitute_empty = '', $substitute_value = null, $empty_check = null) {
        $template_var                       = new TemplateVar($template_tag, $substitute_empty, $substitute_value, $empty_check);
        $this->template_vars[$template_tag] = $template_var;
        return $template_var;
    }

    public function addTemplateVarDefs(array $template_var_defs) {
        if (!is_array($template_var_defs)) {
            throw new Exception('Template Var Defs needs to be an array');
        }

        foreach ($template_var_defs as $template_tag => $template_var_def) {
            $substitute_empty = (isset($template_var_def['substitute_empty'])) ? $template_var_def['substitute_empty'] : '';
            $substitute_value = (isset($template_var_def['substitute_value'])) ? $template_var_def['substitute_value'] : null;
            $empty_check      = (isset($template_var_def['empty_check'])) ? $template_var_def['empty_check'] : null;

            $this->addTemplateVarDef($template_tag, $substitute_empty, $substitute_value, $empty_check);
        }
    }

    /**
     *
     * @param string $key
     * @return TemplateVar
     * @throws Exception
     */
    public function getTemplateVar($key) {
        if (!isset($this->template_vars[$key])) {
            //if template var is not set, but value is, we can go ahead and create one:
            $value = $this->getValue($key);
            if (!empty($value)) {
                $template_var = $this->addTemplateVarDef($key);
                return $template_var;
            }

            throw new Exception("Template Var [$key] is not set");
        }
        return $this->template_vars[$key];
    }

    public function hasTemplateVar($key) {
        return isset($this->template_vars[$key]);
    }

    protected function getKeysInText($text) {
        $delimiter = '#';
        $regex     = $delimiter . preg_quote($this->begin_delimiter, $delimiter)
                . '(.*?)'
                . preg_quote($this->end_delimeter, $delimiter)
                . $delimiter
                . 's';
        preg_match_all($regex, $text, $matches);

        $return = array();

        foreach ($matches[1] as $key) {
            $return[$key] = (isset($return[$key])) ? $return[$key]++ : 1;
        }

        return $return;
    }

    public function checkText($text) {
        $text_keys = $this->getKeysInText($text);
        $errors    = array();

        //check required keys
        foreach ($this->template_vars as $key => $template_var) {
            if ($template_var->isRequired()) {
                if (!array_key_exists($key, $text_keys)) {
                    array_push($errors, "Template var [$key] is required.");
                }
            }
        }

        if (count($errors) > 0) {
            return $errors;
        }
        return true;
    }

    public function substituteText($text, $partial_substitute_ok = false) {
        $text_keys = $this->getKeysInText($text);

        foreach ($text_keys as $key => $count) {

			try {
				$template_var = $this->getTemplateVar($key);
				$substitute   = $template_var->getSubstituteValue($this);
				$text         = str_replace($this->begin_delimiter . $key . $this->end_delimeter, $substitute, $text);
			} catch(Exception $e) {
				if(!$partial_substitute_ok) {
					//rethrow
					throw $e;
				}
			}

        }

        //Check that all vars were substituted
        $keys = $this->getKeysInText($text);
        if (count($keys) > 0) {

            //its possible that a template var was injected. we can see if we have a template_var for it:
            $more_tags_found = false;
            foreach ($keys as $key => $count) {
				//only want to do this if value is set so we don't have an infinite loop
                if ($this->hasTemplateVar($key) && $this->hasValue($key)) {
                    $more_tags_found = true;
                    break;
                }
            }
            if ($more_tags_found) {
                return $this->substituteText($text,$partial_substitute_ok);
            } else {
				if(!$partial_substitute_ok) {
					$unsubstituted = implode(', ', array_keys($keys));
					throw new Exception("There are unsubstituted vars. These need to be set in addTemplateVarDefs: [$unsubstituted]");
				}
            }
        }

        return $text;
    }

}

class TemplateVar {

    protected $template_tag;
    protected $substitute_empty; //what to replace if value not set or value is empty. set to false to error
    protected $substitute_value; //optional - what to replace instead of value - if value not empty
    protected $empty_check; //optional - alternate key to check if empty (can be string or array)

    public function __construct($template_tag, $substitute_empty = '', $substitute_value = null, $empty_check = null) {
        $this->template_tag     = $template_tag;
        $this->substitute_empty = $substitute_empty;
        $this->substitute_value = $substitute_value;
        $this->empty_check      = $empty_check;
    }

    public function isRequired() {
        return $this->substitute_empty === false;
    }

    /**
     *
     * @param string $value
     * @param TemplateVars $template_vars needed for recursive subsctituteText if substitute value is set
     * @return type
     * @throws Exception
     */
    public function getSubstituteValue(TemplateVars $template_vars) {
        $empty_check = $this->template_tag;

        if (!empty($this->empty_check)) {
            $empty_check = $this->empty_check;
        }
var_dump($empty_check);
        $is_empty = $template_vars->isEmpty($empty_check);

        if ($is_empty && empty($this->substitute_value)) {
            if ($this->isRequired()) {
                if (is_array($empty_check)) {
                    $empty_check = implode(', ', $empty_check);
                }
                throw new Exception("Key {$empty_check} is required. Need to set value in TemplateVars");
            }
            return $this->substitute_empty;
        }

        if (!empty($this->substitute_value)) {
            return $template_vars->substituteText($this->substitute_value);
        }

        return $template_vars->getValue($this->template_tag);
    }

}
