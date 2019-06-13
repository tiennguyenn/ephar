<?php

namespace UtilBundle\Utility;

use UtilBundle\Utility\RestfulAPIHelper;
use UtilBundle\Utility\Common;

/**
 * A wrapper class of CURL lib.
 */
class Curl implements \SplSubject
{
    const MAX_EXECUTE_TIME = 5;

    /**
     * @var int
     */
    protected $timeout = 20;

    /**
     * @var int
     */
    protected $method;

    /**
     * @var bool
     */
    protected $usingJson = false;

    /**
     * @var array
     */
    protected $headers;

    /**
     * @var resource
     */
    protected $ch;

    /**
     * @var array
     */
    private $observers = array();

    /**
     * @var int
     */
    protected $startTime;

    /**
     * @var int
     */
    protected $stopTime;

    /**
     * @var string
     */
    protected $currentUrl;

    /**
     * @var Exception
     */
    protected $exception;

    /**
     * @var array
     */
    protected $lastError = null;

    /**
     * @var integer
     */
    protected $isMulti = 0;

    // define constants
    const HTTP_GET  = 1;

    const HTTP_POST = 2;

    const HTTP_PUT  = 3;

    const HTTP_DELETE  = 4;

    /**
     * Constructor
     */
    public function __construct()
    {
        // create a new cURL resource
        $this->ch = curl_init();

        if (!$this->ch) {
            throw new \Exception('Cant create cURL resource');
        }
    }

    /**
     * Get magic method
     *
     * @param string $property
     */
    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    /**
     * Set timeout
     * @param int $timeout
     */
    public function setTimeout($timeout = 10)
    {
        $this->timeout = $timeout;
    }

    /**
     * Set headers
     *
     * @param string $header
     */
    public function setHeader($header)
    {
        $this->headers[] = $header;
    }

    /**
     * Set HTTP method
     *
     * @param int $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * Using json when POST|PUT data
     */
    public function setUsingJson()
    {
        $this->usingJson = true;
    }

    /**
     * Set option for cURL
     *
     * @param int $option_key
     * @param mixed $value
     */
    public function setOption($option_key, $value)
    {
        curl_setopt($this->ch, $option_key, $value);
    }

    /**
     * Get last error
     *
     * @return array
     */
    public function getLastError()
    {
        return $this->lastError;
    }

    /**
     *
     * @param string $url
     * @param array $fields
     * @throws Exception
     * @return Ambigous <\inSing\UtilBundle\Lib\Returns, mixed>
     */
    public function call($url, $fields = array())
    {
        // get start time
        $this->startTime  = Common::getMicroTimeInFloat();
        $this->currentUrl = $url;

        // set URL and other appropriate options
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->timeout);

        if($this->method == self::HTTP_PUT) {
            curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "PUT");
        } else if($this->method == self::HTTP_DELETE) {
            curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        } else {
            curl_setopt($this->ch, ( $this->method == self::HTTP_POST ? CURLOPT_POST : CURLOPT_HTTPGET), true);
        }

        //array methods
        $arrMethods = array(self::HTTP_POST, self::HTTP_PUT);

        if (count($fields) > 0) {
            if($this->usingJson) {
                $postFields = json_encode($fields);

                $this->headers[] = 'Content-Type: application/json';
                $this->headers[] = 'Content-Length: ' . strlen($postFields);
            } else {
                $postFields = http_build_query($fields);
            }

            curl_setopt($this->ch, CURLOPT_POSTFIELDS, $postFields);
        }
        elseif(in_array($this->method, $arrMethods))
        {
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, "");
        }

        if(count($this->headers)) {
            curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->headers);
        }
        else
        {
            curl_setopt($this->ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        }

        // execute the given cURL session
        $result = curl_exec($this->ch);

        // get stop time
        $this->stopTime = Common::getMicroTimeInFloat();
        $code = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);

        if (curl_errno($this->ch) != CURLE_OK) {
            $this->lastError = array(
                'url'    => $url,
                'method' => $this->method == self::HTTP_GET ? 'GET' : 'POST',
                'fields' => http_build_query($fields),
                'error_code' => curl_errno($this->ch),
                'error_str'  => curl_error($this->ch),
            );
            $result = false;
        } elseif($code != 200) {
            $this->lastError = array(
                'url'    => $url,
                'method' => $this->method == self::HTTP_GET ? 'GET' : 'POST',
                'fields' => http_build_query($fields),
                'error_code' => $code,
                'error_str'  => ''
            );
            $result = false;
        } else {
            $this->lastError = null;
        }

        $this->notify();

        return $result;
    }

    /**
     * Attaches an SplObserver to Curl
     *
     * @author Trung Nguyen
     * @param  SplObserver $obs
     * @return void
     */
    public function attach(\SplObserver $obs)
    {
        $id = spl_object_hash($obs);
        $this->observers[$id] = $obs;
    }

    /**
     * Detaches the SplObserver from the Curl
     *
     * @author Trung Nguyen
     * @param  SplObserver $obs
     * @return void
     */
    public function detach(\SplObserver $obs)
    {
        $id = spl_object_hash($obs);
        unset($this->observers[$id]);
    }

    /**
     * Notify all observers
     *
     * @author Trung Nguyen
     * @param  string $type
     * @return void
     */
    public function notify()
    {
        foreach($this->observers as $obs) {
            $obs->update($this);
        }
    }

    public function setMultiRequest($is_multi = true) {
        if($is_multi) {
            $this->isMulti  = 1;
            $this->ch       = $this->ch = curl_multi_init();
        }
    }

    /**
     * Refining code - Multi request action to call many APIs in the same time
     *
     * @param array $nodes
     *            array of API's ulr
     * @param array $params
     *            array of POST fields
     * @param array $method
     *            array of methods that corresponds to urls ($nodes above)
     * @return array $results
     * @author Anh.NguyenN
     */
    public function multiRequest($nodes = array(), $params = array(), $method = array())
    {
        if(empty($nodes)) {
            return false;
        }

        // get start time
        $this->startTime  = Common::getMicroTimeInFloat();

        // $this->ch = curl_multi_init();
        $curl_array = array();

        // build the multi-curl handle, add node to handle
        foreach ($nodes as $key => $url) {
            // setup curl for every node (url)
            $curl_array[$key] = curl_init($url);

            // every node maybe has a deferent request method
            // so, have to set for every one
            if (isset($method[$key]) && $method[$key] == RestfulAPIHelper::RESTFUL_API_GET)
                curl_setopt($curl_array[$key], CURLOPT_HTTPGET, true);
            else {
                if (! empty($params)) {
                    curl_setopt($curl_array[$key], CURLOPT_POSTFIELDS, json_encode($params));

                    // set options for cURL
                    if (RestfulAPIHelper::RESTFUL_API_POST_JSON == $method[$key] || RestfulAPIHelper::RESTFUL_API_PUT == $method[$key]) {

                        // For other POST with JSON in body
                        curl_setopt($curl_array[$key], CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen(json_encode($params))
                        ));
                    }
                }

                if (RestfulAPIHelper::RESTFUL_API_PUT == $method[$key]) {
                    // For PUT method
                    curl_setopt($curl_array[$key], CURLOPT_CUSTOMREQUEST, "PUT");
                } elseif (RestfulAPIHelper::RESTFUL_API_DELETE == $method[$key]) {
                    // For DELETE method
                    curl_setopt($curl_array[$key], CURLOPT_CUSTOMREQUEST, "DELETE");
                } else {
                    // For POST & POST JSON method
                    curl_setopt($curl_array[$key], CURLOPT_POST, true);
                }
            }

            curl_setopt($curl_array[$key], CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl_array[$key], CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl_array[$key], CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl_array[$key], CURLOPT_CONNECTTIMEOUT, $this->timeout);
            curl_setopt($curl_array[$key], CURLOPT_TIMEOUT, $this->timeout);

            curl_multi_add_handle($this->ch, $curl_array[$key]);
        }

        // execute all queries simultaneously, and continue when all are complete
        $running = null;
        $info = array();
        do {
            $status = curl_multi_exec($this->ch, $running);
            $inf = curl_multi_info_read($this->ch);
            if (false !== $inf) {
                $info[] = $inf;
            }
        } while ($status === CURLM_CALL_MULTI_PERFORM || $running);

        // all of our requests are done, we can now access the results
        $res = array();

        foreach ($nodes as $key => $url) {
            $res[$key] = curl_multi_getcontent($curl_array[$key]);

            if (RestfulAPIHelper::RESTFUL_API_DELETE != $method[$key] && RestfulAPIHelper::RESTFUL_API_GET != $method[$key]) {
                // For POST, PUT, JSON request
                $res[$key] = array(
                    'response' => json_decode($res[$key], true),
                    'http_code' => 1
                );
            } elseif (RestfulAPIHelper::RESTFUL_API_DELETE == $method[$key]) {
                // For DELETE request
                $res[$key] = array(
                    'response' => json_decode($res[$key], true),
                    'http_code' => 2
                );
            } elseif (RestfulAPIHelper::RESTFUL_API_GET == $method[$key]) {
                // for GET request
                $res[$key] = array(
                    'code' => 3,
                    'result' => json_decode($res[$key], true)
                );
            } else {
                // for wrong request method
                $res[$key] = array(
                    'code' => - 1,
                    'result' => ''
                );
            }
            // then remove the handle from the multi-handle
            curl_multi_remove_handle($this->ch, $curl_array[$key]);
        }

        // Close the multi-handle and return our results
        curl_multi_close($this->ch);

        // get stop time
        $this->stopTime = Common::getMicroTimeInFloat();

        $this->notify();

        return $res;
    }

}
