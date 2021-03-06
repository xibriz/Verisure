<?php
/**
 * Base class for Verisure kommunication.
 * Sets up cULR and handles requests
 *
 * @author Ruben Andreassen (rubean85@gmail.com)
 */
class verisure {

    public static $LOGIN_ACTION = "j_spring_security_check"; //Action on login FORM
    public static $LOGIN_INPUT_USERNAME = "j_username"; //NAME of username INPUT in login FORM
    public static $LOGIN_INPUT_PASSWORD = "j_password"; //NAME of passowrd INPUT in login FORM
    private static $COOKIE_FILE = "verisure_cookiefile.txt", $CURL_ERROR_FILE = "verisure_curl_error.txt";
    public $ch = null;
    private $debug = true, $fh = null;

    /**
     * Init cULR
     */
    public function __construct() {
        //Try to create file
        if (!file_exists(realpath(verisureConfig::$VERISURE_TMP_FILE_PATH) . DIRECTORY_SEPARATOR . self::$COOKIE_FILE)) {
            fclose(fopen(realpath(verisureConfig::$VERISURE_TMP_FILE_PATH) . DIRECTORY_SEPARATOR . self::$COOKIE_FILE, "w"));
        }
        if (!file_exists(realpath(verisureConfig::$VERISURE_TMP_FILE_PATH) . DIRECTORY_SEPARATOR . self::$COOKIE_FILE) || !is_writable(realpath(verisureConfig::$VERISURE_TMP_FILE_PATH) . DIRECTORY_SEPARATOR . self::$COOKIE_FILE)) {
            error_log("Cookie file is missing or not writable. Try to create it manually at " . realpath(verisureConfig::$VERISURE_TMP_FILE_PATH) . DIRECTORY_SEPARATOR . self::$COOKIE_FILE . " and set chmod 777 on it");
            if (isset($_GET['debug'])) {
                echo "Cookie file is missing or not writable. Try to create it manually at " . realpath(verisureConfig::$VERISURE_TMP_FILE_PATH) . DIRECTORY_SEPARATOR . self::$COOKIE_FILE . " and set chmod 777 on it";
            }
            exit;
        }
        if (isset($_GET['debug'])) {
            echo "Cookie file location: " . realpath(verisureConfig::$VERISURE_TMP_FILE_PATH) . DIRECTORY_SEPARATOR . self::$COOKIE_FILE;
        }
        $this->initCurl();
    }

    /**
     * Close cURL and filehandler if debug is enabled
     */
    public function __destruct() {
        curl_close($this->ch);
        if ($this->debug) {
            fclose($this->fh);
        }
    }

    /**
     * Initialiser cURL
     */
    private function initCurl() {
        $this->ch = curl_init();

        curl_setopt($this->ch, CURLOPT_FORBID_REUSE, 0);
        curl_setopt($this->ch, CURLOPT_FRESH_CONNECT, 0);
        curl_setopt($this->ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($this->ch, CURLOPT_VERBOSE, 0);
        curl_setopt($this->ch, CURLOPT_HEADER, 0);

        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($this->ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT x.y; Win64; x64; rv:10.0) Gecko/20100101 Firefox/50.0");
        curl_setopt($this->ch, CURLOPT_REFERER, verisureConfig::$VERISURE_URL_BASE_PATH.verisureConfig::$VERISURE_LOCAL."/start.html");

        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);

        curl_setopt($this->ch, CURLOPT_COOKIEFILE, realpath(verisureConfig::$VERISURE_TMP_FILE_PATH) . DIRECTORY_SEPARATOR . self::$COOKIE_FILE);
        curl_setopt($this->ch, CURLOPT_COOKIEJAR, realpath(verisureConfig::$VERISURE_TMP_FILE_PATH) . DIRECTORY_SEPARATOR . self::$COOKIE_FILE);

        curl_setopt($this->ch, CURLOPT_POST, 0);

        if ($this->debug) {
            curl_setopt($this->ch, CURLOPT_VERBOSE, 1);
            $this->fh = fopen(realpath(verisureConfig::$VERISURE_TMP_FILE_PATH) . DIRECTORY_SEPARATOR . self::$CURL_ERROR_FILE, 'w+');
            curl_setopt($this->ch, CURLOPT_STDERR, $this->fh);
        }
    }

    public function addHeader($array) {
        if (isset($_GET['debug'])) {
            var_dump($array);
        }
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $array);
    }
    
    /**
     * Add headers needed to do POST against Verisure
     * 
     * @return string X-CSRF-token
     */
    public function addHeaderPOST() {
        $token = $this->getXCsrfToken();
        $this->addHeader(array(
            'Origin: ' . rtrim(verisureConfig::$VERISURE_URL_BASE_PATH, "/"),
            'Host: ' . verisureConfig::$VERISURE_HOST,
            'Accept: application/json, text/javascript, */*; q=0.01',
            'Accept-Language: nb-NO,nb;q=0.9,no-NO;q=0.8,no;q=0.6,nn-NO;q=0.5,nn;q=0.4,en-US;q=0.3,en;q=0.1',
            'Accept-Encoding: gzip, deflate, br',
            'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
            'X-Requested-With: XMLHttpRequest',
            'Connection: keep-alive',
            'X-CSRF-TOKEN: ' . $token,
        ));        
        return $token;
    }
    
    /**
     * Retrieve Mypages and parse the HTML to get the X-CSRF-TOKEN
     * 
     * @return string
     */
    public function getXCsrfToken() {
        $result = $this->urlGET(verisureConfig::$VERISURE_URL_BASE_PATH . verisureConfig::$VERISURE_LOCAL . "/start.html");
        $matches = array();
        preg_match('/(\'X-CSRF-TOKEN\').*?((?:[a-z0-9_][a-z0-9_]*)).*?/is', $result, $matches);
        
        return (isset($matches[2]) && strlen($matches[2]) === 64) ? $matches[2] : '';
    }

    /**
     * Generic function to execute a HTTP GET with a spesific URL
     * 
     * @param string $url
     */
    public function urlGET($url) {
        curl_setopt($this->ch, CURLOPT_URL, $url);
        $result = curl_exec($this->ch);
        if (isset($_GET['debug'])) {
            echo "------------------- urlGET ------------";
            var_dump($url);
            var_dump($result);
        }
        return $result;
    }

    /**
     * Generig function to execute a HTTP POST with a spesific URL and key-value pair as POST parameters
     * 
     * @param string $url
     * @param array $keyValueArray
     * @return string
     */
    public function urlPOST($url, $keyValueArray) {
        curl_setopt($this->ch, CURLOPT_URL, $url);
        
        $encoded = '';
        foreach ($keyValueArray as $key => $value) {
            $encoded .= urlencode($key) . '=' . urlencode($value) . '&';
        }
        if (isset($_GET['debug'])) {
            var_dump(rtrim($encoded, '&'));
        }

        curl_setopt($this->ch, CURLOPT_POSTFIELDS, rtrim($encoded, '&'));
        //var_dump(rtrim($encoded, '&')); exit;
        curl_setopt($this->ch, CURLOPT_POST, 1);
        $resultHTML = curl_exec($this->ch);
        curl_setopt($this->ch, CURLOPT_POST, 0);

        return $resultHTML;
    }

    /**
     * Parse HTML and find all FORM-elements and parse the INPUT elements into an array
     * 
     * @param string $html
     * @return array
     */
    public function parseHTMLtoFORMArray($html) {
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $forms = $dom->getElementsByTagName('form'); // Find Sections

        $i = 0;
        $formArray = array();
        foreach ($forms as $form) {
            $inputArray = array();
            $keyValueArray = array();
            $inputs = $forms->item($i)->getElementsByTagName('input'); // Find Sections
            $j = 0;
            foreach ($inputs as $input) {
                $name = $inputs->item($j)->getAttribute('name');
                $value = $inputs->item($j)->getAttribute('value');
                $inputArray[] = array(
                    "name" => $name,
                    "value" => $value
                );
                $keyValueArray[$name] = $value;
                $j++;
            }

            $formArray[] = array(
                "action" => $forms->item($i)->getAttribute('action'),
                "method" => $forms->item($i)->getAttribute('method'),
                "inputArray" => $inputArray,
                "keyValueArray" => $keyValueArray
            );
            $i++;
        }

        return $formArray;
    }

    /**
     * Returns a spesific FORM by an spesific ACTION string
     * 
     * @param array $formsArray
     * @param string $action
     * @return array
     */
    public function getFORMbyAction($formsArray, $action) {
        $formArray = array();
        foreach ($formsArray as $array) {
            if (strstr($array['action'], $action)) {
                $formArray = $array;
                break;
            }
        }
        return $formArray;
    }

    /**
     * Converting date strings to timestamp
     * 
     * @param string $dateStr
     * @return string
     */
    public function convertDateStringToTimestamp($dateStr) {
        if (verisureConfig::$VERISURE_LOCAL === 'no') {
            return $this->convertDateStringToTimestampNO($dateStr);
        } else { //TODO: Add other languages
            error_log("Could not convert date string to timestamp because the current local (".verisureConfig::$VERISURE_LOCAL.") does not have a suitable function. Using time()!");
            return time();
        }
    }
    
    /**
     * Converting date strings in Norwegian to timestamp.
     * ("I dag" and "I går")
     * 
     * @param string $dateStr
     * @return string
     */
    private function convertDateStringToTimestampNO($dateStr) {
        if (mb_strstr($dateStr, "dag") !== false) { //Example: I dag 05:11
            return strtotime(str_replace("I dag", "Today", $dateStr));
        } else if (mb_strstr($dateStr, "går") !== false) { //Example: I går 05:11
            return strtotime(str_replace("I dag", "Yesterday", $dateStr));
        } else { //Example: 02.01.17 11:41
            list($date, $time) = explode(" ", $dateStr);
            list($day, $month, $year) = explode(".", $date);
            return strtotime($year."-".$month."-".$day." ".$time);
        }
    }
    
    
    /**
     * Converting device names from local language to English
     * 
     * @param string $deviceName
     * @return string
     */
    public function convertDeviceNameToEnglish($deviceName) {
        if (verisureConfig::$VERISURE_LOCAL === 'no') {
            return $this->convertDeviceNameToEnglishNO($deviceName);
        } else { //TODO: Add other languages
            error_log("Could not convert device name to english because the current local (".verisureConfig::$VERISURE_LOCAL.") does not have a suitable function. Using original device name!");
            return $deviceName;
        }
    }
    
    /**
     * Converting device names in Norwegian to English 
     * 
     * @param string $deviceName
     * @return string
     */
    private function convertDeviceNameToEnglishNO($deviceName) {
        switch ($deviceName) {
            case "Røykdetektor":
                return "Smoke Detector";
            case "Vanndetektor":
                return "Water Detector";
            case "Sirene":
                return "Siren";
            default:
                return $deviceName;
        }
    }

}
