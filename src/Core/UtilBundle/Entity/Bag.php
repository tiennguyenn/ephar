<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Bag
 *
 * @ORM\Table(name="bag", indexes={@ORM\Index(name="FK_bag_airway_bill", columns={"airwaybill_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\BagRepository")
 */
class Bag
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
     * @ORM\Column(name="bag_code", type="string", length=20, nullable=true)
     */
    private $bagCode;

    /**
     * @var integer
     *
     * @ORM\Column(name="destination", type="integer", nullable=true)
     */
    private $destination;

    /**
     * @var float
     *
     * @ORM\Column(name="total_package", type="float", precision=10, scale=0, nullable=true)
     */
    private $totalPackage;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=20, nullable=true)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="notes", type="string", length=250, nullable=true)
     */
    private $notes;

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
     * @var \AirwayBill
     *
     * @ORM\ManyToOne(targetEntity="AirwayBill")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="airwaybill_id", referencedColumnName="id")
     * })
     */
    private $airwaybill;



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
     * Set bagCode
     *
     * @param string $bagCode
     * @return Bag
     */
    public function setBagCode($bagCode)
    {
        $this->bagCode = $bagCode;

        return $this;
    }

    /**
     * Get bagCode
     *
     * @return string 
     */
    public function getBagCode()
    {
        return $this->bagCode;
    }

    /**
     * Set destination
     *
     * @param integer $destination
     * @return Bag
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;

        return $this;
    }

    /**
     * Get destination
     *
     * @return integer 
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * Set totalPackage
     *
     * @param float $totalPackage
     * @return Bag
     */
    public function setTotalPackage($totalPackage)
    {
        $this->totalPackage = $totalPackage;

        return $this;
    }

    /**
     * Get totalPackage
     *
     * @return float 
     */
    public function getTotalPackage()
    {
        return $this->totalPackage;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Bag
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set notes
     *
     * @param string $notes
     * @return Bag
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
     * @return Bag
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
     * @return Bag
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
     * Set airwaybill
     *
     * @param \UtilBundle\Entity\AirwayBill $airwaybill
     * @return Bag
     */
    public function setAirwaybill(\UtilBundle\Entity\AirwayBill $airwaybill = null)
    {
        $this->airwaybill = $airwaybill;

        return $this;
    }

    /**
     * Get airwaybill
     *
     * @return \UtilBundle\Entity\AirwayBill 
     */
    public function getAirwaybill()
    {
        return $this->airwaybill;
    }
}
