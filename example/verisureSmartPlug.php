<?php
/*
 * Verisure/example/verisureSmartPlug.php?task=on&id=XXXX+XXXX&debug=1
 * Verisure/example/verisureSmartPlug.php?task=off&id=XXXX+XXXX&debug=1
 * Verisure/example/verisureSmartPlug.php?task=status&id=XXXX+XXXX&debug=1
 */

//Include config
require_once __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'default.config.php';

$debug = isset($_GET['debug']);

$verisureSmartPlugObj = new verisureSmartPlug();
switch (filter_input(INPUT_GET, 'task')) {
    case 'on':
        $result = $verisureSmartPlugObj->turnOn(filter_input(INPUT_GET, 'id'));
        if ($debug) {
            echo "ON<br>";
            echo $result;
        }
        sleep(3);
        echo ($verisureSmartPlugObj->isSmartPlugOn(filter_input(INPUT_GET, 'id')) === true) ? 'on' : 'off';
        break;
    case 'off':
        $result = $verisureSmartPlugObj->turnOff(filter_input(INPUT_GET, 'id'));
        if ($debug) {
            echo "OFF<br>";
            echo $result;
        }
        sleep(3);
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