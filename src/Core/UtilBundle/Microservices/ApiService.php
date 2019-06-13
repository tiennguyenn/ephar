<?php
namespace UtilBundle\Microservices;

use UtilBundle\Utility\Curl;
use UtilBundle\Utility\Common;
use UtilBundle\Utility\MsgUtils;
use UtilBundle\Utility\Constant;

class ApiService
{
    protected $container;
    protected $asiaPayAPI;
    protected $masAPI;
    protected $molPayAPI;

    public function __construct($container)
    {
        $this->container  = $container;
        $this->asiaPayAPI = $this->container->getParameter('asia_pay_api');
        $this->masAPI     = $this->container->getParameter('mas_api');
        $this->molPayAPI = $this->container->getParameter('mol_pay_api');
        $this->reddotAPI = $this->container->getParameter('red_dot_api');
    }

    /**
     * GET data from API
     * @author vinh.nguyen
     */
    public function _getData($url, $params = array())
    {
        if(!empty($params)) {
            if(strpos($url, "?") === false)
                $url = $url . '?' . http_build_query($params);
            else
                $url = $url . '&' . http_build_query($params);
        }
        $url = preg_replace('/%5B[0-9]+%5D/simU', '%5B%5D', $url);
        $cUrl = new Curl();
        $cUrl->setMethod($cUrl::HTTP_GET);

        $result = $cUrl->call($url);

        return $result;
    }

    /**
     * POST data to API
     * @author vinh.nguyen
     */
    protected function _postData($url, $params = array())
    {
        $cUrl = new Curl();
        $cUrl->setMethod($cUrl::HTTP_POST);
        $cUrl->setUsingJson();
        $result = $cUrl->call($url, $params);
        return $result;
    }
    /**
     * POST data to API
     * @author bien .mai
     */
    protected function postData($url, $params = array())
    {
        $cUrl = new Curl();
        $cUrl->setMethod($cUrl::HTTP_POST);
        $cUrl->setUsingJson();
        $result = $cUrl->call($url, $params);

        return ['data' => $result, 'message'=> $cUrl->getLastError()];
    }
    /**
     * PUT data to API
     * @author vinh.nguyen
     */
    protected function _putData($url, $params = array())
    {
        $cUrl = new Curl();
        $cUrl->setMethod($cUrl::HTTP_PUT);
        $cUrl->setUsingJson();
        $result = $cUrl->call($url, $params);

        return $result;
    }

    /**
     * DELETE data to API
     * @author vinh.nguyen
     */
    protected function _deleteData($url, $params = array())
    {
        $cUrl = new Curl();
        $cUrl->setMethod($cUrl::HTTP_DELETE);
        $cUrl->setUsingJson();

        $result = $cUrl->call($url, $params);

        return $result;
    }

    /**
     * Get currency exchange
     * @return type
     * author luyen nguyen
     */
    public function getCurrencyExchange($date) {
        $url = $this->masAPI['eservices_url'];
        $params['resource_id'] = $this->masAPI['resource_id'];
        $params['limit'] = Constant::LIMIT;
        $params['between[end_of_day]'] = $date . ',' . $date;
        $result = json_decode($this->_getData($url, $params), true);
        if ($result['success']) {
            if(isset($result['result']['records'][0]))
                return $result['result']['records'][0];
        }
        return null;
    }
}