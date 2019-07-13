<?php

require_once GLOBAL_LIB_PATH . '/Template/TemplateVars.php';

class EmailContent {
    protected $subject   = '';
    protected $text_body = '';
    protected $html_body = '';
    protected $template_var_defs = array();
    protected $start_delimiter = '--|';
    protected $end_delimiter   = '|--';

    /**
     * @var TemplateVars
     */
    private $template_vars;
    private $substituted_subject;
    private $substituted_text_body;
    private $substituted_html_body;

    public function setDelimiters($start,$end) {
        $this->start_delimiter = $start;
        $this->end_delimiter   = $end;
    }

    public function setEmailSubject($subject) {
        $this->subject = $subject;
    }
    public function getRawEmailSubject() {
        return $this->subject;
    }
    public function getEmailSubject() {
        if(!isset($this->substituted_subject)) {
            $template_vars = $this->getTemplateVars();
            $this->substituted_subject = $template_vars->substituteText($this->getRawEmailSubject());
        }
        return $this->substituted_subject;
    }

    public function setEmailBodyPlainText($text_body) {
        $this->text_body = $text_body;
    }
    public function getRawEmailBodyPlainText() {
        return $this->text_body;
    }
    public function getEmailBodyPlainText() {
        if(!isset($this->substituted_text_body)) {
            $template_vars = $this->getTemplateVars();
            $this->substituted_text_body = $template_vars->substituteText($this->getRawEmailBodyPlainText());
        }
        return $this->substituted_text_body;
    }

    public function setEmailBodyHtml($html_body) {
        $this->html_body = $html_body;
    }
    public function getRawEmailBodyHtml() {
        return $this->html_body;
    }
    public function getEmailBodyHtml() {
        if(!isset($this->substituted_html_body)) {
            $template_vars = $this->getTemplateVars();
            $this->substituted_html_body = $template_vars->substituteText($this->getRawEmailBodyHtml());
        }
        return $this->substituted_html_body;
    }

    public function setTemplateVars(TemplateVars $template_vars) {
        $this->template_vars = $template_vars;
    }

    public function setTemplateVarDefs($template_var_defs) {
	    $this->template_var_defs = $template_var_defs;
    }

    public function reset() {
	unset($this->substituted_html_body);
	unset($this->substituted_subject);
	unset($this->substituted_text_body);
    }

    /**
     * @return TemplateVars
     */
    public function getTemplateVars() {
        if(!isset($this->template_vars)) {
            $this->template_vars = new TemplateVars($this->start_delimiter,$this->end_delimiter);
            $this->template_vars->addTemplateVarDefs($this->template_var_defs);
        }
        return $this->template_vars;
    }
    public function addValue($key, $value) {
        $this->getTemplateVars()->addValue($key, $value);
    }
    public function addValues(array $values) {
        $this->getTemplateVars()->addValues($values);
    }

}
