<!DOCTYPE HTML>
<head>
    <title>Cultibox</title>

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

    <link href="/cultibox/favicon.ico" rel="shortcut icon"/>
    <link rel="stylesheet" href="/cultibox/css/base.css?v=<?=@filemtime('css/base.css')?>" />
    <link rel="stylesheet" href="/cultibox/fonts/opensans.css?v=<?=@filemtime('fonts/opensans.css')?>" />

    <link rel="stylesheet" media="all" type="text/css" href="/cultibox/main/libs/css/cultibox.css?v=<?=@filemtime('main/libs/css/cultibox.css')?>" />
    <link rel="stylesheet" href="/cultibox/main/libs/css/help.css?v=<?=@filemtime('css/help.css')?>" />
</head>

<body id="page" class="page">
    <div id="page-bg">
        <div>
            <div class="wrapper grid-block">
                <header id="header">
                    <div id="headerbar" class="grid-block">
                        <div id="box">
                            <img src="/cultibox/main/libs/img/box.png" alt="">
                        </div>
                                    
                        <a class="logo" href="/cultibox" id="welcome-logo"><img src="/cultibox/main/libs/img/logo_cultibox.png" alt=""></a>    
                    </div>
                </header>
                
                <p class="subtitle_right"><b><i><a href="/cultibox/main/scripts/help.php">Retour au sommaire</a></i></b></p>


                <?php 

                // Ouverture du fichier
                $wiki = "Home";
                if(isset($_GET['wiki']) && !empty($_GET['wiki']))
                {
                    $wiki = $_GET['wiki'];
                }
                 
                        
                require_once('../libs/Michelf/Markdown.inc.php');
                        
                $parser = new Michelf\Markdown;
                $parser->url_filter_func = function ($url) {
                    if (strpos($url, "http") !== false) 
                    {
                        return $url;
                    }
                    else if (strpos($url, "img/") !== false)
                    {
                        return "../cultibox.wiki/" . $url;
                    }
                    else 
                    {
                        return "help.php?wiki=" . $url;
                    }
                 };
                 $my_html = $parser->transform(file_get_contents("../cultibox.wiki/" . $wiki . ".md"));
                        

                 echo $my_html;

                 ?>
                 <p class="subtitle_right"><b><i><a href="/cultibox/main/script/help.php">Retour au sommaire</a></i></b></p>
             </div>
        </div>
    </div>
</body>
</html>


