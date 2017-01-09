<?php
/**
 * Class to handle Smart Plug devices
 *
 * @author Ruben Andreassen (rubean85@gmail.com)
 */
class verisureSmartPlug extends verisure {
    
    private static $STATUS_ON = "on";
    private static $STATUS_OFF = "off";
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Retrieve the status of the alarm and locks
     * 
     * @return stdClass/false
     */
    public function getSmartPlugStatus() {
        curl_setopt($this->ch, CURLOPT_URL, projectConfig::$VERISURE_URL_BASE_PATH."settings/smartplug");
        $result = curl_exec($this->ch);
        
        $resultJSON = json_decode($result);
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
            $result = ($obj->status === self::$STATUS_ON);
            break;
        }
        return $result;
    }
    
    /**
     * Turn a spesific Smart Plug ON
     * 
     * @param string $id
     * @return string
     */
    public function turnOn($id) {
        $url = projectConfig::$VERISURE_URL_BASE_PATH . "smartplugs/onoffplug.cmd";
        $paramsArray = array(
            "targetDeviceLabel" => str_replace(" ", "+", $id),
            "targetOn" => self::$STATUS_ON
        );
        
        return $this->urlPOST($url, $paramsArray);
    }
    
    /**
     * Turn a spesific Smart Plug OFF
     * 
     * @return string
     */
    public function turnOff($id) {
        $url = projectConfig::$VERISURE_URL_BASE_PATH . "smartplugs/onoffplug.cmd";
        $paramsArray = array(
            "targetDeviceLabel" => str_replace(" ", "+", $id),
            "targetOn" => self::$STATUS_OFF
        );
        if (isset($_GET['debug'])) {
            echo $url;
            var_dump($paramsArray);
        }
        
        return $this->urlPOST($url, $paramsArray);
    }
}
