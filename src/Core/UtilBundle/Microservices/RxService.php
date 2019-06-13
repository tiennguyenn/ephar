<?php

namespace UtilBundle\Microservices;

class RxService extends RouterV2Service
{
    protected $refillUrl = '/callback_refill';
    protected $orderStatusUrl = '/callback_order_status';

    public function notifyPartnerForRefill($params)
    {
        $url = $this->baseUrl . $this->refillUrl;

        $resp = $this->getDataFromApi($url, 'post', $params);

        return json_decode($resp['data']);
    }

    public function notifyPartnerUpdateStatus($params)
    {
        $url = $this->baseUrl . $this->orderStatusUrl;

        $resp = $this->getDataFromApi($url, 'post', $params);

        return json_decode($resp['data']);
    }
}