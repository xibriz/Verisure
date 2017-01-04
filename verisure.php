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
    sleep(2); //Wait a little
} else {
    if ($debug) {
        echo 'Could not log in to the Verisure Portal';
    }
    error_log("Could not log in to the Verisure Portal");
    exit;
}

$verisureRemoteControlObj = new verisureRemoteControl();
//If alarm not of = alarm is on
$alarmStatus = $verisureRemoteControlObj->isAlarmOff();
if ($alarmStatus === false) {
    if ($debug) {
        echo 'Alarm is On.. Starting Roomba';
    }
    //Start the iRobot Roomba
    $iRobotObj = new iRobot();
    $iRobotObj->start();
    
    //Turn the light off
    //TODO
} 
//Alarm is off
else if ($alarmStatus === true) {
    if ($debug) {
        echo 'Alarm is Off.. Docking Roomba';
    }
    //Dock the iRobot Roomba
    $iRobotObj = new iRobot();
    $iRobotObj->dock();
    
    //Turn the light on
    //TODO
} else {
    if ($debug) {
        echo 'Unknown Alarm Status';
    }
}