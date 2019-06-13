<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PaymentGatewayWeeklyLog
 *
 * @ORM\Table(name="payment_gateway_weekly_log", indexes={@ORM\Index(name="FK_payment_gateway_weekly_log", columns={"payment_gateway_weekly_id"})})
 * @ORM\Entity
 */
class PaymentGatewayWeeklyLog
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
     * @ORM\Column(name="created_by", type="string", length=250, nullable=true)
     */
    private $createdBy;

    /**
     * @var string
     *
     * @ORM\Column(name="notes", type="text", length=65535, nullable=true)
     */
    private $notes;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var \PaymentGatewayWeekly
     *
     * @ORM\ManyToOne(targetEntity="PaymentGatewayWeekly")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="payment_gateway_weekly_id", referencedColumnName="id")
     * })
     */
    private $paymentGatewayWeekly;



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
     * Set createdBy
     *
     * @param string $createdBy
     *
     * @return PaymentGatewayWeeklyLog
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return string
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set notes
     *
     * @param string $notes
     *
     * @return PaymentGatewayWeeklyLog
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get notes
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return PaymentGatewayWeeklyLog
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
     * Set paymentGatewayWeekly
     *
     * @param \UtilBundle\Entity\PaymentGatewayWeekly $paymentGatewayWeekly
     *
     * @return PaymentGatewayWeeklyLog
     */
    public function setPaymentGatewayWeekly(\UtilBundle\Entity\PaymentGatewayWeekly $paymentGatewayWeekly = null)
    {
        $this->paymentGatewayWeekly = $paymentGatewayWeekly;

        return $this;
    }

    /**
     * Get paymentGatewayWeekly
     *
     * @return \UtilBundle\Entity\PaymentGatewayWeekly
     */
    public function getPaymentGatewayWeekly()
    {
        return $this->paymentGatewayWeekly;
    }
}
