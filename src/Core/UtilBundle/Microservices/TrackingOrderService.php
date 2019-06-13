<?php

namespace UtilBundle\Microservices;

use UtilBundle\Microservices\RouterService;

class TrackingOrderService extends RouterService
{
    const API_ORDER = '/orders/';

    public function getListRxStatusLog($params)
    {
        $url = $this->baseUrl . self::API_ORDER.$params['order_number'];
        $resp = $this->getDataFromApi($url, 'get', $params);
        return json_decode($resp['data'], true);
    }
}