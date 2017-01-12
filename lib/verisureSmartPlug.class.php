<?php

/**
 * Class to handle Smart Plug devices
 *
 * @author Ruben Andreassen (rubean85@gmail.com)
 */
class verisureSmartPlug extends verisure {

    private static $STATUS_ON = "on";
    private static $STATUS_OFF = "off";
    private static $STATUS_UPDATING = "updating";

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
        $this->addHeaderX();

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
        $this->addHeaderX();

        return $this->urlPOST($url, $paramsArray);
    }

    /**
     * Add headers needed to do POST against Verisure
     */
    private function addHeaderX() {
        if (file_exists(realpath(verisureConfig::$VERISURE_TMP_FILE_PATH) . DIRECTORY_SEPARATOR . self::$X_CSRF_TOKEN_FILE)) {
            $handle = fopen(realpath(verisureConfig::$VERISURE_TMP_FILE_PATH) . DIRECTORY_SEPARATOR . self::$X_CSRF_TOKEN_FILE, 'r');
            $token = fgets($handle);
            fclose($handle);
            //TODO: check length of X-CSRF-TOKEN
            $this->addHeader(array(
                'Origin: '. rtrim(verisureConfig::$VERISURE_URL_BASE_PATH, "/"),
                'Accept: application/json, text/javascript, */*; q=0.01',
                'Accept-Language: nb-NO,nb;q=0.9,no-NO;q=0.8,no;q=0.6,nn-NO;q=0.5,nn;q=0.4,en-US;q=0.3,en;q=0.1',
                'Accept-Encoding: gzip, deflate, br',
                'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
                'X-Requested-With: XMLHttpRequest',
                'Connection: keep-alive',
                'X-CSRF-TOKEN: ' . $token,
            ));
        } else {
            error_log("Could not locate X-CSRF-TOKEN-file at location " . realpath(verisureConfig::$VERISURE_TMP_FILE_PATH) . DIRECTORY_SEPARATOR . self::$X_CSRF_TOKEN_FILE);
        }
    }

}
