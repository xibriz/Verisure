<?php
//Include config
require_once './config/ruben.config.php';
//Include Library
require_once './lib/verisure.class.php';
//Include Library Extension
require_once './ext/iRobot.class.php';

$verisureLogonObj = new verisureLogon();
if ($verisureLogonObj->runLogon()) {
    sleep(2); //Wait a little
} else {
    error_log("Could not log in to the Verisure Portal");
    exit;
}

$verisureRemoteControlObj = new verisureRemoteControl();
//If alarm not of = alarm is on
if (!$verisureRemoteControlObj->isAlarmOff()) {
    //Start the iRobot Roomba
    $iRobotObj = new iRobot();
    $iRobotObj->start();
    
    //Turn the light off
    //TODO
} 
//Alarm is off
else {
    //Dock the iRobot Roomba
    $iRobotObj = new iRobot();
    $iRobotObj->dock();
    
    //Turn the light on
    //TODO
}