<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PaymentGatewaySettlement
 *
 * @ORM\Table(name="payment_gateway_settlement")
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\PaymentGatewaySettlementRepository")
 */
class PaymentGatewaySettlement
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
     * @ORM\Column(name="order_number", type="string", length=50, nullable=true)
     */
    private $orderNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="transaction_ref", type="string", length=50, nullable=true)
     */
    private $transactionRef;

    /**
     * @var string
     *
     * @ORM\Column(name="expected_amount", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $expectedAmount;
    
    /**
     * @var string
     *
     * @ORM\Column(name="settlement_amount_updated", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $settlementAmountUpdated;

    /**
     * @var string
     *
     * @ORM\Column(name="settlement_amount", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $settlementAmount;
	
    /**
     * @var string
     *
     * @ORM\Column(name="transaction_gross_amount", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $transactionGrossAmount;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="settlement_date", type="datetime", nullable=true)
     */
    private $settlementDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="transaction_date", type="datetime", nullable=true)
     */
    private $transactionDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private $status;

    /**
     * @var integer
     *
     * @ORM\Column(name="settlement_type", type="integer", nullable=true)
     */
    private $settlementType;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="synced_on", type="datetime", nullable=true)
     */
    private $syncedOn;
    /**

     * @var string
     *
     * @ORM\Column(name="remark", type="string", length=200, nullable=true)
     */
    private $remark;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_method", type="string", length=10, nullable=true)
     */
    private $paymentMethod;

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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set orderNumber
     *
     * @param integer $orderNumber
     * @return PaymentGatewaySettlement
     */
    public function setOrderNumber($orderNumber)
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }

    /**
     * Get orderNumber
     *
     * @return string
     */
    public function getOrderNumber()
    {
        return $this->orderNumber;
    }

    /**
     * Set transactionRef
     *
     * @param string $transactionRef
     * @return PaymentGatewaySettlement
     */
    public function setTransactionRef($transactionRef)
    {
        $this->transactionRef = $transactionRef;

        return $this;
    }

    /**
     * Get transactionRef
     *
     * @return string
     */
    public function getTransactionRef()
    {
        return $this->transactionRef;
    }

    /**
     * Set expectedAmount
     *
     * @param string $expectedAmount
     * @return PaymentGatewaySettlement
     */
    public function setExpectedAmount($expectedAmount)
    {
        $this->expectedAmount = $expectedAmount;

        return $this;
    }

    /**
     * Get expectedAmount
     *
     * @return string 
     */
    public function getExpectedAmount()
    {
        return $this->expectedAmount;
    }

    /**
     * Set settlementAmountUpdated
     *
     * @param string $settlementAmountUpdated
     * @return PaymentGatewaySettlement
     */
    public function setSettlementAmountUpdated($settlementAmountUpdated)
    {
        $this->settlementAmountUpdated = $settlementAmountUpdated;

        return $this;
    }

    /**
     * Get settlementAmountUpdated
     *
     * @return string 
     */
    public function getSettlementAmountUpdated()
    {
        return $this->settlementAmountUpdated;
    }

    /**
     * Set settlementAmount
     *
     * @param string $settlementAmount
     * @return PaymentGatewaySettlement
     */
    public function setSettlementAmount($settlementAmount)
    {
        $this->settlementAmount = $settlementAmount;

        return $this;
    }

    /**
     * Get settlementAmount
     *
     * @return string 
     */
    public function getSettlementAmount()
    {
        return $this->settlementAmount;
    }
	
    /**
     * Set transactionGrossAmount
     *
     * @param string $transactionGrossAmount
     * @return PaymentGatewaySettlement
     */
    public function setTransactionGrossAmount($transactionGrossAmount)
    {
        $this->transactionGrossAmount = $transactionGrossAmount;

        return $this;
    }

    /**
     * Get transactionGrossAmount
     *
     * @return string 
     */
    public function getTransactionGrossAmount()
    {
        return $this->transactionGrossAmount;
    }

    /**
     * Set settlementDate
     *
     * @param \DateTime $settlementDate
     * @return PaymentGatewaySettlement
     */
    public function setSettlementDate($settlementDate)
    {
        $this->settlementDate = $settlementDate;

        return $this;
    }

    /**
     * Get settlementDate
     *
     * @return \DateTime 
     */
    public function getSettlementDate()
    {
        return $this->settlementDate;
    }

    /**
     * Set transactionDate
     *
     * @param \DateTime $transactionDate
     * @return PaymentGatewaySettlement
     */
    public function setTransactionDate($transactionDate)
    {
        $this->transactionDate = $transactionDate;

        return $this;
    }

    /**
     * Get transactionDate
     *
     * @return \DateTime 
     */
    public function getTransactionDate()
    {
        return $this->transactionDate;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return PaymentGatewaySettlement
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set settlementType
     *
     * @param integer $settlementType
     * @return PaymentGatewaySettlement
     */
    public function setSettlementType($settlementType)
    {
        $this->settlementType = $settlementType;

        return $this;
    }

    /**
     * Get settlementType
     *
     * @return integer
     */
    public function getSettlementType()
    {
        return $this->settlementType;
    }

    /**
     * Set remax
     *
     * @param string $remark
     * @return PaymentGatewaySettlement
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
     * Set paymentMethod
     *
     * @param string $paymentMethod
     * @return PaymentGatewaySettlement
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    /**
     * Get paymentMethod
     *
     * @return string 
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return PaymentGatewaySettlement
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
     * @return PaymentGatewaySettlement
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
     * Set syncedOn
     *
     * @param \DateTime $syncedOn
     *
     * @return PaymentGatewaySettlement
     */
    public function setSyncedOn($syncedOn)
    {
        $this->syncedOn = $syncedOn;

        return $this;
    }

    /**
     * Get syncedOn
     *
     * @return \DateTime
     */
    public function getSyncedOn()
    {
        return $this->syncedOn;
    }
}
