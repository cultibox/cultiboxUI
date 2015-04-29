<?php

$GLOBALS['PLUGIN_irrigation']['TRACE_LEVEL'] = "debug";

$GLOBALS['PLUGIN_irrigation']['localtechnique'] = array(
    'nom' => "localtechnique",
    'ip' => '192.168.0.53',
    'irriActive' => "true",
    'pompe' => array(
        "nom" => "Surpresseur",
        "prise" => 2
    ),
    'engrais' => array (
        '0' => array (
            "nom" => "engrais1",
            "prise" => 3,
            "active" => "true"
        ),
        '1' => array (
            "nom" => "engrais2",
            "prise" => 4,
            "active" => "true"
        ),
        '2' => array (
            "nom" => "engrais3",
            "prise" => 5,
            "active" => "true"
        ),
        '3' => array (
            "nom" => "eau",
            "prise" => 6,
            "active" => "false"
        )
    ),
);

$GLOBALS['PLUGIN_irrigation']['plateforme'][0] = array(
    'nom' => "montmartre",
    'ip' => '192.168.0.50',
    "active" => "true",
    'limitDesamorcagePompe'  => "false",
    'tempsPerco' => '300',
    'tempsMaxRemp' => '300',
    "priseDansLT" => 7,
    'pompe' => array(
        "nom" => "pompe",
        "prise" => 1
    ),
    'zone' => array (
        '0' => array (
            "nom" => "EV_sud",
            "prise" => 2,
            "tempsOn" => 100,
            "tempsOff" => 150,
            "active" => "true",
            "coef" => 1.0
        ),
        '1' => array (
            "nom" => "EV_milieu",
            "prise" => 3,
            "tempsOn" => 100,
            "tempsOff" => 150,
            "active" => "true",
            "coef" => 1.0
        ),
        '2' => array (
            "nom" => "EV_nord",
            "prise" => 4,
            "tempsOn" => 100,
            "tempsOff" => 150,
            "active" => "true",
            "coef" => 1.0
        )
    )
);

$GLOBALS['PLUGIN_irrigation']['plateforme'][1] = array(
    'nom' => "dantin",
    'ip' => '192.168.0.54',
    "active" => "false",
    'limitDesamorcagePompe'  => "false",
    'tempsPerco' => '300',
    'tempsMaxRemp' => '300',
    "priseDansLT" => 8,
    'pompe' => array(
        "nom" => "pompe",
        "prise" => 1
    ),
    'zone' => array (
        '0' => array (
            "nom" => "EV_nord",
            "prise" => 2,
            "tempsOn" => 100,
            "tempsOff" => 150,
            "active" => "true",
            "coef" => 1.0
        ),
        '1' => array (
            "nom" => "EV_sud",
            "prise" => 3,
            "tempsOn" => 100,
            "tempsOff" => 150,
            "active" => "true",
            "coef" => 1.0
        )
    )
);

$GLOBALS['PLUGIN_irrigation']['plateforme'][2] = array(
    'nom' => "centrale",
    'ip' => '192.168.0.55',
    "active" => "false",
    'limitDesamorcagePompe'  => "false",
    'tempsPerco' => '300',
    'tempsMaxRemp' => '300',
    "priseDansLT" => 9,
    'pompe' => array(
        "nom" => "pompe",
        "prise" => 1
    ),
    'zone' => array (
        '0' => array (
            "nom" => "EV",
            "prise" => 2,
            "tempsOn" => 100,
            "tempsOff" => 150,
            "active" => "true",
            "coef" => 1.0
        )
    )
);

$GLOBALS['PLUGIN_irrigation']['plateforme'][3] = array(
    'nom' => "mogador",
    'ip' => '192.168.0.51',
    "active" => "false",
    'limitDesamorcagePompe'  => "false",
    'tempsPerco' => '300',
    'tempsMaxRemp' => '300',
    "priseDansLT" => 10,
    'pompe' => array(
        "nom" => "pompe",
        "prise" => 1
    ),
    'zone' => array (
        '0' => array (
            "nom" => "EV_nord",
            "prise" => 2,
            "tempsOn" => 100,
            "tempsOff" => 150,
            "active" => "true",
            "coef" => 1.0
        ),
        '1' => array (
            "nom" => "EV_sud",
            "prise" => 3,
            "tempsOn" => 100,
            "tempsOff" => 150,
            "active" => "true",
            "coef" => 1.0
        )
    )
);

$GLOBALS['PLUGIN_irrigation']['plateforme'][4] = array(
    'nom' => "opera",
    'ip' => '192.168.0.52',
    "active" => "false",
    'limitDesamorcagePompe'  => "false",
    'tempsPerco' => '300',
    'tempsMaxRemp' => '300',
    "priseDansLT" => 11,
    'pompe' => array(
        "nom" => "pompe",
        "prise" => 1
    ),
    'zone' => array (
        '0' => array (
            "nom" => "EV_1",
            "prise" => 2,
            "tempsOn" => 100,
            "tempsOff" => 150,
            "active" => "true",
            "coef" => 1.0
        ),
        '1' => array (
            "nom" => "EV_2",
            "prise" => 3,
            "tempsOn" => 100,
            "tempsOff" => 150,
            "active" => "true",
            "coef" => 1.0
        ),
        '2' => array (
            "nom" => "EV_3",
            "prise" => 4,
            "tempsOn" => 100,
            "tempsOff" => 150,
            "active" => "true",
            "coef" => 1.0
        )
    )
);

?>
