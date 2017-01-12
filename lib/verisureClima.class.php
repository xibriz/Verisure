<?php

/**
 * Class to handle clima devices (temperature and humidity from supported devices)
 *
 * @author Ruben Andreassen (rubean85@gmail.com)
 */
class verisureClima extends verisure {
    private static $MAX_DEVICES_IN_GRAPH_REQUEST = 5;

    private static $TYPE_SMOKE_DETECTOR = "Røykdetektor";
    private static $TYPE_SIREN = "Sirene";
    private static $TYPE_WATER_DETECTOR = "Vanndetektor";

    public function __construct() {
        parent::__construct();
    }

    /**
     * Retrieve the current status of all the clima devices
     * 
     * @return array/false
     */
    public function getClimaStatus() {
        curl_setopt($this->ch, CURLOPT_URL, verisureConfig::$VERISURE_URL_BASE_PATH . "overview/climatedevice");
        $result = curl_exec($this->ch);

        $resultJSON = json_decode($result);
        return (json_last_error() === JSON_ERROR_NONE) ? $this->editTimestampOnObject($resultJSON) : false;
    }

    /**
     * Retrieve all the values (1 month) of all devices
     * 
     * @return array/false
     */
    public function getClimaGraphValues() {
        $offset = 0;
        $lenght = self::$MAX_DEVICES_IN_GRAPH_REQUEST;
        $devicesSnArrayLength = count(verisureConfig::$VERISURE_DEVICE_CLIMA);

        if (($offset + $lenght) > $devicesSnArrayLength) {
            $lenght = abs($devicesSnArrayLength - $offset - $lenght);
        }

        $climaResult = json_decode($this->getClimaValues(array_slice(verisureConfig::$VERISURE_DEVICE_CLIMA, $offset, $lenght)));
        if (json_last_error() === JSON_ERROR_NONE) {
            $offset += $lenght;
            while ($offset < $devicesSnArrayLength) {
                if (($offset + $lenght) > $devicesSnArrayLength) {
                    $lenght = abs($devicesSnArrayLength - $offset - $lenght);
                }
                $climaResultTmp = json_decode($this->getClimaValues(array_slice(verisureConfig::$VERISURE_DEVICE_CLIMA, $offset, $lenght)));
                if (json_last_error() === JSON_ERROR_NONE) {
                    $climaResult = array_merge($climaResult, $climaResultTmp);
                }

                $offset += $lenght;
            }
            return $climaResult;
        }
        //Ukjent feil
        else {
            return false;
        }
    }

    /**
     * Helper function to retrieve max number of devices from clima graph
     * 
     * @param array $deviceArray
     * @return JSON
     */
    private function getClimaValues($deviceArray) {

        $url = "start/getclimatedata.cmd?";
        foreach ($deviceArray as $array) {
            $url .= "deviceLabels[]=" . urlencode($array['id']) . "&";
        }

        curl_setopt($this->ch, CURLOPT_URL, verisureConfig::$VERISURE_URL_BASE_PATH . rtrim($url, "&"));
        return curl_exec($this->ch);
    }
    
    /**
     * Edit the timestamp value
     * 
     * @param array $resultJSON
     * @return array
     */
    private function editTimestampOnObject($resultJSON) {
        foreach ($resultJSON as &$obj) {
            $obj->timestamp = $this->convertDateStringToTimestamp($obj->timestamp);
        }
        return $resultJSON;
    }

}
