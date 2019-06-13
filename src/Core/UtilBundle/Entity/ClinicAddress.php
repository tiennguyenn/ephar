<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ClinicAddress
 *
 * @ORM\Table(name="clinic_address", indexes={@ORM\Index(name="doctor_id", columns={"doctor_id"}), @ORM\Index(name="address_id", columns={"address_id"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class ClinicAddress
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
     * @ORM\Column(name="business_phone_id", type="integer", nullable=false)
     */
    private $businessPhoneId;

    /**
     * @var integer
     *
     * @ORM\Column(name="emergency_phone_id", type="integer", nullable=true)
     */
    private $emergencyPhoneId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=false)
     */
    private $createdOn;

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
     * @var \Address
     *
     * @ORM\ManyToOne(targetEntity="Address", cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="address_id", referencedColumnName="id")
     * })
     */
    private $address;

    /**
     * @var \Doctor
     *
     * @ORM\ManyToOne(targetEntity="Doctor")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="doctor_id", referencedColumnName="id")
     * })
     */
    private $doctor;
    
    /**
     * @var \Phone
     *
     * @ORM\ManyToOne(targetEntity="Phone", cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="business_phone_id", referencedColumnName="id")
     * })
     */
    private $businessPhone;


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
     * Set businessPhoneId
     *
     * @param integer $businessPhoneId
     * @return ClinicAddress
     */
    public function setBusinessPhoneId($businessPhoneId)
    {
        $this->businessPhoneId = $businessPhoneId;

        return $this;
    }

    /**
     * Get businessPhoneId
     *
     * @return integer 
     */
    public function getBusinessPhoneId()
    {
        return $this->businessPhoneId;
    }

    /**
     * Set emergencyPhoneId
     *
     * @param integer $emergencyPhoneId
     * @return ClinicAddress
     */
    public function setEmergencyPhoneId($emergencyPhoneId)
    {
        $this->emergencyPhoneId = $emergencyPhoneId;

        return $this;
    }

    /**
     * Get emergencyPhoneId
     *
     * @return integer 
     */
    public function getEmergencyPhoneId()
    {
        return $this->emergencyPhoneId;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return ClinicAddress
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
     * @return ClinicAddress
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
     * Set deletedOn
     *
     * @param \DateTime $deletedOn
     * @return ClinicAddress
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
     * Set address
     *
     * @param \UtilBundle\Entity\Address $address
     * @return ClinicAddress
     */
    public function setAddress(\UtilBundle\Entity\Address $address = null)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return \UtilBundle\Entity\Address 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set doctor
     *
     * @param \UtilBundle\Entity\Doctor $doctor
     * @return ClinicAddress
     */
    public function setDoctor(\UtilBundle\Entity\Doctor $doctor = null)
    {
        $this->doctor = $doctor;

        return $this;
    }

    /**
     * Get doctor
     *
     * @return \UtilBundle\Entity\Doctor 
     */
    public function getDoctor()
    {
        return $this->doctor;
    }

    /**
     * Set businessPhone
     *
     * @param \UtilBundle\Entity\Phone $businessPhone
     * @return ClinicAddress
     */
    public function setBusinessPhone(\UtilBundle\Entity\Phone $businessPhone = null)
    {
        $this->businessPhone = $businessPhone;

        return $this;
    }

    /**
     * Get businessPhone
     *
     * @return \UtilBundle\Entity\Phone 
     */
    public function getBusinessPhone()
    {
        return $this->businessPhone;
    }
     /**
     * Gets triggered only on insert

     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdOn = new \DateTime("now");
        $this->updatedOn = new \DateTime("now");
    }

    /**
     * Gets triggered every time on update

     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->updatedOn = new \DateTime("now");
    }
}
