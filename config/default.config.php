<?php
/**
 * Autoload classes. Try to locate classes in /lib and /ext
 * ClassName should have a corresponding file named ClassName.class.php in /lib or /ext
 * 
 * @author Ruben Andreassen (rubean85@gmail.com)
 */
spl_autoload_register(function ($class_name) {
    //Look in /lib
    if (file_exists(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."lib".DIRECTORY_SEPARATOR.$class_name.".class.php")) {
        require_once __DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."lib".DIRECTORY_SEPARATOR.$class_name.".class.php";
    } 
    else if (file_exists(__DIR__.DIRECTORY_SEPARATOR.".".DIRECTORY_SEPARATOR."lib".DIRECTORY_SEPARATOR.$class_name.".class.php")) {
        require_once __DIR__.DIRECTORY_SEPARATOR.".".DIRECTORY_SEPARATOR."lib".DIRECTORY_SEPARATOR.$class_name.".class.php";
    } 
    //Look in /ext
    else if (file_exists(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."ext".DIRECTORY_SEPARATOR.$class_name.".class.php")) {
        require_once __DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."ext".DIRECTORY_SEPARATOR.$class_name.".class.php";        
    }
    else if (file_exists(__DIR__.DIRECTORY_SEPARATOR.".".DIRECTORY_SEPARATOR."ext".DIRECTORY_SEPARATOR.$class_name.".class.php")) {
        require_once __DIR__.DIRECTORY_SEPARATOR.".".DIRECTORY_SEPARATOR."ext".DIRECTORY_SEPARATOR.$class_name.".class.php";        
    }
});

/**
 * Class with personal configuration (passwords, API keys etc.)
 *
 * @author Ruben Andreassen (rubean85@gmail.com)
 */
class verisureConfig {
    /**
     * Verisure
     * ==========================================================
     */
    public static $VERISURE_URL_BASE_PATH = "https://mypages.verisure.com/";
    public static $VERISURE_TMP_FILE_PATH = "/tmp";
    /**
     * Verisure Login credentials
     */
    public static $VERISURE_LOCAL = "no"; //Possible values: se, no, fi, fr, dk, frbe, nlbe, nl, uk
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

}

/**
 * Class with personal configuration (passwords, API keys etc.) for EmonCMS
 * 
 * @author Ruben Andreassen (rubean85@gmail.com)
 */
class emoncmsConfig {
    /**
     * Requires a EmonCMS server or account on the Cloud service
     */
    public static $EMONCMS_URL_BASE_PATH = "http://localhost/emoncms/";
    public static $EMONCMS_URL_API_WITH_KEY = "input/post.json?apikey=WRITE_API_KEY";
}

/**
 * Class with personal configuration (passwords, API keys etc.) for iRobot
 * 
 * @author Ruben Andreassen (rubean85@gmail.com)
 */
class irobotConfig {
    /**
     * Requires dorita980 and rest980 service running locally
     */
    public static $IROBOT_URL_BASE_PATH = "http://localhost:3000/";
    public static $IROBOT_URL_API_LOCAL_ACTION = "api/local/action/";
}