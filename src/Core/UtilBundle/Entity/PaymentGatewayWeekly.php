<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PaymentGatewayWeekly
 *
 * @ORM\Table(name="payment_gateway_weekly")
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\PaymentGatewayWeeklyRepository")
 */
class PaymentGatewayWeekly
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
     * @var \DateTime
     *
     * @ORM\Column(name="start_period", type="date", nullable=false)
     */
    private $startPeriod;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_period", type="date", nullable=false)
     */
    private $endPeriod;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expected_settlement_date", type="date", nullable=true)
     */
    private $expectedSettlementDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="actual_settelement_date", type="date", nullable=true)
     */
    private $actualSettelementDate;

    /**
     * @var string
     *
     * @ORM\Column(name="expected_amount_due", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $expectedAmountDue;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_gateway_amount_due", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $paymentGatewayAmountDue;

    /**
     * @var string
     *
     * @ORM\Column(name="bank_settlement", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $bankSettlement;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;



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
     * Set startPeriod
     *
     * @param \DateTime $startPeriod
     *
     * @return PaymentGatewayWeekly
     */
    public function setStartPeriod($startPeriod)
    {
        $this->startPeriod = $startPeriod;

        return $this;
    }

    /**
     * Get startPeriod
     *
     * @return \DateTime
     */
    public function getStartPeriod()
    {
        return $this->startPeriod;
    }

    /**
     * Set endPeriod
     *
     * @param \DateTime $endPeriod
     *
     * @return PaymentGatewayWeekly
     */
    public function setEndPeriod($endPeriod)
    {
        $this->endPeriod = $endPeriod;

        return $this;
    }

    /**
     * Get endPeriod
     *
     * @return \DateTime
     */
    public function getEndPeriod()
    {
        return $this->endPeriod;
    }

    /**
     * Set expectedSettlementDate
     *
     * @param \DateTime $expectedSettlementDate
     *
     * @return PaymentGatewayWeekly
     */
    public function setExpectedSettlementDate($expectedSettlementDate)
    {
        $this->expectedSettlementDate = $expectedSettlementDate;

        return $this;
    }

    /**
     * Get expectedSettlementDate
     *
     * @return \DateTime
     */
    public function getExpectedSettlementDate()
    {
        return $this->expectedSettlementDate;
    }

    /**
     * Set actualSettelementDate
     *
     * @param \DateTime $actualSettelementDate
     *
     * @return PaymentGatewayWeekly
     */
    public function setActualSettelementDate($actualSettelementDate)
    {
        $this->actualSettelementDate = $actualSettelementDate;

        return $this;
    }

    /**
     * Get actualSettelementDate
     *
     * @return \DateTime
     */
    public function getActualSettelementDate()
    {
        return $this->actualSettelementDate;
    }

    /**
     * Set expectedAmountDue
     *
     * @param string $expectedAmountDue
     *
     * @return PaymentGatewayWeekly
     */
    public function setExpectedAmountDue($expectedAmountDue)
    {
        $this->expectedAmountDue = $expectedAmountDue;

        return $this;
    }

    /**
     * Get expectedAmountDue
     *
     * @return string
     */
    public function getExpectedAmountDue()
    {
        return $this->expectedAmountDue;
    }

    /**
     * Set paymentGatewayAmountDue
     *
     * @param string $paymentGatewayAmountDue
     *
     * @return PaymentGatewayWeekly
     */
    public function setPaymentGatewayAmountDue($paymentGatewayAmountDue)
    {
        $this->paymentGatewayAmountDue = $paymentGatewayAmountDue;

        return $this;
    }

    /**
     * Get paymentGatewayAmountDue
     *
     * @return string
     */
    public function getPaymentGatewayAmountDue()
    {
        return $this->paymentGatewayAmountDue;
    }

    /**
     * Set bankSettlement
     *
     * @param string $bankSettlement
     *
     * @return PaymentGatewayWeekly
     */
    public function setBankSettlement($bankSettlement)
    {
        $this->bankSettlement = $bankSettlement;

        return $this;
    }

    /**
     * Get bankSettlement
     *
     * @return string
     */
    public function getBankSettlement()
    {
        return $this->bankSettlement;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return PaymentGatewayWeekly
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
}
