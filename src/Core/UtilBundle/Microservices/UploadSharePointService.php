<?php

namespace UtilBundle\Microservices;

use UtilBundle\Microservices\RouterService;
use UtilBundle\Utility\Constant;
use UtilBundle\Utility\Common;

class UploadSharePointService extends RouterService
{

    public function executeUploadAction($params = [])
    {
        $result = $this->postData( $this->sharePointUrl, $params);
        return $result;
    }

}