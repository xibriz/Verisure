<?php
//Include config
//require_once './config/default.config.php';
require_once './config/ruben.config.php';

$verisureLogonObj = new verisureLogon();
if ($verisureLogonObj->runLogon()) {
    echo "LOGON OK<br><br>";
} else {
    echo "Could not log in! Check the error log.<br><br>";
}
if ($verisureLogonObj->isLoggedIn()) {
    echo "You are logged in<br><br>";
} else {
    echo "You are NOT logged in. Check the error log. Aborting script...<br><br>";
    exit;
}

/**
 * Remote Control
 */
$verisureRemoteControlObj = new verisureRemoteControl();
if ($verisureRemoteControlObj->isAlarmOff()) {
    echo "Alarm is OFF<br><br>";
} else {
    echo "Alarm is ON or we don't know.<br><br>";
}
echo 'Remote status:<br>';
var_dump($verisureRemoteControlObj->getRemoteStatus());

/**
 * Clima
 */
$verisureClimaObj = new verisureClima();
echo 'Clima status:<br>';
var_dump($verisureClimaObj->getClimaStatus());

/**
 * Smart Plug
 */
$verisureSmartPlugObj = new verisureSmartPlug();
echo 'Smart Plug status:<br>';
var_dump($verisureSmartPlugObj->getSmartPlugStatus());