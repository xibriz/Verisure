<?php

/**
 * Class with personal configuration (passwords, API keys etc.)
 *
 * @author Ruben Andreassen (rubean85@gmail.com)
 */
class projectConfig {
    /**
     * Verisure
     * ==========================================================
     */
    public static $VERISURE_URL_BASE_PATH = "https://mypages.verisure.com/";
    public static $VERISURE_TMP_FILE_PATH = "/tmp";
    /**
     * Verisure Login credentials
     */
    public static $VERISURE_USERNAME = "your@mail.com";
    public static $VERISURE_PASSWORD = "XXXX";

    /**
     * Run verisureRemoteControl->getRemoteStatus() to find info to fill out here
     */
    public static $VERISURE_ALARM_ID = 1;
    //Yale Doorman
    public static $VERISURE_DEVICE_LOCK = array(
        //0 => array("id" => "XXXXXXXX", "name" => "Lås 1"),
        //1 => array("id" => "XXXXXXXX", "name" => "Lås 2")
        //2 => ...
    );

    /**
     * Run verisureClima->getClimaStatus() to find info to fill out here
     */
    //Devices with temperature and/or humidity
    public static $VERISURE_DEVICE_CLIMA = array(
        //0 => array("id" => "XXXX XXXX", "name" => "Røykdetektor 1"),
        //1 => array("id" => "XXXX XXXX", "name" => "Røykdetektor 2")
        //2 => ...
    );
    
    /**
     * Run verisureSmartPlug->getSmartPlugStatus() to find info to fill out here
     */
    public static $VERISURE_DEVICE_SMART_PLUG = array(
        //0 => array("id" => "XXXX XXXX", "name" => "Smart Plug 1"),
        //1 => array("id" => "XXXX XXXX", "name" => "Smart Plug 2"),
        //2 => ...
    );

    /**
     * EmonCMS
     * Requires a EmonCMS server or account on the Cloud service
     */
    public static $EMONCMS_URL_BASE_PATH = "http://localhost/emoncms/";
    public static $EMONCMS_URL_API_WITH_KEY = "input/post.json?apikey=WRITE_API_KEY";
    
    /**
     * iRobot Roomba 980
     * Requires dorita980 and rest980 service running locally
     */
    public static $IROBOT_URL_BASE_PATH = "http://localhost:3000/";
    public static $IROBOT_URL_API_LOCAL_ACTION = "api/local/action/";
}
