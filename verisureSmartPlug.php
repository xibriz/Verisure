<?php

//Include config
require_once './config/ruben.config.php';
//Include Library
require_once './lib/verisure.class.php';
//Include Library Extension
require_once './ext/iRobot.class.php';

$debug = isset($_GET['debug']);

$verisureLogonObj = new verisureLogon();
if ($verisureLogonObj->runLogon()) {
    if ($debug) {
        echo 'Logon OK';
    }
} else {
    if ($debug) {
        echo 'Could not log in to the Verisure Portal';
    }
    error_log("Could not log in to the Verisure Portal");
    exit;
}
//exit;

$verisureSmartPlugObj = new verisureSmartPlug();
switch (filter_input(INPUT_GET, 'task')) {
    case 'on':
        $result = $verisureSmartPlugObj->turnOn(filter_input(INPUT_GET, 'id'));
        if ($debug) {
            echo "ON<br>";
            echo $result;
        }
        echo ($verisureSmartPlugObj->isSmartPlugOn(filter_input(INPUT_GET, 'id')) === true) ? 'on' : 'off';
        break;
    case 'off':
        $result = $verisureSmartPlugObj->turnOff(filter_input(INPUT_GET, 'id'));
        if ($debug) {
            echo "OFF<br>";
            echo $result;
        }
        echo ($verisureSmartPlugObj->isSmartPlugOn(filter_input(INPUT_GET, 'id')) === true) ? 'on' : 'off';
        break;
    case 'status':
        if ($debug) {
            echo "STATUS<br>";
        }
        echo ($verisureSmartPlugObj->isSmartPlugOn(filter_input(INPUT_GET, 'id')) === true) ? 'on' : 'off';
        break;
    default :
        if ($debug) {
            echo 'Unrecognized task';
        }
        error_log("Unrecognized task");
        break;
}