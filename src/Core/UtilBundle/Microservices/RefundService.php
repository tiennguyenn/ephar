<?php

namespace UtilBundle\Microservices;

use UtilBundle\Microservices\RouterService;
use UtilBundle\Utility\Constant;
use UtilBundle\Utility\Common;

class RefundService extends RouterService
{

    public function executeApprovalRefundAction($params = [])
    {
        $url = "https://prod-01.southeastasia.logic.azure.com:443/workflows/f692753ad992405e9022785657cb3523/triggers/manual/paths/invoke?api-version=2016-06-01&sp=%2Ftriggers%2Fmanual%2Frun&sv=1.0&sig=ZUycFS4G_zYr1WupSFZOAJqEV90LYKYVmKe2JuRrYVE";
        $result = $this->postData($this->approvalUrl, $params);
        return $result;
    }

    public function executeRxInfoRefundAction($params = [])
    {
        $url = "https://prod-13.southeastasia.logic.azure.com:443/workflows/5a2bb755f21149a8aa3dcbae38b3881d/triggers/manual/paths/invoke?api-version=2016-06-01&sp=%2Ftriggers%2Fmanual%2Frun&sv=1.0&sig=mMZhYriFrie7u7IxdNj8tfVWRr60tuDEYQr3V3VGQc8";
        $result = $this->postData($this->sharePointUrl, $params);
        return $result;
    }

    //root: .../api/v1
    ///get_token

    public function getUsedToken()
    {
         $url = $this->baseUrl. API_Get_Token;
         $resp = $this->getDataFromApi($url, 'get');
         return json_decode($resp['data']);

    }


    
    public function executeApprovalInvoicePartyAction($params = [])
    {
        $result = $this->postData($this->invoicePartyUrl, $params);
        return $result;
    }

    public function pharmacistUpdateRedispenseAction($params = [])
    {
        $result = $this->postData($this->pharmacistRedispenseUrl, $params);
        return $result;
    }

}
