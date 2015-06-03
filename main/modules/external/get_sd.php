<?php

require_once('../../libs/utilfunc_sd_card.php');

$sd_card="";
$sd_card=get_sd_card();

echo json_encode($sd_card);

?>
