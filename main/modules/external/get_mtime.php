<?php

require_once('../../libs/utilfunc.php');
require_once('../../libs/config.php');

$file=$_GET['file'];

date_default_timezone_set('Europe/Paris');

echo json_encode(__('LAST_WEBCAM')." : ".date('d-m-Y H:i:s', filemtime("${file}")));

?>
