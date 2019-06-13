<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PaymentDateSetting
 *
 * @ORM\Table(name="payment_date_setting")
 * @ORM\Entity
 */
class PaymentDateSetting
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="pay_doctors", type="integer", nullable=true)
     */
    private $payDoctors;

    /**
     * @var integer
     *
     * @ORM\Column(name="pay_agents", type="integer", nullable=true)
     */
    private $payAgents;

    /**
     * @var integer
     *
     * @ORM\Column(name="pay_pharmacy", type="integer", nullable=true)
     */
    private $payPharmacy;

    /**
     * @var integer
     *
     * @ORM\Column(name="pay_courier", type="integer", nullable=true)
     */
    private $payCourier;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_on", type="datetime", nullable=true)
     */
    private $updatedOn;



    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set payDoctors
     *
     * @param integer $payDoctors
     * @return PaymentDateSetting
     */
    public function setPayDoctors($payDoctors)
    {
        $this->payDoctors = $payDoctors;

        return $this;
    }

    /**
     * Get payDoctors
     *
     * @return integer 
     */
    public function getPayDoctors()
    {
        return $this->payDoctors;
    }

    /**
     * Set payAgents
     *
     * @param integer $payAgents
     * @return PaymentDateSetting
     */
    public function setPayAgents($payAgents)
    {
        $this->payAgents = $payAgents;

        return $this;
    }

    /**
     * Get payAgents
     *
     * @return integer 
     */
    public function getPayAgents()
    {
        return $this->payAgents;
    }

    /**
     * Set payPharmacy
     *
     * @param integer $payPharmacy
     * @return PaymentDateSetting
     */
    public function setPayPharmacy($payPharmacy)
    {
        $this->payPharmacy = $payPharmacy;

        return $this;
    }

    /**
     * Get payPharmacy
     *
     * @return integer 
     */
    public function getPayPharmacy()
    {
        return $this->payPharmacy;
    }

    /**
     * Set payCourier
     *
     * @param integer $payCourier
     * @return PaymentDateSetting
     */
    public function setPayCourier($payCourier)
    {
        $this->payCourier = $payCourier;

        return $this;
    }

    /**
     * Get payCourier
     *
     * @return integer 
     */
    public function getPayCourier()
    {
        return $this->payCourier;
    }

    /**
     * Set updatedOn
     *
     * @param \DateTime $updatedOn
     * @return PaymentDateSetting
     */
    public function setUpdatedOn($updatedOn)
    {
        $this->updatedOn = $updatedOn;

        return $this;
    }

    /**
     * Get updatedOn
     *
     * @return \DateTime 
     */
    public function getUpdatedOn()
    {
        return $this->updatedOn;
    }
}
