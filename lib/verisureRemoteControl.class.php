<?php

/**
 * Class to handle alarm status and locks
 *
 * @author Ruben Andreassen (rubean85@gmail.com)
 */
class verisureRemoteControl extends verisure{
    //Yale Doorman
    public static $TYPE_DOOR_LOCK = "DOOR_LOCK";
    public static $STATUS_DOOR_LOCK_LOCKED = "locked"; //Door locked (låst)
    public static $STATUS_DOOR_LOCK_UNLOCKED = "unlocked"; //Door unlocked (åpen)
    public static $STATE_DOOR_LOCK_LOCKED = "LOCKED";
    public static $STATE_DOOR_LOCK_UNLOCKED = "UNLOCKED";
    //Alarm
    public static $TYPE_ALARM_STATE = "ARM_STATE";
    public static $STATUS_ALARM_STATE_UNARMED = "unarmed"; //Alarm unarmed (frakoblet)
    public static $STATUS_ALARM_STATE_ARMEDHOME = "armedhome"; //Alarm armedhome (delsikring)
    public static $STATUS_ALARM_STATE_ARMED = "armed"; //Alarm TODO (tilkoblet)
    public static $STATE_ALARM_STATE_ARMEDHOME = "ARMED_HOME";
    public static $STATE_ALARM_STATE_ARMED = "ARMED_AWAY";
    public static $STATE_ALARM_STATE_UNARMED = "DISARMED";
    
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Retrieve the status of the alarm and locks
     * 
     * @return stdClass/false
     */
    public function getRemoteStatus() {
        curl_setopt($this->ch, CURLOPT_URL, verisureConfig::$VERISURE_URL_BASE_PATH."remotecontrol");
        $result = curl_exec($this->ch);
        
        $resultJSON = json_decode($result);
        return (json_last_error() === JSON_ERROR_NONE) ? $this->addTimestampToObject($resultJSON) : false;
    }
    
    /**
     * Check if a spesific door is locked
     * 
     * true = locked
     * false = unlocked
     * null = don't know
     * 
     * @param string $id
     * @return boolean/null
     */
    public function isDoorLocked($id) {
        $statusObj = $this->getRemoteStatus();
        if ($statusObj === false) {
            return null;
        }
        $isDoorLocked = null;
        foreach ($statusObj as $obj) {
            if ($obj->id !== $id) {
                continue;
            }
            $isDoorLocked = ($obj->status === self::$STATUS_DOOR_LOCK_UNLOCKED) ? false : true;
            break;
        }
        return $isDoorLocked;
    }
    
    /**
     * Check if the alarm is off
     * 
     * true = alarm is off
     * false = alarm is on (full or partially)
     * null = don't know
     * 
     * @return boolean/null
     */
    public function isAlarmOff() {
        $statusObj = $this->getRemoteStatus();
        if ($statusObj === false) {
            return null;
        }
        $isAlarmOff = null;
        foreach ($statusObj as $obj) {
            if ($obj->id !== verisureConfig::$VERISURE_ALARM_ID) {
                continue;
            }
            $isAlarmOff = ($obj->status === self::$STATUS_ALARM_STATE_UNARMED) ? true : false;
            break;
        }
        return $isAlarmOff;
    }
    
    /**
     * Add timestamp to object
     * 
     * @param array $resultJSON
     * @return array
     */
    private function addTimestampToObject($resultJSON) {
        foreach ($resultJSON as &$obj) {
            $obj->timestamp = date("Y-m-d H:i:s", $this->convertDateStringToTimestamp($obj->date));
        }
        return $resultJSON;
    }
    
    
    /**
     * Lock a door
     * 
     * @param string $id
     * @return string
     */
    public function lock($id) {
        $url = verisureConfig::$VERISURE_URL_BASE_PATH . "remotecontrol/lockunlock.cmd";
        $paramsArray = array(
            "code" => verisureConfig::$VERISURE_CODE,
            "deviceLabel" => $id,
            "state" => self::$STATE_DOOR_LOCK_LOCKED,
            "_csrf" => $this->preparePOST()
        );

        return $this->urlPOST($url, $paramsArray);
    }

    /**
     * Unlock a door
     * 
     * @return string
     */
    public function unlock($id) {
        $url = verisureConfig::$VERISURE_URL_BASE_PATH . "remotecontrol/lockunlock.cmd";
        $paramsArray = array(
            "code" => verisureConfig::$VERISURE_CODE,
            "deviceLabel" => $id,
            "state" => self::$STATE_DOOR_LOCK_UNLOCKED,
            "_csrf" => $this->preparePOST()
        );
        if (isset($_GET['debug'])) {
            var_dump($paramsArray);
        }

        return $this->urlPOST($url, $paramsArray);
    }
    
    /**
     * Set Armed Home
     * 
     * @return string
     */
    public function armedHome() {
        $url = verisureConfig::$VERISURE_URL_BASE_PATH . "remotecontrol/armstatechange.cmd";
        
        $paramsArray = array(
            "code" => verisureConfig::$VERISURE_CODE,
            "state" => self::$STATE_ALARM_STATE_ARMEDHOME,
            "_csrf" => $this->preparePOST()
        );
        
        return $this->urlPOST($url, $paramsArray);
    }
    
    /**
     * Set Armed Away
     * 
     * @return string
     */
    public function armedAway() {
        $url = verisureConfig::$VERISURE_URL_BASE_PATH . "remotecontrol/armstatechange.cmd";
        $paramsArray = array(
            "code" => verisureConfig::$VERISURE_CODE,
            "state" => self::$STATE_ALARM_STATE_ARMED,
            "_csrf" => $this->preparePOST()
        );

        return $this->urlPOST($url, $paramsArray);
    }
    
    /**
     * Set Unarmed
     * 
     * @return string
     */
    public function unarmed() {
        $url = verisureConfig::$VERISURE_URL_BASE_PATH . "remotecontrol/armstatechange.cmd";
        $paramsArray = array(
            "code" => verisureConfig::$VERISURE_CODE,
            "state" => self::$STATE_ALARM_STATE_UNARMED,
            "_csrf" => $this->preparePOST()
        );

        return $this->urlPOST($url, $paramsArray);
    }
    
    private function preparePOST() {
        $token = $this->addHeaderPOST();
        $this->getRemoteStatus();
        return $token;
    }
    
    
}
