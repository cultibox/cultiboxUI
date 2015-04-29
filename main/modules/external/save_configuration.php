<?php

require_once('../../libs/config.php');
require_once('../../libs/utilfunc.php');
require_once('../../libs/db_get_common.php');
require_once('../../libs/db_set_common.php');

$main_error=array();

$parttosave     = getvar("parttosave");
$EMAIL_PROVIDER = getvar("EMAIL_PROVIDER");
$EMAIL_SMTP     = getvar("EMAIL_SMTP");
$EMAIL_PORT     = getvar("EMAIL_PORT");
$EMAIL_ADRESS   = getvar("EMAIL_ADRESS");
$EMAIL_PASSWORD = getvar("EMAIL_PASSWORD");

// Regulation is not available for lamp and other
switch ($parttosave) 
{
    case "email":
    
        $toSave = array(
            "EMAIL_PROVIDER" => $EMAIL_PROVIDER,
            "EMAIL_SMTP"     => $EMAIL_SMTP,
            "EMAIL_PORT" => $EMAIL_PORT,
            "EMAIL_ADRESS" => $EMAIL_ADRESS,
            "EMAIL_PASSWORD" => $EMAIL_PASSWORD
        );

        configuration\saveEmailUserConf($toSave);
        break;
    default:
        break;
}



?>
