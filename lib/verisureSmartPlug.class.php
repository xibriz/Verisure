<?php

/**
 * Class to handle Smart Plug devices
 *
 * @author Ruben Andreassen (rubean85@gmail.com)
 */
class verisureSmartPlug extends verisure {

    public static $STATUS_ON = "on";
    public static $STATUS_OFF = "off";
    public static $STATUS_UPDATING = "updating";

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Retrieve the status of the alarm and locks
     * 
     * @return stdClass/false
     */
    public function getSmartPlugStatus() {
        curl_setopt($this->ch, CURLOPT_URL, verisureConfig::$VERISURE_URL_BASE_PATH . "settings/smartplug");
        $result = curl_exec($this->ch);

        $resultJSON = json_decode($result);
        if (isset($_GET['debug'])) {
            var_dump($resultJSON);
        }
        return (json_last_error() === JSON_ERROR_NONE) ? $resultJSON : false;
    }

    /**
     * Return the status of a spesific Smart Plug
     * 
     * true = ON
     * false = OFF
     * null = Don't know
     * 
     * @param string $id
     * @return boolean/null
     */
    public function isSmartPlugOn($id) {
        $id = str_replace("+", " ", $id);
        $statusObj = $this->getSmartPlugStatus();
        if (isset($_GET['debug'])) {
            var_dump($id);
            var_dump($statusObj);
        }
        if ($statusObj === false) {
            return null;
        }
        $result = null;
        foreach ($statusObj as $obj) {
            if ($obj->deviceLabel !== $id) {
                continue;
            }
            if ($obj->status === self::$STATUS_UPDATING) { //If updating, retry
                sleep(3);
                $result = $this->isSmartPlugOn($id);
            } else {
                $result = ($obj->status === self::$STATUS_ON);
            }
            break;
        }
        return $result;
    }
    
    /**
     * Return the status of a soesific Smart Plug as a string
     * 
     * @param string $id
     * @return string on/off/unknown
     */
    public function isSmartPlugOnString($id) {
        $status = $this->isSmartPlugOn($id);
        if ($status === true) {
            return 'on';
        } else if ($status === false) {
            return 'off';
        } else {
            return 'unknown';
        }
    }

    /**
     * Turn a spesific Smart Plug ON
     * 
     * @param string $id
     * @return string
     */
    public function turnOn($id) {
        $url = verisureConfig::$VERISURE_URL_BASE_PATH . "smartplugs/onoffplug.cmd";
        $paramsArray = array(
            "targetDeviceLabel" => $id,
            "targetOn" => self::$STATUS_ON
        );
        $this->addHeaderPOST();
        sleep(1);
        $this->getSmartPlugStatus();
        sleep(1);
        if (isset($_GET['debug'])) {
            var_dump($paramsArray);
        }

        return $this->urlPOST($url, $paramsArray);
    }

    /**
     * Turn a spesific Smart Plug OFF
     * 
     * @return string
     */
    public function turnOff($id) {
        $url = verisureConfig::$VERISURE_URL_BASE_PATH . "smartplugs/onoffplug.cmd";
        $paramsArray = array(
            "targetDeviceLabel" => $id,
            "targetOn" => self::$STATUS_OFF
        );
        $this->addHeaderPOST();
        sleep(1);
        $this->getSmartPlugStatus();
        sleep(1);
        
        return $this->urlPOST($url, $paramsArray);
    }

}
