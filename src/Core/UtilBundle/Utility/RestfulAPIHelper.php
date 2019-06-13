<?php

/**
 * RestfulAPIHelper class to help in calling RnR Api
 *
 */

namespace UtilBundle\Utility;

class RestfulAPIHelper
{

    // Restful API Helper specific constants
    const RESTFUL_API_ERROR = 'RESTFUL_API_ERROR';
    const RESTFUL_API_GET = 'RESTFUL_API_GET';
    const RESTFUL_API_POST_JSON = 'RESTFUL_API_POST_JSON';
    const RESTFUL_API_POST_AUTHENTICATE = 'RESTFUL_API_POST_AUTHENTICATE';
    const RESTFUL_API_PUT = 'RESTFUL_API_PUT';
    const RESTFUL_API_DELETE = 'RESTFUL_API_DELETE';
    const RESTFUL_API_POST = 'RESTFUL_API_POST';
    // HTTP Status Code constants
    const HTTP_CONTINUE = 100;
    const HTTP_SWITCHING_PROTOCOLS = 101;
    const HTTP_PROCESSING = 102;            // RFC2518
    const HTTP_OK = 200;
    const HTTP_CREATED = 201;
    const HTTP_ACCEPTED = 202;
    const HTTP_NON_AUTHORITATIVE_INFORMATION = 203;
    const HTTP_NO_CONTENT = 204;
    const HTTP_RESET_CONTENT = 205;
    const HTTP_PARTIAL_CONTENT = 206;
    const HTTP_MULTI_STATUS = 207;          // RFC4918
    const HTTP_ALREADY_REPORTED = 208;      // RFC5842
    const HTTP_IM_USED = 226;               // RFC3229
    const HTTP_MULTIPLE_CHOICES = 300;
    const HTTP_MOVED_PERMANENTLY = 301;
    const HTTP_FOUND = 302;
    const HTTP_SEE_OTHER = 303;
    const HTTP_NOT_MODIFIED = 304;
    const HTTP_USE_PROXY = 305;
    const HTTP_RESERVED = 306;
    const HTTP_TEMPORARY_REDIRECT = 307;
    const HTTP_PERMANENTLY_REDIRECT = 308;  // RFC-reschke-http-status-308-07
    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_PAYMENT_REQUIRED = 402;
    const HTTP_FORBIDDEN = 403;
    const HTTP_NOT_FOUND = 404;
    const HTTP_METHOD_NOT_ALLOWED = 405;
    const HTTP_NOT_ACCEPTABLE = 406;
    const HTTP_PROXY_AUTHENTICATION_REQUIRED = 407;
    const HTTP_REQUEST_TIMEOUT = 408;
    const HTTP_CONFLICT = 409;
    const HTTP_GONE = 410;
    const HTTP_LENGTH_REQUIRED = 411;
    const HTTP_PRECONDITION_FAILED = 412;
    const HTTP_REQUEST_ENTITY_TOO_LARGE = 413;
    const HTTP_REQUEST_URI_TOO_LONG = 414;
    const HTTP_UNSUPPORTED_MEDIA_TYPE = 415;
    const HTTP_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    const HTTP_EXPECTATION_FAILED = 417;
    const HTTP_I_AM_A_TEAPOT = 418;         // RFC2324
    const HTTP_UNPROCESSABLE_ENTITY = 422;  // RFC4918
    const HTTP_LOCKED = 423;                // RFC4918
    const HTTP_FAILED_DEPENDENCY = 424;     // RFC4918
    const HTTP_RESERVED_FOR_WEBDAV_ADVANCED_COLLECTIONS_EXPIRED_PROPOSAL = 425;   // RFC2817
    const HTTP_UPGRADE_REQUIRED = 426;      // RFC2817
    const HTTP_PRECONDITION_REQUIRED = 428; // RFC6585
    const HTTP_TOO_MANY_REQUESTS = 429;     // RFC6585
    const HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;   // RFC6585
    const HTTP_INTERNAL_SERVER_ERROR = 500;
    const HTTP_NOT_IMPLEMENTED = 501;
    const HTTP_BAD_GATEWAY = 502;
    const HTTP_SERVICE_UNAVAILABLE = 503;
    const HTTP_GATEWAY_TIMEOUT = 504;
    const HTTP_VERSION_NOT_SUPPORTED = 505;
    const HTTP_VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL = 506;  // RFC2295
    const HTTP_INSUFFICIENT_STORAGE = 507;  // RFC4918
    const HTTP_LOOP_DETECTED = 508;         // RFC5842
    const HTTP_NOT_EXTENDED = 510;          // RFC2774
    const HTTP_NETWORK_AUTHENTICATION_REQUIRED = 511;   // RFC6585
    const AUTH_OK = 1;
    const AUTH_SESSION_INVALID = 2;
    const AUTH_SESSION_EXPIRE = 3;
    const SESSION_TOKEN_TIMEOUT = 419;
    const USER_TOKEN_TIMEOUT = 420;
    const TBL_STATUS_OK = 0;

    /**
     * Send the actual HTTP POST to TableDB API
     *
     * @return Returns the result from json_decode of RnR Api's response
     * @throws RestfulAPIException	Error sending HTTP POST
     */
    public static function doGet($url)
    {
        try {
            // initialize cURL
            $ch = curl_init();

            // set options for cURL
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);

            // execute HTTP POST request
            $response = curl_exec($ch);
            // close connection

            curl_close($ch);

            return self::parseResult($response);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Send the actual HTTP GET to RnR API
     *
     * @return Returns the result from json_decode of RnR Api's response
     * @throws RestfulAPIException	Error sending HTTP GET
     */
    public static function doPost($url, $fields, $post_method = self::RESTFUL_API_POST_JSON)
    {
        try {
            // initialize cURL
            $ch = curl_init();
            //Convert json object from params
            $calling_string = self::getJsonObject($fields);

            //For other POST with JSON in body
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $calling_string);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($calling_string))
            );

            //var_dump($ch);exit;
            if (self::RESTFUL_API_PUT == $post_method) {
                //For PUT method
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            } else {
                //For POST & POST JSON method
                curl_setopt($ch, CURLOPT_POST, true);
            }

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);

            // execute HTTP POST request
            $response = curl_exec($ch);
            // close connection
            //curl_close($ch);

            return self::parseResult($response);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * form up variables in the correct format for HTTP
     *
     * @param $fields
     * @return $fields_string ( '&' seperate string )
     */
    private static function getJsonObject($fields)
    {
        return json_encode($fields);
    }

    /**
     * Throw RestfulAPIException if there are errors.
     *
     * @return Returns the result from json_decode of RnR Api's response
     * @throws RestfulAPIException   Error sending HTTP POST
     */
    public static function parseResult($response)
    {
        if ($response == '' || $response == false) {
            //return TIMEOUT Exception
            throw new RestfulAPIException(RestfulAPIException::RESTFUL_API_TIME_OUT);
        } else {
            return json_decode($response, true);
        }
    }

    /**
     * Generate API Signature ( Copy form Authenicate API )
     *
     * @author  Co Vu Thanh Tung
     * @param   string $params
     * @param   array  $pathInfo
     * @return  string
     */
    private static function genSignature($params, $pathinfo)
    {
        $result = $pathinfo;

        ksort($params);
        foreach ($params as $key => $value) {
            $result .= $key . urlencode($value);
        }

        return md5($result);
    }

    /**
     * Generate API Signature for Content-Type : application/json ( Copy form Authenicate API )
     *
     * @author  Co Vu Thanh Tung
     * @param   string $params
     * @param   array  $pathInfo
     * @return  string
     */
    private static function genSignatureJson($secret, $params, $pathinfo)
    {
        $params = json_encode($params);
        $params = str_replace("\n", '', $params);
        $params = str_replace("\r", '', $params);

        $pathinfo = $secret . $pathinfo;

        return md5($pathinfo . $params);
    }

}
