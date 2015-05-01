<?php

require_once('../../libs/config.php');
require_once('../../libs/config.php');
require_once($GLOBALS['BASE_PATH'].'main/libs/db_get_common.php');
require_once($GLOBALS['BASE_PATH'].'main/libs/db_set_common.php');
require_once($GLOBALS['BASE_PATH'].'main/libs/debug.php');
require_once $GLOBALS['BASE_PATH'].'main/libs/utilfunc.php';
require_once($GLOBALS['BASE_PATH'].'main/libs/utilfunc_sd_card.php');

ob_start();

include $GLOBALS['BASE_PATH'].'main/scripts/plugs.php';
include $GLOBALS['BASE_PATH'].'main/templates/plugs.html';


$include = ob_get_clean();
echo "$include";

?>
