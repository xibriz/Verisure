<?php
/**
 * Verisure/emoncms.php?type=ALL
 * Verisure/emoncms.php?type=LAST
 * Verisure/emoncms.php?type=CURRENT
 */

//Include config
require_once __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'default.config.php';

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
 * (Uses the Overview page of Verisure. These statuses may lack a proper timestamp value based on your local language)
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
