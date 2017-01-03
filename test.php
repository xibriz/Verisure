<?php
//Include config
require_once './config/ruben.config.php';
//Include Library
require_once './lib/verisure.class.php';

$verisureLogonObj = new verisureLogon();
var_dump($verisureLogonObj->runLogon());
//var_dump($verisureLogonObj->isLoggedIn());


$verisureRemoteControlObj = new verisureRemoteControl();
var_dump($verisureRemoteControlObj->getRemoteStatus());
/*var_dump($verisureRemoteControlObj->isAlarmOff());
 * 
 */

//$verisureClimaObj = new verisureClima();
//var_dump($verisureClimaObj->getClimaStatus());
