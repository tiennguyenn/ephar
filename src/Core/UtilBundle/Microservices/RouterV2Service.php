<?php
/**
 * Created by PhpStorm.
 * User: nanang
 * Date: 04/02/19
 * Time: 9:39
 */

namespace UtilBundle\Microservices;


const API_GET_TOKEN = "/oauth/access_token";
const INVALID_TOKEN = "invalid_grant";
const ACCESS_DENIED = "access_denied";

abstract class RouterV2Service
{
    private $apiClientId;
    private $apiClientSecret;
    private $container;
    private $headers;
    private $memcached;
    private $accessToken;

    protected $baseUrl;

    public function __construct($container)
    {
        $this->container = $container;
        $this->baseUrl = $this->container->getParameter('apiv2_url');
        $this->apiClientId = $this->container->getParameter('apiv2_client_id');
        $this->apiClientSecret = $this->container->getParameter('apiv2_client_secret');
        $this->memcached = $this->container->get('session.memcached');
        $this->accessToken = $this->memcached->get('apiv2_access_token');

        // Standard JSON Header
        $this->headers = array(
            "Accept" => "application/json",
            "Content-Type" => "application/json"
        );
    }

    public function getAccessToken()
    {
        $url = $this->baseUrl . API_GET_TOKEN;

        if ($this->accessToken === false) {
            $authInfo = array(
                'client_id' => $this->apiClientId,
                'client_secret' => $this->apiClientSecret,
                'grant_type' => 'client_credentials'
            );

            $resp = $this->doRequest($url, 'post-json', $authInfo);

            $dataResponse = json_decode($resp['data']);

            $this->accessToken = $dataResponse->access_token;

            $this->memcached->set('apiv2_access_token', $this->accessToken, 86400);
        }

        return $this->accessToken;
    }

    public function getDataFromApi($url, $method, $params = array())
    {
        $url =  $url.'?access_token='.$this->accessToken;

        $resp = $this->doRequest($url, $method, $params);

        $dataResponse = json_decode($resp['data']);

        if(isset($dataResponse->error) && ($dataResponse->error == INVALID_TOKEN || $dataResponse->error == ACCESS_DENIED)) {
            $this->getAccessToken();
            return $this->doRequest($url, $method, $params);
        }

        return $resp;
    }

    private function doRequest($url, $method, $data = '')
    {
        $header[0] = "Accept: */*";
        $header[] = "Cache-Control: max-age=0";
        $header[] = "Connection: keep-alive";
        $header[] = "Keep-Alive: 300";
        $header[] = "Accept-Language: en-us";

        $ch = curl_init();
        if ($data)
        {
            if ($method == 'get') {
                $str_data = http_build_query($data);
                $header[] = "Content-Type: application/x-www-form-urlencoded";
                $url .= '?'.$str_data;
            }   else if ($method == 'post') {
                $str_data = http_build_query($data);
                $header[] = "Content-Type: application/x-www-form-urlencoded";
                curl_setopt($ch, CURLOPT_POSTFIELDS, $str_data);
            }   else if ($method == 'post-json') {
                $header[] = "Content-Type: application/json";
                if (is_array($data)){
                    $data = json_encode($data);
                }
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            }

        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        $user_agent = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:14.0) Gecko/20100101 Firefox/14.0.1";
        curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($ch, CURLOPT_TIMEOUT,15);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_STDERR, fopen('php://stderr', 'w'));
        $response  =curl_exec ($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close ($ch);

        return ['code' => $code, 'data' => $response];
    }
}