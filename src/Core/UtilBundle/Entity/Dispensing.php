<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Dispensing
 *
 * @ORM\Table(name="dispensing", indexes={@ORM\Index(name="FK_dispensing_rx", columns={"rx_id"})})
 * @ORM\Entity
 */
class Dispensing
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
     * @ORM\Column(name="box_type_id", type="integer", nullable=true)
     */
    private $boxTypeId;

    /**
     * @var string
     *
     * @ORM\Column(name="box_position", type="string", length=20, nullable=true)
     */
    private $boxPosition;

    /**
     * @var integer
     *
     * @ORM\Column(name="box_total", type="integer", nullable=true)
     */
    private $boxTotal;

    /**
     * @var integer
     *
     * @ORM\Column(name="box_size", type="integer", nullable=true)
     */
    private $boxSize;

    /**
     * @var string
     *
     * @ORM\Column(name="tracking_number", type="string", length=20, nullable=true)
     */
    private $trackingNumber;

    /**
     * @var float
     *
     * @ORM\Column(name="weight", type="float", precision=10, scale=0, nullable=true)
     */
    private $weight;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var \Rx
     *
     * @ORM\ManyToOne(targetEntity="Rx")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="rx_id", referencedColumnName="id")
     * })
     */
    private $rx;



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
     * Set boxTypeId
     *
     * @param integer $boxTypeId
     *
     * @return Dispensing
     */
    public function setBoxTypeId($boxTypeId)
    {
        $this->boxTypeId = $boxTypeId;

        return $this;
    }

    /**
     * Get boxTypeId
     *
     * @return integer
     */
    public function getBoxTypeId()
    {
        return $this->boxTypeId;
    }

    /**
     * Set boxPosition
     *
     * @param string $boxPosition
     *
     * @return Dispensing
     */
    public function setBoxPosition($boxPosition)
    {
        $this->boxPosition = $boxPosition;

        return $this;
    }

    /**
     * Get boxPosition
     *
     * @return string
     */
    public function getBoxPosition()
    {
        return $this->boxPosition;
    }

    /**
     * Set boxTotal
     *
     * @param integer $boxTotal
     *
     * @return Dispensing
     */
    public function setBoxTotal($boxTotal)
    {
        $this->boxTotal = $boxTotal;

        return $this;
    }

    /**
     * Get boxTotal
     *
     * @return integer
     */
    public function getBoxTotal()
    {
        return $this->boxTotal;
    }

    /**
     * Set boxSize
     *
     * @param integer $boxSize
     *
     * @return Dispensing
     */
    public function setBoxSize($boxSize)
    {
        $this->boxSize = $boxSize;

        return $this;
    }

    /**
     * Get boxSize
     *
     * @return integer
     */
    public function getBoxSize()
    {
        return $this->boxSize;
    }

    /**
     * Set trackingNumber
     *
     * @param string $trackingNumber
     *
     * @return Dispensing
     */
    public function setTrackingNumber($trackingNumber)
    {
        $this->trackingNumber = $trackingNumber;

        return $this;
    }

    /**
     * Get trackingNumber
     *
     * @return string
     */
    public function getTrackingNumber()
    {
        return $this->trackingNumber;
    }

    /**
     * Set weight
     *
     * @param float $weight
     *
     * @return Dispensing
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight
     *
     * @return float
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return Dispensing
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
     * Set rx
     *
     * @param \UtilBundle\Entity\Rx $rx
     *
     * @return Dispensing
     */
    public function setRx(\UtilBundle\Entity\Rx $rx = null)
    {
        $this->rx = $rx;

        return $this;
    }

    /**
     * Get rx
     *
     * @return \UtilBundle\Entity\Rx
     */
    public function getRx()
    {
        return $this->rx;
    }
}
