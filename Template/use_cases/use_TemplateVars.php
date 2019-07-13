<?php

require GLOBAL_LIB_PATH . '/Template/TemplateVars.php';

$template_vars = new TemplateVars('--|','|--');

$template_vars->addTemplateVarDef('site_id', false);
$template_vars->addTemplateVarDef('site_loc_name', false);
$template_vars->addTemplateVarDef('facebook_link', '', "<a href=\"--|facebook_url|--\">Facebook</a>","facebook_url");
$template_vars->addTemplateVarDef('web_width_conditional', '530', '630', 'include_web_controls');


$values = array(
    'site_id' => 1234,
    'site_loc_name' => 'Store Name',
    //'facebook_url' => 'facebook.com/4357634785634786/'
    'facebook_url' => ''
);

$template_vars->addValues($values);

$text = "
    An email for store --|site_id|--

    Welcome to --|site_loc_name|--

    --|facebook_link|--
";

//$template_vars->checkText($text);

print $template_vars->substituteText($text);