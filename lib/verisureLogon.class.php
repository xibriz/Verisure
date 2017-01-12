<?php

/**
 * Description of verisureLogon
 *
 * @author ran
 */
class verisureLogon extends verisure {

    function __construct() {
        parent::__construct();
    }

    /**
     * Log the script into the Verisure portal
     * 
     * @return boolean
     */
    public function runLogon() {
        //Check if we are already logged in
        if ($this->isLoggedIn()) {
            return true;
        } 
        
        //Continue with the login process
        $loginPageHTML = $this->getLoginPageHTML();
        //Find all FORMs
        $formsArray = $this->parseHTMLtoFORMArray($loginPageHTML);
        //Find the login FORM (could be more than one FORM on the page)
        $loginFormArray = $this->getFORMbyAction($formsArray, verisure::$LOGIN_ACTION);
        if (count($loginFormArray) === 0) { //Could not find login form
            error_log("Could not find Verisure login FORM based on ACTION " . verisure::$LOGIN_ACTION);
            return false;
        }
        //Good to go, log in
        $loginResultHTML = $this->postLoginForm($loginFormArray);
        //Verify the result
        $loginResultJSON = json_decode($loginResultHTML);
        return (json_last_error() === JSON_ERROR_NONE && $loginResultJSON->status === 'ok');
    }

    /**
     * Check if we are logged in by trying to get the Ethernet status of the alarm system
     * 
     * @return boolean
     */
    public function isLoggedIn() {
        //Try to get connection status of the alarm system
        $result = $this->urlGET(verisureConfig::$VERISURE_URL_BASE_PATH . "overview/ethernetstatus");
        //Ttry do decode json
        json_decode($result);

        //If no errors, then we are logged in
        return (json_last_error() === JSON_ERROR_NONE);
    }

    /**
     * Get the login page
     * 
     * @return HTML
     */
    private function getLoginPageHTML() {
        return $this->urlGET(verisureConfig::$VERISURE_URL_BASE_PATH . verisureConfig::$VERISURE_LOCAL . "/login.html");
    }

    /**
     * POST the login FORM
     * 
     * @param array $formArray
     * @return HTML
     */
    private function postLoginForm($formArray) {
        //Insert username and password
        foreach ($formArray['keyValueArray'] as $name => &$value) {
            if (strstr($name, verisure::$LOGIN_INPUT_USERNAME)) {
                $value = verisureConfig::$VERISURE_USERNAME;
            }
            if (strstr($name, verisure::$LOGIN_INPUT_PASSWORD)) {
                $value = verisureConfig::$VERISURE_PASSWORD;
            }
        }

        return $this->urlPOST(verisureConfig::$VERISURE_URL_BASE_PATH . $formArray['action'], $formArray['keyValueArray']);
    }

}
