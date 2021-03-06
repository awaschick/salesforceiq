<?php namespace SalesforceIQ\Resource;

use Exception;

class ApiRequest {

    protected $key = "";
    protected $secret = "";
    protected $apiEndpoint = "https://api.salesforceiq.com/v2/";
    protected $debug = false;
    protected $data = array();

    function __construct($key = null, $secret = null)
    {
        if ($key && $secret) {
            Config::setKey($key, $secret);
        }

        // Set key & secret
        $this->key    = Config::$key;
        $this->secret = Config::$secret;
    }

    /**
     * @param $target
     * @param null $data
     * @return mixed
     */
    public function newPost($target, $data = null)
    {
        return $this->send('POST', $target, $data);
    }

    /**
     * @param $target
     * @param null $data
     * @return mixed
     */
    public function newPut($target, $data = null)
    {
        return $this->send('PUT', $target, $data);
    }

    /**
     * @param $target
     * @param null $data
     * @return mixed
     */
    public function newGet($target, $data = null)
    {
        return $this->send('GET', $target, $data);
    }

    /**
     * @param $requestType
     * @param $target
     * @param $data
     * @return mixed
     */
    public function send($requestType, $target, $data)
    {
        $headers = array();

        $url = $this->apiEndpoint.$target;

        if ($this->debug)
        {
            echo "Setting up Request with API Key: {$this->key}\n";
            echo "Setting up Request with API Secret: {$this->secret}\n";
            echo "Setting up Request with Endpoint: {$url}\n";
            echo "Setting up Request with Method: {$requestType}\n";
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERPWD, $this->key . ':' . $this->secret);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $requestType);

        if ($requestType == 'PUT' || $requestType == 'POST')
        {
            if (!$data) {
                $data = $this->data;
            }

            // Create payload
            if (is_object($data) && method_exists($data, 'toJson')) {
                $payload = $data->toJson();
            }
            else {
                $payload = json_encode($data);
            }

            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            $headers[] = 'Content-Length: '.strlen($payload);
            $headers[] = 'Content-Type: application/json';
        }

        $headers[] = 'Accept: application/json';

        if ($this->debug) {
            curl_setopt($ch, CURLOPT_VERBOSE, true);
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 1);


        $response = curl_exec($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);

        $this->data = array();

        curl_close($ch);

        if ($this->debug){
            echo "\n-----RAW----\n";
            echo $body;
            echo "\n-----\n";
        }

        $return = json_decode($body, true);

        if ($this->debug) {
            print_r($return);
        }

        // Server error
        if (isset($return['statusCode'])) {
            throw new Exception($return['errorMessage'], $return['statusCode']);
        }

        return $return;
    }

}