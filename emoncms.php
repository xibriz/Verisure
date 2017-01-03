<?php
//Include config
require_once './config/ruben.config.php';
//Include Library
require_once './lib/verisure.class.php';
//Include Library Extension
require_once './ext/emonCms.class.php';

switch (filter_input(INPUT_GET, 'type')) {
    case 'ALL':
        allValues();
        break;
    case 'LAST':
        lastValues();
        break;
    case 'CURRENT':
        currentValues();
        break;
    default:
        echo "ERROR! Unknown type. Valid types are ALL, LAST and CURRENT";
        exit;
}

/**
 * Get all values of all clima devices
 * (Uses the Graph page)
 */
function allValues() {
    runLogon();
    $emonCmsObj = new emonCms(true);
    $emonCmsObj->logAll();
}

/**
 * Get the last values of all clima devices
 * (Uses the Graph page)
 */
function lastValues() {
    runLogon();
    $emonCmsObj = new emonCms(true);
    $emonCmsObj->logLast();
}

/**
 * Get the current values of all clima devices
 * (Uses the Overview page of Verisure. These statuses lacks a proper timestamp value)
 */
function currentValues() {
    runLogon();
    $emonCmsObj = new emonCms(true);
    $emonCmsObj->logCurrent();
}

/**
 * Helper function, log in to Verisure
 */
function runLogon() {
    $verisureLogonObj = new verisureLogon();
    if (!$verisureLogonObj->runLogon()) {
        echo "ERROR! could not log in";
        exit;
    } else {
        sleep(2);
    }
}
