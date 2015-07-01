<?php 


        if((isset($_GET['id']))&&(!empty($_GET['id']))) {
            $id=$_GET['id'];
        } else {
            $id=0;
        }

        if((isset($_GET['palette']))&&(!empty($_GET['palette']))) {
            $palette=$_GET['palette'];
        } else {
            $palette="MJPEG";
        }

        if((isset($_GET['auto_brightness']))&&(!empty($_GET['auto_brightness']))&&(strcmp($_GET['auto_brightness'],"true")==0)) {
            $brightness="-1";
        } else if((isset($_GET['brightness']))&&(!empty($_GET['brightness']))) {
            $brightness=$_GET['brightness'];
        } else {
            $brightness="0";
        }

        if((isset($_GET['auto_contrast']))&&(!empty($_GET['auto_contrast']))&&(strcmp($_GET['auto_contrast'],"true")==0)) {
            $contrast="-1";
        } else if((isset($_GET['contrast']))&&(!empty($_GET['contrast']))) {
            $contrast=$_GET['contrast'];
        } else {
            $contrast="0";
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

        $conf[]="device /dev/video".$id;
        $conf[]="resolution ".$resolution;
        if(strcmp("$brightness","-1")!=0) {
            $conf[]="set brightness=".$brightness."%";
        }

        if(strcmp("$contrast","-1")!=0) {
            $conf[]="set contrast=".$contrast."%";
        }

        $conf[]="skip 10";
        $conf[]="title \"".$title."\"";
        $conf[]="top-banner";
        $conf[]="font /usr/share/fonts/truetype/msttcorefonts/arial.ttf";
        $conf[]="timestamp \"%d-%m-%Y %H:%M:%S\"";
        $conf[]="save /var/www/cultibox/tmp/webcam${id}.jpg";
        if(strcmp("$palette","AUTO")!=0) {
            $conf[]="palette $palette";
        }

        if($f=fopen("/tmp/webcam".$id.".conf","w")) {
            foreach($conf as $myInf) {
                fputs($f,"$myInf\n");
            }   
            fclose($f);

            exec("sudo mv /tmp/webcam".$id.".conf /etc/culticam/",$output,$err);
        }

        $nb=$id+1;
        $dim=explode("x",$resolution);
        switch($palette) {
            case 'MJPEG': $pal=2;
                 break;
            case 'JPEG': $pal=4;
                 break;
            case 'RGB32': $pal=4;
                 break;
            case 'UYVY': $pal=5;
                 break;
            case 'YUYV': $pal=6;
                 break;
            default:
                 $pal=8;
        }

        $thread[]="videodevice /dev/video".$id;
        $thread[]="v4l2_palette $pal";
        $thread[]="input 8";
        $thread[]="text_left ".$title;
        $thread[]="target_dir /var/www/cultibox/tmp";
        $thread[]="webcam_port 808".$nb;

        if(($dim[0] % 16 != 0) || ($dim[1] % 16 != 0)) {
            $dim[0]="640";
            $dim[1]="480";
        }
        
        $thread[]="width ".$dim[0];
        $thread[]="height ".$dim[1];
    
        if(strcmp("$brightness","-1")!=0) {
            $brightness=intval($brightness*100/255);
            $thread[]="brightness $brightness";
        }


        if(strcmp("$contrast","-1")!=0) {
           $contrast=intval($contrast*100/255); 
           $thread[]="contrast $contrast";
        }

        $thread[]="text_right %d-%m-%Y\\n%T"; 

    

        if($f=fopen("/tmp/thread".$nb.".conf","w")) {
            foreach($thread as $myInf) {
                fputs($f,"$myInf\n");
            }
            fclose($f);

            exec("sudo mv /tmp/thread".$nb.".conf /etc/culticam/",$output,$err);
        }

        exec("sudo chown -R cultipi:cultipi /etc/culticam",$output,$err);
        echo json_encode("0");
?>
