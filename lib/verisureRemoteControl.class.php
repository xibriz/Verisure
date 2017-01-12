<?php

/**
 * Class to handle alarm status and locks
 *
 * @author Ruben Andreassen (rubean85@gmail.com)
 */
class verisureRemoteControl extends verisure{
    //Yale Doorman
    private static $TYPE_DOOR_LOCK = "DOOR_LOCK";
    private static $STATUS_DOOR_LOCK_LOCKED = "locked"; //Door locked (låst)
    private static $STATUS_DOOR_LOCK_UNLOCKED = "unlocked"; //Door unlocked (åpen)
    //Alarm
    private static $TYPE_ALARM_STATE = "ARM_STATE";
    private static $STATUS_ALARM_STATE_UNARMED = "unarmed"; //Alarm unarmed (frakoblet)
    private static $STATUS_ALARM_STATE_ARMEDHOME = "armedhome"; //Alarm armedhome (delsikring)
    private static $STATUS_ALARM_STATE_ARMED = "armed"; //Alarm TODO (tilkoblet)
    
    
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
}
