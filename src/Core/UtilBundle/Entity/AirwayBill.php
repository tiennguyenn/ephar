<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AirwayBill
 *
 * @ORM\Table(name="airway_bill")
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\AirwayBillRepository")
 */
class AirwayBill
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
     * @ORM\Column(name="master_awb_code", type="string", length=20, nullable=true)
     */
    private $masterAwbCode;

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
     * @var string
     *
     * @ORM\Column(name="permit_number", type="string", length=20, nullable=true)
     */
    private $permitNumber;

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
     * Set verificationCode
     *
     * @param string $verificationCode
     * @return AirwayBill
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
     * Set masterAwbCode
     *
     * @param string $masterAwbCode
     * @return AirwayBill
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
     * Set status
     *
     * @param string $status
     * @return AirwayBill
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
     * @return AirwayBill
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
     * Set permitNumber
     *
     * @param string $permitNumber
     * @return AirwayBill
     */
    public function setPermitNumber($permitNumber)
    {
        $this->permitNumber = $permitNumber;

        return $this;
    }

    /**
     * Get permitNumber
     *
     * @return string 
     */
    public function getPermitNumber()
    {
        return $this->permitNumber;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return AirwayBill
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
     * @return AirwayBill
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
}
