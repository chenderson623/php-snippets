<?php

require_once GLOBAL_LIB_PATH . '/Email/EmailContent.php';

$email_template = new EmailContent();

$subject = "Specials for --|site_loc_name|--";
$text_body = <<<TEXT
This is the text body for --|site_loc_name|--

--|facebook_link|--
TEXT;
$html_body = <<<HTML
This is the text body for --|site_loc_name|--

--|facebook_link|--
HTML;


$template_var_defs = array(
    'site_id'       => array('substitute_empty'=>false),
    'site_loc_name' => array('substitute_empty'=>false),
    'facebook_link' => array('substitute_empty'=>'', 'substitute_value'=>"<a href=\"--|facebook_url|--\">Facebook</a>",'empty_check'=>'facebook_url')
);
$start_delimiter = '--|';
$end_delimiter   = '|--';

$email_template->setEmailSubject($subject);
$email_template->setEmailBodyPlainText($text_body);
$email_template->setEmailBodyHtml($html_body);
$email_template->setDelimiters($start_delimiter, $end_delimiter);

$template_vars = $email_template->getTemplateVars();

var_dump($template_var_defs);

$template_vars->addTemplateVarDefs($template_var_defs);

$values = array(
    'site_id' => 1234,
    'site_loc_name' => 'Store Name',
    'facebook_url' => 'facebook.com/4357634785634786/'
);

$template_vars->addValues($values);

var_dump($email_template->getRawEmailSubject());
var_dump($email_template->getEmailSubject());
var_dump($email_template->getEmailBodyPlainText());
var_dump($email_template->getEmailBodyHtml());