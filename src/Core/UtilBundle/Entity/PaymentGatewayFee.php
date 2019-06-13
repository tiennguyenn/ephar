<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PaymentGatewayFee
 *
 * @ORM\Table(name="payment_gateway_fee", indexes={@ORM\Index(name="FK_payment_gateway_fee", columns={"fee_setting_id"}), @ORM\Index(name="FK_payment_gateway_fee_country", columns={"country_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\PaymentGatewayFeeRepository")
 */
class PaymentGatewayFee
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
     * @var string
     *
     * @ORM\Column(name="payment_method", type="string", length=10,  nullable=true)
     */
    private $paymentMethod;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=50, nullable=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_gate", type="string", length=50, nullable=true)
     */
    private $paymentGate;

    /**
     * @var \Country
     *
     * @ORM\ManyToOne(targetEntity="Country",cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="country_id", referencedColumnName="id")
     * })
     */
    private $country;

    /**
     * @var \FeeSetting
     *
     * @ORM\ManyToOne(targetEntity="FeeSetting",cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fee_setting_id", referencedColumnName="id")
     * })
     */
    private $feeSetting;



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
     * Set paymentMethod
     *
     * @param boolean $paymentMethod
     * @return PaymentGatewayFee
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    /**
     * Get paymentMethod
     *
     * @return boolean 
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return PaymentGatewayFee
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return PaymentGatewayFee
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set country
     *
     * @param \UtilBundle\Entity\Country $country
     * @return PaymentGatewayFee
     */
    public function setCountry(\UtilBundle\Entity\Country $country = null)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return \UtilBundle\Entity\Country 
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set feeSetting
     *
     * @param \UtilBundle\Entity\FeeSetting $feeSetting
     * @return PaymentGatewayFee
     */
    public function setFeeSetting(\UtilBundle\Entity\FeeSetting $feeSetting = null)
    {
        $this->feeSetting = $feeSetting;

        return $this;
    }

    /**
     * Get feeSetting
     *
     * @return \UtilBundle\Entity\FeeSetting 
     */
    public function getFeeSetting()
    {
        return $this->feeSetting;
    }

    /**
     * Set paymentGate
     *
     * @param string $paymentGate
     *
     * @return PaymentGatewayFee
     */
    public function setPaymentGate($paymentGate)
    {
        $this->paymentGate = $paymentGate;

        return $this;
    }

    /**
     * Get paymentGate
     *
     * @return string
     */
    public function getPaymentGate()
    {
        return $this->paymentGate;
    }
}
