<?php

require_once GLOBAL_LIB_PATH . '/Email/EmailContent.php';


class EmailContent_Test extends EmailContent {
    protected $subject = "Specials for --|site_loc_name|--";


    protected $text_body = <<<TEXT
This is the text body for --|site_loc_name|--

--|facebook_link|--
TEXT;


    protected $html_body = <<<HTML
This is the text body for --|site_loc_name|--

--|facebook_link|--
HTML;


    protected $template_var_defs = array(
        'site_id'       => array('substitute_empty'=>false),
        'site_loc_name' => array('substitute_empty'=>false),
        'facebook_link' => array('substitute_empty'=>'', 'substitute_value'=>"<a href=\"--|facebook_url|--\">Facebook</a>",'empty_check'=>'facebook_url')
    );
    protected $start_delimiter = '--|';
    protected $end_delimiter   = '|--';

}


$email_template = new EmailContent_Test();

$values = array(
    'site_id' => 1234,
    'site_loc_name' => 'Store Name',
    'facebook_url' => 'facebook.com/4357634785634786/'
);

$email_template->addValues($values);


var_dump($email_template->getRawEmailSubject());
var_dump($email_template->getEmailSubject());
var_dump($email_template->getEmailBodyPlainText());
var_dump($email_template->getEmailBodyHtml());