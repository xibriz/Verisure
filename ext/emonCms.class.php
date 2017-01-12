<?php

/**
 * Extension to log Clima values (temperature and humidity) to EmonCMS
 *
 * @author Ruben Andreassen (rubean85@gmail.com)
 */
class emonCms extends verisureClima {
    
    private $debug = false;

    public function __construct($debug) {
        parent::__construct();
        $this->debug = $debug;
    }

    /**
     * Get all values of all clima devices and log them via EmonCMS API
     * (Uses the Graph page)
     * 
     * @return boolean
     */
    public function logAll() {
        $climaArray = $this->getClimaGraphValues();
        if (!$this->validateClimaResult($climaArray)) {
            return false;
        }
        foreach ($climaArray as $sensorObj) {
            foreach ($sensorObj->samples as $statusObj) {
                $this->logValue($this->buildUrl($statusObj->timestamp, $sensorObj->serial, $sensorObj->climateSensorType, $statusObj->value));
            }
        }
        return true;
    }

    /**
     * Get the current values of all clima devices and log them via EmonCMS API
     * (Uses the Overview page of Verisure. These statuses lacks a proper timestamp value)
     * 
     * @return boolean
     */
    public function logCurrent() {
        $climaArray = $this->getClimaStatus();
        if (!$this->validateClimaResult($climaArray)) {
            return false;
        }
        foreach ($climaArray as $obj) {
            if (strlen($obj->humidity) > 0) {
                $this->logValue($this->buildUrl($obj->timestamp, $obj->id, "humidity", str_replace(",", ".", rtrim($obj->humidity, "%"))));
            }
            if (strlen($obj->temperature) > 0) {
                $this->logValue($this->buildUrl($obj->timestamp, $obj->id, "temperature", str_replace("&#176;", "", str_replace(",", ".", $obj->temperature))));
            }
        }
        return true;
    }

    /**
     * Get the last values of all clima devices and log them via EmonCMS API
     * (Uses the Graph page)
     * 
     * @return boolean
     */
    public function logLast() {
        $climaArray = $this->getClimaGraphValues();
        if (!$this->validateClimaResult($climaArray)) {
            return false;
        }
        foreach ($climaArray as $sensorObj) {
            $statusObj = array_pop($sensorObj->samples);
            $this->logValue($this->buildUrl($statusObj->timestamp, $sensorObj->serial, $sensorObj->climateSensorType, $statusObj->value));
        }
        return true;
    }

    /**
     * Validate the clima result
     * 
     * @param array $climaArray
     * @return boolean
     */
    private function validateClimaResult($climaArray) {
        if ($climaArray === false) {
            error_log("ERROR! Not JSON result");
            return false;
        }
        if (count($climaArray) === 0) {
            error_log("ERROR! Empty array");
            return false;
        }
        return true;
    }

    /**
     * Helper function, build the URL to EmonCMS API
     * 
     * @param int $timestamp
     * @param string $id
     * @param string $type
     * @param string $value
     * @return string
     */
    private function buildUrl($timestamp, $id, $type, $value) {
        return emoncmsConfig::$EMONCMS_URL_BASE_PATH . emoncmsConfig::$EMONCMS_URL_API_WITH_KEY . "&time=" . substr($timestamp, 0, 10) . "&node=" . str_replace(" ", "_", $id) . "&json={%22" . $type . "%22:%22" . $value . "%22}";
    }

    /**
     * Log the one value from Verisure clima to EmonCMS
     * 
     * @param string $url
     */
    private function logValue($url) {
        if ($this->debug) {
            echo $url . "<br>";
        } else {
            //TODO: check if response is "ok"
            $this->urlGET($url);
        }
    }

}
