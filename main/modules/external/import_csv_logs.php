<?php

require_once('../../libs/config.php');


if((isset($_GET['filename']))&&(!empty($_GET['filename']))) {
    if((isset($_GET['table']))&&(!empty($_GET['table']))) {
        $table=$_GET['table'];    
    } else {
        $table="logs";
    }

    $file="../../../tmp/import/".$_GET['filename'];
    if(is_file("$file")) {
        exec("sed '1d' '$file' > /tmp/import.tmp && mv /tmp/import.tmp '$file'",$output,$err);
        exec("/usr/bin/mysql --defaults-extra-file=/var/www/cultibox/sql_install/my-extra.cnf -h 127.0.0.1 --port=3891 cultibox -e \"LOAD DATA LOCAL INFILE '$file' INTO TABLE cultibox.$table;\"",$output,$err);
        if($err!=0) {
             echo json_encode("1");
             return 0;
         } else {
             echo json_encode("0");
             return 0;
         }
    }
}
echo json_encode("1");

?>
