<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PaymentAdjustment
 *
 * @ORM\Table(name="payment_adjustment", indexes={@ORM\Index(name="payment_status_id", columns={"payment_status_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\PaymentAdjustmentRepository")
 */
class PaymentAdjustment
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
     * @ORM\Column(name="order_id", type="string", length=50, nullable=true)
     */
    private $orderId;

    /**
     * @var string
     *
     * @ORM\Column(name="user_type", type="string", length=50, nullable=true)
     */
    private $userType;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="decimal", precision=13, scale=4, nullable=true)
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="remark", type="string", length=200, nullable=true)
     */
    private $remark;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_type", type="string", length=20, nullable=true)
     */
    private $paymentType;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_on", type="datetime", nullable=true)
     */
    private $updatedOn;

    /**
     * @var \PaymentStatus
     *
     * @ORM\ManyToOne(targetEntity="PaymentStatus")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="payment_status_id", referencedColumnName="id")
     * })
     */
    private $paymentStatus;



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
     * Set orderId
     *
     * @param string $orderId
     * @return PaymentAdjustment
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * Get orderId
     *
     * @return string 
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Set userType
     *
     * @param string $userType
     * @return PaymentAdjustment
     */
    public function setUserType($userType)
    {
        $this->userType = $userType;

        return $this;
    }

    /**
     * Get userType
     *
     * @return string 
     */
    public function getUserType()
    {
        return $this->userType;
    }

    /**
     * Set amount
     *
     * @param string $amount
     * @return PaymentAdjustment
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return string 
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set remark
     *
     * @param string $remark
     * @return PaymentAdjustment
     */
    public function setRemark($remark)
    {
        $this->remark = $remark;

        return $this;
    }

    /**
     * Get remark
     *
     * @return string 
     */
    public function getRemark()
    {
        return $this->remark;
    }

    /**
     * Set paymentType
     *
     * @param string $paymentType
     * @return PaymentAdjustment
     */
    public function setPaymentType($paymentType)
    {
        $this->paymentType = $paymentType;

        return $this;
    }

    /**
     * Get paymentType
     *
     * @return string 
     */
    public function getPaymentType()
    {
        return $this->paymentType;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return PaymentAdjustment
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get createdOn
     *
     * @return \DateTime 
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * Set updatedOn
     *
     * @param \DateTime $updatedOn
     * @return PaymentAdjustment
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

    /**
     * Set paymentStatus
     *
     * @param \UtilBundle\Entity\PaymentStatus $paymentStatus
     * @return PaymentAdjustment
     */
    public function setPaymentStatus(\UtilBundle\Entity\PaymentStatus $paymentStatus = null)
    {
        $this->paymentStatus = $paymentStatus;

        return $this;
    }

    /**
     * Get paymentStatus
     *
     * @return \UtilBundle\Entity\PaymentStatus 
     */
    public function getPaymentStatus()
    {
        return $this->paymentStatus;
    }
}
