<?php

namespace AdminBundle\EventListener;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use UtilBundle\Entity\Rx;

class RxListener
{
    public $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function preUpdate(PreUpdateEventArgs $eventArgs)
    {
        $entity = $eventArgs->getObject();

        if (!$entity instanceof Rx) {
            return;
        }

        if (!$eventArgs->hasChangedField('isOnHold')) {
            return;
        }

        $isOnHold = $entity->getIsOnHold();
        if (empty($isOnHold)) {
            return;
        }

        $noteType = 'onhold';
        if ($isOnHold > 1) {
            $noteType = 'resolved';
        }

        $orderPhysicalNumber = $entity->getOrderPhysicalNumber();

        $apiUrl = $this->container->getParameter('atc_order_notify_api');
        $data = [
            'order_number' => $orderPhysicalNumber,
            'note_type' => $noteType,
            'access_token' => $this->generateAccessToken()
        ];

        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $result = curl_exec($ch);
        curl_close($ch);

        $this->container->get('logger')->addInfo('Post request to ATC');
        $this->container->get('logger')->addInfo('Order :' . $orderPhysicalNumber);
        $this->container->get('logger')->addInfo('Result :' . $result);
    }

    private function generateAccessToken()
    {
        return 'bedc0131d424b419fc4c4dcd3058a298';
    }
}