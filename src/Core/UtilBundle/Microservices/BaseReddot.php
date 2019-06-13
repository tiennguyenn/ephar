<?php
namespace UtilBundle\Microservices;

use UtilBundle\Utility\Constant;
use UtilBundle\Utility\Common;
use UtilBundle\Microservices\GmedRequest;

abstract class BaseReddot
{
    protected $container;
    private $headers;
    private $config;
    protected $api_url;
    protected $mid;
    protected $api_key;
    protected $api_secret;
    protected $currency;
    protected $payment_type;
    protected $api_mode;
    protected static $sytemConfig;

    public function __construct($container)
    {
        $this->container  = $container;
        $this->headers = array(
            "Accept" => "application/json",
            "Content-Type" => "application/json"
        );
        $this->config = $this->container->getParameter('red_dot_api');


        $this->api_url = $this->config['api_url'];
        if(empty(self::$sytemConfig)){
            $em = $this->container->get('doctrine')->getManager();
            self::$sytemConfig = $em->getRepository('UtilBundle:PlatformSettings')->getPlatFormSetting();
        }
        // for SGD
        $this->mid = $this->config['mid'];
        $this->api_key = $this->config['key'];
        $this->api_secret = $this->config['secret'];
        $this->currency = self::$sytemConfig['currencyCode'];

        $this->payment_type = 'S';
        $this->api_mode = 'redirection_hosted';

    }


    public function requestData( $url, $method, $data ){
        $resp = null;

        if ($method == "get" ){
            $query = is_array($data) ? http_build_query($data) : $data;
        }else{
            $query = is_array($data) ? json_encode($data) : $data;
        }
        switch($method) {
            case 'put':
                $resp = GmedRequest::putData($url, $this->headers, $query);
                break;
            case 'post':
                $resp = GmedRequest::postData($url, $this->headers, $query);
                break;
            case 'delete' :
                $resp = GmedRequest::deleteData($url, $this->headers, $query);
                break;
            case 'get' :
                $url .= "?" . $query;
                $resp = GmedRequest::getData($url, $this->headers, $query);
                break;
            default :return json_encode(array('error' => 'Invalid method requested.'));
        }

        if(!isset($resp->data)) {
            $resp->data = [];
        }
        return $resp;
    }
    protected function formatResponse($resp) {
        $data= (json_decode(json_encode($resp), true));
        $body = $data['body'];
        $result = [];
        $result['code'] = $data['code'];
        $result['data'] = $body;
        return $result;

    }
    public function start_payment($data, $patientEmail)
    {
        $data['mid'] = $this->mid;
        $data['api_mode'] = $this->api_mode;
        $data['payment_type'] = $this->payment_type;
        $data['ccy'] = $this->currency;
        $data['signature'] = $this->get_sign($data);
        $data['payer_email'] = $patientEmail;

        $contents = $this->requestData($this->api_url['start_payment'], 'post', $data);

        return array(
            'input_data' => $data,
            'response' => $contents
        );
    }

    public function refund($params)
    {
        $data = array();
        $data['response_type'] = 'json';
        $data['action_type'] = 'refund';
        $data['order_number'] = $params['order_id'];
        $data['mid'] = $this->mid;
        $data['amount'] = $params['amount'];
        $data['currency'] = $this->currency;
        $data['transaction_id'] = $params['transaction_id'];
        $data['signature'] = $this->calculateSignature($data);

        $contents = $this->get_page($this->api_url['refund'], 'post', $data);
        $response = json_decode($contents, true);
        return $response;
    }

    public function validate_payment($transaction_id)
    {
        if ($transaction_id)
        {

            $data = array('request_mid' => $this->mid, 'transaction_id' => $transaction_id);
            $data['signature'] = $this->sign_generic($this->api_secret, $data);
            $contents = $this->requestData($this->api_url['validate'], 'post', $data);
            $resp = $this->formatResponse($contents);
            $response = $resp['data'];
            if (isset($response['signature']))
            {
                $calculated_signature = $this->sign_generic($this->api_secret, $response);
                if ($calculated_signature == $response['signature'])
                {
                    return $response;
                }
            }
        }
        return false;
    }

    public function handle_notify()
    {
        $querystring = @file_get_contents('php://input');
        $response = json_decode($querystring, true);
        if (isset($response['signature']))
        {
            $calculated_signature = $this->sign_generic($this->api_secret, $response);
            if ($calculated_signature == $response['signature'])
            {
                return $response;
            }
        }
        return false;
    }

    function calculateSignature($params, $chosenFields = NULL)
    {
        if ($chosenFields == NULL)
        {
            $chosenFields = array_keys($params);
        }

        sort($chosenFields);
        $requestsString = '';

        foreach($chosenFields as $field)
        {
            if (isset($params[$field]))
            {
                $requestsString.= $field . '=' . ($params[$field]) . '&';
            }
        }
        $requestsString.= 'secret_key=' . $this->api_secret;
        $signature = md5($requestsString);
        return $signature;
    }



    private function get_sign($params)
    {
        $fields_for_sign = array('mid', 'order_id', 'payment_type', 'amount', 'ccy');
        $aggregated_field_str = "";
        foreach ($fields_for_sign as $f)
        {
            $aggregated_field_str .= trim($params[$f]);
        }
        $aggregated_field_str .= $this->api_secret;
        $signature = hash ('sha512', $aggregated_field_str);
        return $signature;
    }

    private function sign_generic($secret_key, $params)
    {
        unset($params['signature']);
        $data_to_sign = "";
        $this->recursive_generic_array_sign($params, $data_to_sign);
        $data_to_sign.= $secret_key;
        return hash('sha512', $data_to_sign);
    }

    private function recursive_generic_array_sign(&$params, &$data_to_sign)
    {
        ksort($params);
        foreach($params as $v)
        {
            if (is_array($v))
            {
                $this->recursive_generic_array_sign($v, $data_to_sign);
            }
            else
            {
                $data_to_sign.= $v;
            }
        }
    }


    private function get_page($url, $method, $data = '')
    {
        //echo $url.' '.$method;	exit;
        $usecookie  = getcwd().'/cookie.txt';
        $header[0] = "Accept: */*";
        $header[] = "Cache-Control: max-age=0";
        $header[] = "Connection: keep-alive";
        $header[] = "Keep-Alive: 300";
        $header[] = "Accept-Language: en-us";

        $ch = curl_init();
        if ($data)
        {
            if ($method == 'get')
            {
                $str_data = http_build_query($data);
                $header[] = "Content-Type: application/x-www-form-urlencoded";
                $url .= '?'.$str_data;
            } else if ($method == 'post')
            {
                $str_data = http_build_query($data);
                $header[] = "Content-Type: application/x-www-form-urlencoded";
                curl_setopt($ch, CURLOPT_POSTFIELDS, $str_data);
            }  else if ($method == 'post-json')
            {
                $header[] = "Content-Type: application/json";
                if (is_array($data)) $data = json_encode($data);
                //echo $data;exit;
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

            }else if ($method == 'put')
            {
                $str_data = http_build_query($data);
                $str_data = json_encode($data);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $str_data);
            } else if ($method == 'put-json')
            {
                $url .= '?'.$str_data;
                $header[] = "Content-Type: application/json";
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                if (is_array($data)) $data = json_encode($data);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
            }else  if ($method == 'delete')
            {
                $str_data = http_build_query($data);
                $header[] = "Content-Type: application/x-www-form-urlencoded";
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $str_data);
            }
        }


        //echo $url."<Br>";
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
        if ($usecookie)
        {
            curl_setopt($ch, CURLOPT_COOKIEJAR, $usecookie);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $usecookie);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        //curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_STDERR, fopen('php://stderr', 'w'));
        $result =curl_exec ($ch);
        //echo curl_error ($ch );
        curl_close ($ch);
        return $result;
    }

}