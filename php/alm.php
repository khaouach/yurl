<?php

class AlmFactory {

    private static $factory = null;
    private $rootAddress = null;
    private $qc = null; //curl connection
    private $debug = false;
    private $ckfile = null;
    private static $URL_AUTHENTICATION = "/qcbin/rest/is-authenticated";
    private static $URL_AUTHENTICATION_POINT = "/qcbin/authentication-point/authenticate";
    private static $URL_SESSION = "/qcbin/rest/site-session";
    private static $URL_DEFECTS = "/qcbin/rest/domains/%s/projects/%s/defects";
    private static $URL_LOGOUT = "/qcbin/authentication-point/logout";

    private function __construct($rootAddress) {
        $this->rootAddress = $rootAddress;

//create a new cURL resource
        $this->qc = curl_init();
        curl_setopt($this->qc, CURLOPT_HTTPHEADER, array('Accept: application/xml', 'Content-Type: text/html'));
    }

    /**
     * get an instance of the factory
     * @return BootFactory
     */
    public static function inst($rootAddress) {
        if (self::$factory == null) {
            self::$factory = new AlmFactory($rootAddress);
        }
        return self::$factory;
    }

    /**
     * set debug parameter on or off
     */
    public function debug($debug) {
        $this->debug = $debug;
        return self::$factory;
    }

    /**
     * contruct an url
     * @param type $url
     * @return type
     */
    private function getUrl($url) {
        return $this->rootAddress . $url;
    }

    /**
     * outputting values to the screen
     * @param type $obj
     */
    private function out($obj) {
        if ($this->debug) {
            if (is_object($obj) || is_array($obj)) {
                $outp = print_r($obj, true);
            } else {
                $outp = $obj;
            }
            printf("<pre>%s</pre>", $outp);
        }
    }

    /**
     * Authenticate user, returns true if succesful, false if it failes
     * @param type $user
     * @param type $password
     * @return type
     */
    public function authenticate($username, $password) {
        $this->out("Authentincating");

        //create a cookie file
        $this->ckfile = realpath(tempnam("/tmp", "CURLCOOKIE"));

        //set URL and other appropriate options
        curl_setopt($this->qc, CURLOPT_URL, $this->getUrl(self::$URL_AUTHENTICATION));
        curl_setopt($this->qc, CURLOPT_HEADER, 0);
        curl_setopt($this->qc, CURLOPT_POST, 1);
        curl_setopt($this->qc, CURLOPT_RETURNTRANSFER, 1);

        //grab the URL and pass it to the browser
        $result = curl_exec($this->qc);
        $response = curl_getinfo($this->qc);

        //401 Not authenticated (as expected)
        //We need to pass the Authorization: Basic headers to authenticate url with the 
        //Correct credentials.
        //Store the returned cookfile into $ckfile
        //Then use the cookie when we need it......
        if ($response['http_code'] == '401') {


            $credentials = sprintf("%s:%s", $username, $password);
            $headers = array("POST /HTTP/1.1", "Authorization: Basic " . base64_encode($credentials));

            curl_setopt($this->qc, CURLOPT_URL, $this->getUrl(self::$URL_AUTHENTICATION_POINT));
            curl_setopt($this->qc, CURLOPT_POST, 1); //Not sure we need these again as set above?
            curl_setopt($this->qc, CURLOPT_HTTPHEADER, $headers);

            //Set the cookie
            curl_setopt($this->qc, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($this->qc, CURLOPT_COOKIEJAR, $this->ckfile);

            $result = curl_exec($this->qc);
            $response = curl_getinfo($this->qc);

            //hier wordt die sessie url aangeroepen, maar hoe dat exact moet weet ik nog niet
            curl_setopt($this->qc, CURLOPT_URL, $this->getUrl(self::$URL_SESSION));
            curl_setopt($this->qc, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($this->qc, CURLOPT_COOKIEJAR, $this->ckfile);

            $result = curl_exec($this->qc);
//             $response = curl_getinfo($qc);
            //The response will be 200   
            if ($response['http_code'] == '200') {
                 $this->out("Authentication successful");
                 return true;
            } else {
                 $this->out("Authentication failed");
            }
        } else {
            $this->out("Not sure what happened?!");
        }

        

        return false;
    }
    
    /**
     * Log off and close connection
     */
    public function signOff(){       
        curl_setopt($this->qc, CURLOPT_URL, $this->getUrl(self::$URL_LOGOUT));
        curl_setopt($this->qc, CURLOPT_HEADER, 0);
        curl_setopt($this->qc, CURLOPT_HTTPGET, 1);
        curl_setopt($this->qc, CURLOPT_RETURNTRANSFER, 1);

        //grab the URL and pass it to the browser
        $result = curl_exec($this->qc);
        //Close cURL resource, and free up system resources
        curl_close($this->qc);
    }

    /**
     * get List of defects
     * @param type $domain
     * @param type $project
     * @param type $query
     * @return type
     */
    public function getDefects($domain, $project, $query=null, $fields=null) {
         //Use the cookie for subsequent calls...
        curl_setopt($this->qc, CURLOPT_HTTPGET, 1); //Not sure we need these again as set above?
        curl_setopt($this->qc, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->qc, CURLOPT_HTTPHEADER, array('Accept: application/xml', 'Content-Type: text/html'));
        curl_setopt($this->qc, CURLOPT_COOKIEFILE, $this->ckfile);
        
        
        $queryFields = http_build_query(array("query" => $query));
        $fieldFields = http_build_query(array("fields" => implode(",", $fields)));
        
        
        if(!($query==null && $fields==null)){
            $url = $this->getUrl(sprintf(self::$URL_DEFECTS, $domain, $project))  . "?";
        }
        
        if($query!=null){        
            $url = $this->getUrl(sprintf(self::$URL_DEFECTS, $domain, $project))  . "?".$queryFields;
        }
        
        if($fields!=null){        
            $url = $this->getUrl(sprintf(self::$URL_DEFECTS, $domain, $project))  . "?".$fieldFields;
        }
        $this->out("executing url : ". $url);
        curl_setopt($this->qc, CURLOPT_URL, $url);

        //In this example we are retrieving the xml so...
        $xml = simplexml_load_string(curl_exec($this->qc));
        $json = json_encode($xml);
        return $json;
    }

}
$factory = AlmFactory::inst("http://alm12.nl.eu.abnamro.com");
//$factory->debug(true);
$factory->authenticate("x11442", "Apollo12");
//$defects = $factory->getDefects("ITIN_DC", "Alerting");

$query = "{status[NOT (Closed)]}";
$fields = array("status", "description");

$defects = $factory->getDefects("ITIN_DC", "Alerting", $query, $fields);
echo $defects;
$factory->signOff();