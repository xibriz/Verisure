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
        curl_setopt($this->ch, CURLOPT_URL, projectConfig::$VERISURE_URL_BASE_PATH . "overview/ethernetstatus");
        //Retrieve result, and try do decode json
        json_decode(curl_exec($this->ch));

        //If no errors, then we are logged in
        return (json_last_error() === JSON_ERROR_NONE);
    }

    /**
     * Get the login page
     * 
     * @return HTML
     */
    private function getLoginPageHTML() {
        curl_setopt($this->ch, CURLOPT_URL, projectConfig::$VERISURE_URL_BASE_PATH . "no/login.html");
        return curl_exec($this->ch);
    }

    /**
     * POST the login FORM
     * 
     * @param array $formArray
     * @return HTML
     */
    private function postLoginForm($formArray) {
        curl_setopt($this->ch, CURLOPT_URL, projectConfig::$VERISURE_URL_BASE_PATH . $formArray['action']);
        
        $encoded = '';
        foreach ($formArray['keyValueArray'] as $name => $value) {
            if (strstr($name, verisure::$LOGIN_INPUT_USERNAME)) {
                $value = projectConfig::$VERISURE_USERNAME;
            }
            if (strstr($name, verisure::$LOGIN_INPUT_PASSWORD)) {
                $value = projectConfig::$VERISURE_PASSWORD;
            }
            $encoded .= urlencode($name) . '=' . urlencode($value) . '&';
        }
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, rtrim($encoded, "&"));
        
        curl_setopt($this->ch, CURLOPT_POST, 1);
        $resultHTML = curl_exec($this->ch);
        curl_setopt($this->ch, CURLOPT_POST, 0);
        
        return $resultHTML;
    }

}
