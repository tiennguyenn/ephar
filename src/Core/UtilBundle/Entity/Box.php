<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Box
 *
 * @ORM\Table(name="box", indexes={@ORM\Index(name="box_type_id", columns={"box_type_id"}), @ORM\Index(name="FK_box_rx", columns={"rx_id"}), @ORM\Index(name="FK_box_bag", columns={"bag_id"}), @ORM\Index(name="FK_box_country", columns={"destination"})})
 * @ORM\Entity
 */
class Box
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
     * @ORM\Column(name="verification_code", type="string", length=5, nullable=true)
     */
    private $verificationCode;

    /**
     * @var string
     *
     * @ORM\Column(name="tracking_code", type="string", length=50, nullable=true)
     */
    private $trackingCode;

    /**
     * @var string
     *
     * @ORM\Column(name="shipper_code", type="string", length=10, nullable=true)
     */
    private $shipperCode;

    /**
     * @var string
     *
     * @ORM\Column(name="box_position", type="string", length=50, nullable=false)
     */
    private $boxPosition;

    /**
     * @var integer
     *
     * @ORM\Column(name="box_total", type="integer", nullable=false)
     */
    private $boxTotal;

    /**
     * @var string
     *
     * @ORM\Column(name="box_size", type="string", length=10, nullable=true)
     */
    private $boxSize;

    /**
     * @var string
     *
     * @ORM\Column(name="tracking_number", type="string", length=50, nullable=false)
     */
    private $trackingNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="master_awb_code", type="string", length=50, nullable=true)
     */
    private $masterAwbCode;

    /**
     * @var string
     *
     * @ORM\Column(name="delivery_person", type="string", length=100, nullable=true)
     */
    private $deliveryPerson;

    /**
     * @var string
     *
     * @ORM\Column(name="notes", type="string", length=250, nullable=true)
     */
    private $notes;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="processing_status", type="string", length=20, nullable=true)
     */
    private $processingStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="processing_notes", type="string", length=250, nullable=true)
     */
    private $processingNotes;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=false)
     */
    private $createdOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_status_on", type="datetime", nullable=false)
     */
    private $updatedStatusOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_on", type="datetime", nullable=false)
     */
    private $updatedOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted_on", type="datetime", nullable=true)
     */
    private $deletedOn;

    /**
     * @var \BoxType
     *
     * @ORM\ManyToOne(targetEntity="BoxType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="box_type_id", referencedColumnName="id")
     * })
     */
    private $boxType;

    /**
     * @var \Bag
     *
     * @ORM\ManyToOne(targetEntity="Bag")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="bag_id", referencedColumnName="id")
     * })
     */
    private $bag;

    /**
     * @var \Country
     *
     * @ORM\ManyToOne(targetEntity="Country")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="destination", referencedColumnName="id")
     * })
     */
    private $destination;

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
     * Set verificationCode
     *
     * @param string $verificationCode
     * @return Box
     */
    public function setVerificationCode($verificationCode)
    {
        $this->verificationCode = $verificationCode;

        return $this;
    }

    /**
     * Get verificationCode
     *
     * @return string 
     */
    public function getVerificationCode()
    {
        return $this->verificationCode;
    }

    /**
     * Set trackingCode
     *
     * @param string $trackingCode
     * @return Box
     */
    public function setTrackingCode($trackingCode)
    {
        $this->trackingCode = $trackingCode;

        return $this;
    }

    /**
     * Get trackingCode
     *
     * @return string 
     */
    public function getTrackingCode()
    {
        return $this->trackingCode;
    }

    /**
     * Set masterAwbCode
     *
     * @param string $masterAwbCode
     * @return Box
     */
    public function setMasterAwbCode($masterAwbCode)
    {
        $this->masterAwbCode = $masterAwbCode;

        return $this;
    }

    /**
     * Get masterAwbCode
     *
     * @return string
     */
    public function getMasterAwbCode()
    {
        return $this->masterAwbCode;
    }

    /**
     * Set shipperCode
     *
     * @param string $shipperCode
     * @return Box
     */
    public function setShipperCode($shipperCode)
    {
        $this->shipperCode = $shipperCode;

        return $this;
    }

    /**
     * Get shipperCode
     *
     * @return string 
     */
    public function getShipperCode()
    {
        return $this->shipperCode;
    }

    /**
     * Set boxPosition
     *
     * @param string $boxPosition
     * @return Box
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
     * @return Box
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
     * @param string $boxSize
     * @return Box
     */
    public function setBoxSize($boxSize)
    {
        $this->boxSize = $boxSize;

        return $this;
    }

    /**
     * Get boxSize
     *
     * @return string 
     */
    public function getBoxSize()
    {
        return $this->boxSize;
    }

    /**
     * Set trackingNumber
     *
     * @param string $trackingNumber
     * @return Box
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
     * Set deliveryPerson
     *
     * @param string $deliveryPerson
     * @return Box
     */
    public function setDeliveryPerson($deliveryPerson)
    {
        $this->deliveryPerson = $deliveryPerson;

        return $this;
    }

    /**
     * Get deliveryPerson
     *
     * @return string 
     */
    public function getDeliveryPerson()
    {
        return $this->deliveryPerson;
    }

    /**
     * Set notes
     *
     * @param string $notes
     * @return Box
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
     * Set status
     *
     * @param boolean $status
     * @return Box
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set processingStatus
     *
     * @param string $processingStatus
     * @return Box
     */
    public function setProcessingStatus($processingStatus)
    {
        $this->processingStatus = $processingStatus;

        return $this;
    }

    /**
     * Get processingStatus
     *
     * @return string 
     */
    public function getProcessingStatus()
    {
        return $this->processingStatus;
    }

    /**
     * Set processingNotes
     *
     * @param string $processingNotes
     * @return Box
     */
    public function setProcessingNotes($processingNotes)
    {
        $this->processingNotes = $processingNotes;

        return $this;
    }

    /**
     * Get processingNotes
     *
     * @return string 
     */
    public function getProcessingNotes()
    {
        return $this->processingNotes;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return Box
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
     * @return Box
     */
    public function setUpdatedOn($updatedOn)
    {
        $this->updatedOn = $updatedOn;

        return $this;
    }

    /**
     * Set updatedStatusOn
     *
     * @param \DateTime $updatedStatusOn
     * @return Box
     */
    public function setUpdatedStatusOn($updatedStatusOn)
    {
        $this->updatedStatusOn = $updatedStatusOn;

        return $this;
    }

    /**
     * Get updatedStatusOn
     *
     * @return \DateTime
     */
    public function getUpdatedStatusOn()
    {
        return $this->updatedStatusOn;
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
     * Set deletedOn
     *
     * @param \DateTime $deletedOn
     * @return Box
     */
    public function setDeletedOn($deletedOn)
    {
        $this->deletedOn = $deletedOn;

        return $this;
    }

    /**
     * Get deletedOn
     *
     * @return \DateTime 
     */
    public function getDeletedOn()
    {
        return $this->deletedOn;
    }

    /**
     * Set boxType
     *
     * @param \UtilBundle\Entity\BoxType $boxType
     * @return Box
     */
    public function setBoxType(\UtilBundle\Entity\BoxType $boxType = null)
    {
        $this->boxType = $boxType;

        return $this;
    }

    /**
     * Get boxType
     *
     * @return \UtilBundle\Entity\BoxType 
     */
    public function getBoxType()
    {
        return $this->boxType;
    }

    /**
     * Set bag
     *
     * @param \UtilBundle\Entity\Bag $bag
     * @return Box
     */
    public function setBag(\UtilBundle\Entity\Bag $bag = null)
    {
        $this->bag = $bag;

        return $this;
    }

    /**
     * Get bag
     *
     * @return \UtilBundle\Entity\Bag 
     */
    public function getBag()
    {
        return $this->bag;
    }

    /**
     * Set destination
     *
     * @param \UtilBundle\Entity\Country $destination
     * @return Box
     */
    public function setDestination(\UtilBundle\Entity\Country $destination = null)
    {
        $this->destination = $destination;

        return $this;
    }

    /**
     * Get destination
     *
     * @return \UtilBundle\Entity\Country 
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * Set rx
     *
     * @param \UtilBundle\Entity\Rx $rx
     * @return Box
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
