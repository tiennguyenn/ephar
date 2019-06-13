<?php

namespace AdminBundle\Libs;

use Symfony\Component\Yaml\Yaml;

class Config {

  const CONFIG_PATH = 'Resources/configdata/';  
  private static $configData =null;
  private static $isLoanedOut = FALSE;

  /**
   * Get root path
   * @return string ~/DemoBundle/
   */
  public static function rootPath()
  {
    return __DIR__.'/../';
  }
 

  /**
   * Get $key value from common config file
   * @param $key
   * @return null
   */
  public static function get($index)
  {
    if(FALSE == self::$isLoanedOut)
    {
        self::$configData = self::loadConfig("app");
        self::$isLoanedOut = TRUE;
    }
    $data = self::$configData;
    if(is_array($data) && isset($data[$index]))
    {
      $result = array();
      foreach ($data[$index] as $key => $value) {
        $result[$value['value']] = array('code'=>$key,'name'=>$value['name']);
      }
      return $result;
    }
    return FALSE;
  }

  /**
   * Get config params from DemoBundle/Reosurce/config/folder_name
   * @param $folderName
   * @param $paramKey
   * @return null
   */
  public static function loadConfig( $file)
  {
    $folderPath = self::rootPath() . self::CONFIG_PATH.$file.'.yml';
    $paramValue = Yaml::parse($folderPath);
   
    return $paramValue;
  } 

} 