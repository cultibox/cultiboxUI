<?php 

$err="";
$output=array();

if((isset($_GET['file']))&&(!empty($_GET['file']))) {
    $file="../../../tmp/import/".$_GET['file'];
} else {
    $file="";
}

exec("sudo dpkg -i --force-confdef --force-confold \"$file\"",$output,$err);
echo json_encode("$err");

?>
