<?php
namespace UtilBundle\Microservices;

use Unirest\Request;

class GmedRequest extends Request
{

    public static function putData($url, $headers = array(), $body = null, $username = null, $password = null){
        return self::put($url, $headers, $body , $username, $password );
    }

    public static function postData($url, $headers = array(), $body = null, $username = null, $password = null)
    {
        return self::post($url, $headers , $body , $username , $password );
    }

    public static function getData($url, $headers = array(), $parameters = null, $username = null, $password = null)
    {
        return self::get($url, $headers , $parameters , $username , $password );
    }

    public static function deleteData($url, $headers = array(), $body = null, $username = null, $password = null)
    {
        return self::delete($url, $headers, $body , $username , $password );
    }
}