<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DispensingLabel
 *
 * @ORM\Table(name="dispensing_label", indexes={@ORM\Index(name="FK_dispensing_label", columns={"rx_line_id"}), @ORM\Index(name="FK_dispensing_label_1", columns={"dispensing_id"})})
 * @ORM\Entity
 */
class DispensingLabel
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
     * @ORM\Column(name="date_expired", type="datetime", nullable=true)
     */
    private $dateExpired;

    /**
     * @var boolean
     *
     * @ORM\Column(name="type", type="boolean", nullable=true)
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer", nullable=true)
     */
    private $quantity;

    /**
     * @var string
     *
     * @ORM\Column(name="batch_number", type="string", length=20, nullable=true)
     */
    private $batchNumber;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var \Dispensing
     *
     * @ORM\ManyToOne(targetEntity="Dispensing")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="dispensing_id", referencedColumnName="id")
     * })
     */
    private $dispensing;

    /**
     * @var \RxLine
     *
     * @ORM\ManyToOne(targetEntity="RxLine")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="rx_line_id", referencedColumnName="id")
     * })
     */
    private $rxLine;



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
     * Set dateExpired
     *
     * @param \DateTime $dateExpired
     *
     * @return DispensingLabel
     */
    public function setDateExpired($dateExpired)
    {
        $this->dateExpired = $dateExpired;

        return $this;
    }

    /**
     * Get dateExpired
     *
     * @return \DateTime
     */
    public function getDateExpired()
    {
        return $this->dateExpired;
    }

    /**
     * Set type
     *
     * @param boolean $type
     *
     * @return DispensingLabel
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return boolean
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return DispensingLabel
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set batchNumber
     *
     * @param string $batchNumber
     *
     * @return DispensingLabel
     */
    public function setBatchNumber($batchNumber)
    {
        $this->batchNumber = $batchNumber;

        return $this;
    }

    /**
     * Get batchNumber
     *
     * @return string
     */
    public function getBatchNumber()
    {
        return $this->batchNumber;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return DispensingLabel
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
     * Set dispensing
     *
     * @param \UtilBundle\Entity\Dispensing $dispensing
     *
     * @return DispensingLabel
     */
    public function setDispensing(\UtilBundle\Entity\Dispensing $dispensing = null)
    {
        $this->dispensing = $dispensing;

        return $this;
    }

    /**
     * Get dispensing
     *
     * @return \UtilBundle\Entity\Dispensing
     */
    public function getDispensing()
    {
        return $this->dispensing;
    }

    /**
     * Set rxLine
     *
     * @param \UtilBundle\Entity\RxLine $rxLine
     *
     * @return DispensingLabel
     */
    public function setRxLine(\UtilBundle\Entity\RxLine $rxLine = null)
    {
        $this->rxLine = $rxLine;

        return $this;
    }

    /**
     * Get rxLine
     *
     * @return \UtilBundle\Entity\RxLine
     */
    public function getRxLine()
    {
        return $this->rxLine;
    }
}
