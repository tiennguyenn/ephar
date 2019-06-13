<?php

namespace UtilBundle\Microservices;

use UtilBundle\Utility\Constant;
use UtilBundle\Utility\Common;
use Unirest\Request;

const API_AUTH = '/auth';
const API_Fresh_Token = "/refresh_token";
const API_Get_Token ="/get_token";
const API_Auth ="/auth";
const EXPIRED_TOKEN = 408;
const INVALID_TOKEN = 400;
abstract class  RouterService {

    private $apiKey;
    protected $baseUrl;
    private $accessToken;
    private $clientId;
    private $clientSecret;
    private $headers;
    private $container;    
    protected $infoUrl;
    protected $sharePointUrl;
    protected $approvalUrl;
    protected $pharmacistRedispenseUrl;

    public function __construct($container)
    {
        $this->container = $container;
        // Set Base URL
        $this->baseUrl = $this->container->getParameter('api_url');

        // Website Id and Secret
        $this->clientId = $this->container->getParameter('api_client_id');
        $this->clientSecret = $this->container->getParameter('api_client_secret');

        // admin API Token
        $this->apiKey = $this->container->getParameter('api_token');
        $refunParams = $this->container->getParameter('refund');
        $this->approvalUrl = $refunParams['approvalUrl'];
        $this->infoUrl = $refunParams['infoUrl'];

        $this->sharePointUrl = $this->container->getParameter('upload_share_point');
        $this->invoicePartyUrl = $this->container->getParameter('invoice_party_at_fault_url');
        $this->pharmacistRedispenseUrl = $this->container->getParameter('pharmacist_redispense_url');        

        // Standard JSON Header
        $this->headers = array(
            "Accept" => "application/json",
            "Content-Type" => "application/json"
        );
    }

    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
    }

    public function setClientSecret($clientSecret)
    {
        $this->clientSecret = $clientSecret;
    }

    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Refresh Authentication token
     */
    public function refreshAuthenticationToken()
    {
        $url = $this->baseUrl . API_Fresh_Token;

        $auth_info = array(
            "client_id" => $this->clientId,
            "client_secret" => $this->clientSecret,
            "access_token" => $this->apiKey
        );
        $resp = $this->get_page($url, 'post-json', $auth_info);
        return $resp;
    }

    public function authenticate()
    {
        $url = $this->baseUrl . API_AUTH;

        $auth_info = array(
            "client_id" => $this->clientId,
            "client_secret" => $this->clientSecret
        );
        $resp = $this->get_page($url, 'post-json', $auth_info);
        return $resp;
    }


    protected function getDataFromApi($url, $method, $param = array(), $firstCall = true) {
        $query['access_token'] = $this->apiKey;
        if(strtolower($method) == 'post') {
            $url =  $url.'?access_token='.$this->apiKey;
        }
        $query = array_merge($query, $param);

        //debug
        $resp = $this->get_page($url, $method, $query);

          $dataResponse = json_decode($resp['data']);
        if(isset($dataResponse->status) && ($dataResponse->status == EXPIRED_TOKEN || $dataResponse->status == INVALID_TOKEN) && $firstCall === true) {
            $this->refreshAuthenticationToken();
            return $this->get_page($url, $method, $query);
        }

        return $resp;
    }

    protected function postData($url,$param = [] ){
        return $this->get_page($url, 'post-json', $param);
    }

    private function get_page($url, $method, $data = '')
    {
        //echo $url.' '.$method;	exit;
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
            }   else if ($method == 'post-json')
            {
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
