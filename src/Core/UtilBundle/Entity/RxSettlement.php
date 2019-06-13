<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RxSettlement
 *
 * @ORM\Table(name="rx_settlement", indexes={@ORM\Index(name="FK_rx_settlement", columns={"rx_id"})})
 * @ORM\Entity
 */
class RxSettlement
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
     * @ORM\Column(name="rx_id", type="integer", nullable=true)
     */
    private $rxId;

    /**
     * @var string
     *
     * @ORM\Column(name="settlement_reference", type="string", length=50, nullable=true)
     */
    private $settlementReference;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="settled_on", type="datetime", nullable=true)
     */
    private $settledOn;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="decimal", precision=14, scale=2, nullable=true)
     */
    private $amount;

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
     * Set rxId
     *
     * @param integer $rxId
     * @return RxSettlement
     */
    public function setRxId($rxId)
    {
        $this->rxId = $rxId;

        return $this;
    }

    /**
     * Get rxId
     *
     * @return integer 
     */
    public function getRxId()
    {
        return $this->rxId;
    }

    /**
     * Set settlementReference
     *
     * @param string $settlementReference
     * @return RxSettlement
     */
    public function setSettlementReference($settlementReference)
    {
        $this->settlementReference = $settlementReference;

        return $this;
    }

    /**
     * Get settlementReference
     *
     * @return string 
     */
    public function getSettlementReference()
    {
        return $this->settlementReference;
    }

    /**
     * Set settledOn
     *
     * @param \DateTime $settledOn
     * @return RxSettlement
     */
    public function setSettledOn($settledOn)
    {
        $this->settledOn = $settledOn;

        return $this;
    }

    /**
     * Get settledOn
     *
     * @return \DateTime 
     */
    public function getSettledOn()
    {
        return $this->settledOn;
    }

    /**
     * Set amount
     *
     * @param string $amount
     * @return RxSettlement
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
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return RxSettlement
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
