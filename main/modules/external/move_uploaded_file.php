<?php


$upload_dir="";
$filename="";
if((isset($_GET['upload_dir']))&&(!empty($_GET['upload_dir']))) {
    $upload_dir=$_GET['upload_dir'];
}

if((isset($_GET['filename']))&&(!empty($_GET['filename']))) {
    $filename=$_GET['filename'];
}

if((isset($_GET['type']))&&(!empty($_GET['type']))&&(strcmp($_GET['type'],"plug")==0)) {
    $filename=explode("====",$filename); 
    $name=basename($filename[0], ".".substr(strrchr($filename[0],'.'),1));
    $pattern = array(" ", "(", ")");
    $name=str_replace($pattern,"",$name);
    copy("../../../tmp/import/$filename[0]",$upload_dir . "/" . $name . "_ON." . substr(strrchr($filename[0],'.'),1));
    copy("../../../tmp/import/$filename[1]",$upload_dir . "/" . $name . "_OFF." . substr(strrchr($filename[1],'.'),1));
    if(count($filename)==2) {
        if(is_file("../../../tmp/import/$filename[0]")) {
            unlink("../../../tmp/import/$filename[0]");
        }

        if(is_file("../../../tmp/import/$filename[1]")) {
            unlink("../../../tmp/import/$filename[1]");
        }
    } 
} else {
    if((is_file("../../../tmp/import/$filename"))&&(strcmp("$upload_dir","")!=0)) {
        copy("../../../tmp/import/$filename",$upload_dir . "/" . strtolower($filename));
        unlink("../../../tmp/import/$filename");
    }
}

?>
