<?php
/**
 * Extension to contol the iRobot Roomba 980
 * You may want to start the Roomba when you leave the house and turn on the alarm?
 *
 * @author Ruben Andreassen (rubean85@gmail.com)
 */
class iRobot extends verisureRemoteControl {
    private static $ACTION_START = "start";
    private static $ACTION_STOP = "stop";
    private static $ACTION_PAUSE = "pause";
    private static $ACTION_DOCK = "dock";
    private static $ACTION_RESUME = "resume";
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Start the iRobot
     */
    public function start() {
        //TODO: What if already started?
        $this->urlGET(projectConfig::$IROBOT_URL_BASE_PATH.projectConfig::$IROBOT_URL_API_LOCAL_ACTION.self::$ACTION_START);
    }
    
    /**
     * Dock the iRobot
     */
    public function dock() {
        //TODO: What if already docked?
        $this->urlGET(projectConfig::$IROBOT_URL_BASE_PATH.projectConfig::$IROBOT_URL_API_LOCAL_ACTION.self::$ACTION_DOCK);
    }
}
