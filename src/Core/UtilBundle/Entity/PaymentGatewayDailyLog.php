<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PaymentGatewayDailyLog
 *
 * @ORM\Table(name="payment_gateway_daily_log", indexes={@ORM\Index(name="FK_payment_gateway_daily_log", columns={"payment_gateway_daily_id"})})
 * @ORM\Entity
 */
class PaymentGatewayDailyLog
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
     * @var \PaymentGatewayDaily
     *
     * @ORM\ManyToOne(targetEntity="PaymentGatewayDaily")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="payment_gateway_daily_id", referencedColumnName="id")
     * })
     */
    private $PaymentGatewayDaily;



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
     * @return PaymentGatewayDailyLog
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
     * @return PaymentGatewayDailyLog
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
     * @return PaymentGatewayDailyLog
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
     * Set PaymentGatewayDaily
     *
     * @param \UtilBundle\Entity\PaymentGatewayDaily $PaymentGatewayDaily
     *
     * @return PaymentGatewayDailyLog
     */
    public function setPaymentGatewayDaily(\UtilBundle\Entity\PaymentGatewayDaily $PaymentGatewayDaily = null)
    {
        $this->PaymentGatewayDaily = $PaymentGatewayDaily;

        return $this;
    }

    /**
     * Get PaymentGatewayDaily
     *
     * @return \UtilBundle\Entity\PaymentGatewayDaily
     */
    public function getPaymentGatewayDaily()
    {
        return $this->PaymentGatewayDaily;
    }
}
