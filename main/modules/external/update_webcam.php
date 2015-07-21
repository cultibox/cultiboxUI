<?php 


        if((isset($_GET['id']))&&(!empty($_GET['id']))) {
            $id=$_GET['id'];
        } else {
            $id=0;
        }


        if((isset($_GET['auto_brightness']))&&(!empty($_GET['auto_brightness']))&&(strcmp($_GET['auto_brightness'],"true")==0)) {
            $brightness="-1";
        } else if((isset($_GET['brightness']))&&(!empty($_GET['brightness']))) {
            $brightness=$_GET['brightness'];
        } else {
            $brightness="-1";
        }

        if((isset($_GET['auto_contrast']))&&(!empty($_GET['auto_contrast']))&&(strcmp($_GET['auto_contrast'],"true")==0)) {
            $contrast="-1";
        } else if((isset($_GET['contrast']))&&(!empty($_GET['contrast']))) {
            $contrast=$_GET['contrast'];
        } else {
            $contrast="-1";
        }


        if((isset($_GET['resolution']))&&(!empty($_GET['resolution']))) {
            $resolution=$_GET['resolution'];
        } else {
            $resolution="640x480";
        }

        if((isset($_GET['title']))&&(!empty($_GET['title']))) {
            $title=$_GET['title'];
        } else {
            $title="Webcam ".$id;
        }

        $conf=array();

        $conf[]="DEVICE=/dev/video".$id;
        $conf[]="RESOLUTION=".$resolution;
        if(strcmp("$brightness","-1")!=0) {
            $conf[]="BRIGHTNESS=".$brightness;
        }

        if(strcmp("$contrast","-1")!=0) {
            $conf[]="CONTRAST=".$contrast;
        }

        $conf[]="TITLE=\"".$title."\"";
        
        if($f=fopen("/tmp/webcam".$id.".conf","w")) {
            foreach($conf as $myInf) {
                fputs($f,"$myInf\n");
            }   
            fclose($f);

            exec("sudo mv /tmp/webcam".$id.".conf /etc/culticam/",$output,$err);
        }
        
        exec("sudo chown -R cultipi:cultipi /etc/culticam",$output,$err);
        echo json_encode("0");
?>
