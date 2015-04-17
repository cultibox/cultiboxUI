<?php


$upload_dir="";
$filename="";
if((isset($_GET['upload_dir']))&&(!empty($_GET['upload_dir']))) {
    $upload_dir=$_GET['upload_dir'];
}

if((isset($_GET['filename']))&&(!empty($_GET['filename']))) {
    $filename=$_GET['filename'];
}


if((is_file("../../../tmp/import/$filename"))&&(strcmp("$upload_dir","")!=0)) {
    exec("/bin/mv \"../../../tmp/import/$filename\" ../../libs/img/$upload_dir/",$output,$err);
}

echo "/bin/mv ../../../tmp/import/$filename ../../libs/img/$upload_dir/";
?>
