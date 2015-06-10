<?php


require_once('../../libs/utilfunc_sd_card.php');

if((isset($_GET['path']))&&(!empty($_GET['path']))) {
    $path=$_GET['path'];

    if((isset($_GET['reverse']))&&(!empty($_GET['reverse']))) {
        $reverse=$_GET['reverse'];
    } else {
        $reverse=0;
    }
    if(!copy_firm_sd("$path",$reverse)) {
        echo json_encode("false");
    } else {
        echo json_encode("true");
    }
}
?>
