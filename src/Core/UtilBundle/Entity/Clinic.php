<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Clinic
 *
 * @ORM\Table(name="clinic", indexes={@ORM\Index(name="doctor_id", columns={"doctor_id"}), @ORM\Index(name="business_address_id", columns={"business_address_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\ClinicRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Clinic
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
     * @ORM\Column(name="business_name", type="string", length=350, nullable=false)
     */
    private $businessName;

    /**
     * @var string
     *
     * @ORM\Column(name="trading_as_name", type="string", length=350, nullable=true)
     */
    private $tradingAsName;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=250, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="website_url", type="string", length=350, nullable=true)
     */
    private $websiteUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="business_logo_url", type="string", length=350, nullable=true)
     */
    private $businessLogoUrl;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_primary", type="boolean", nullable=true)
     */
    private $isPrimary;

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
     * @var \Doctor
     *
     * @ORM\ManyToOne(targetEntity="Doctor", inversedBy="clinics")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="doctor_id", referencedColumnName="id")
     * })
     */
    private $doctor;

    /**
     * @var \ClinicAddress
     *
     * @ORM\ManyToOne(targetEntity="ClinicAddress", cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="business_address_id", referencedColumnName="id")
     * })
     */
    private $businessAddress;



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
     * Set businessName
     *
     * @param string $businessName
     * @return Clinic
     */
    public function setBusinessName($businessName)
    {
        $this->businessName = $businessName;

        return $this;
    }

    /**
     * Get businessName
     *
     * @return string 
     */
    public function getBusinessName()
    {
        return $this->businessName;
    }

    /**
     * Set tradingAsName
     *
     * @param string $tradingAsName
     * @return Clinic
     */
    public function setTradingAsName($tradingAsName)
    {
        $this->tradingAsName = $tradingAsName;

        return $this;
    }

    /**
     * Get tradingAsName
     *
     * @return string 
     */
    public function getTradingAsName()
    {
        return $this->tradingAsName;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Clinic
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set websiteUrl
     *
     * @param string $websiteUrl
     * @return Clinic
     */
    public function setWebsiteUrl($websiteUrl)
    {
        $this->websiteUrl = $websiteUrl;

        return $this;
    }

    /**
     * Get websiteUrl
     *
     * @return string 
     */
    public function getWebsiteUrl()
    {
        return $this->websiteUrl;
    }

    /**
     * Set businessLogoUrl
     *
     * @param string $businessLogoUrl
     * @return Clinic
     */
    public function setBusinessLogoUrl($businessLogoUrl)
    {
        $this->businessLogoUrl = $businessLogoUrl;

        return $this;
    }

    /**
     * Get businessLogoUrl
     *
     * @return string 
     */
    public function getBusinessLogoUrl()
    {
        return $this->businessLogoUrl;
    }

    /**
     * Set isPrimary
     *
     * @param boolean $isPrimary
     * @return Clinic
     */
    public function setIsPrimary($isPrimary)
    {
        $this->isPrimary = $isPrimary;

        return $this;
    }

    /**
     * Get isPrimary
     *
     * @return boolean 
     */
    public function getIsPrimary()
    {
        return $this->isPrimary;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return Clinic
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
     * @return Clinic
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
     * @return Clinic
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
     * Set doctor
     *
     * @param \UtilBundle\Entity\Doctor $doctor
     * @return Clinic
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
     * Set businessAddress
     *
     * @param \UtilBundle\Entity\ClinicAddress $businessAddress
     * @return Clinic
     */
    public function setBusinessAddress(\UtilBundle\Entity\ClinicAddress $businessAddress = null)
    {
        $this->businessAddress = $businessAddress;

        return $this;
    }

    /**
     * Get businessAddress
     *
     * @return \UtilBundle\Entity\ClinicAddress 
     */
    public function getBusinessAddress()
    {
        return $this->businessAddress;
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
