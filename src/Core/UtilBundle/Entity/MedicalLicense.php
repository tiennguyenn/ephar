<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MedicalLicense
 *
 * @ORM\Table(name="medical_license", indexes={@ORM\Index(name="dladlasdf_idx", columns={"medical_registration_type_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\MedicalLicenseRepository")
 * @ORM\HasLifecycleCallbacks
 */
class MedicalLicense
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
     * @ORM\Column(name="registration_number", type="string", length=255, nullable=false)
     */
    private $registrationNumber;

    /**
     * @var integer
     *
     * @ORM\Column(name="issuing_country_id", type="integer", nullable=true)
     */
    private $issuingCountryId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="issuing_date", type="date", nullable=true)
     */
    private $issuingDate;

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
     * @var \DateTime
     *
     * @ORM\Column(name="deleted_on", type="datetime", nullable=true)
     */
    private $deletedOn;

    /**
     * @var \MedicalRegistrationType
     *
     * @ORM\ManyToOne(targetEntity="MedicalRegistrationType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="medical_registration_type_id", referencedColumnName="id")
     * })
     */
    private $medicalRegistrationType;



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
     * Set registrationNumber
     *
     * @param string $registrationNumber
     * @return MedicalLicense
     */
    public function setRegistrationNumber($registrationNumber)
    {
        $this->registrationNumber = $registrationNumber;

        return $this;
    }

    /**
     * Get registrationNumber
     *
     * @return string 
     */
    public function getRegistrationNumber()
    {
        return $this->registrationNumber;
    }

    /**
     * Set issuingCountryId
     *
     * @param integer $issuingCountryId
     * @return MedicalLicense
     */
    public function setIssuingCountryId($issuingCountryId)
    {
        $this->issuingCountryId = $issuingCountryId;

        return $this;
    }

    /**
     * Get issuingCountryId
     *
     * @return integer 
     */
    public function getIssuingCountryId()
    {
        return $this->issuingCountryId;
    }

    /**
     * Set issuingDate
     *
     * @param \DateTime $issuingDate
     * @return MedicalLicense
     */
    public function setIssuingDate($issuingDate)
    {
        $this->issuingDate = $issuingDate;

        return $this;
    }

    /**
     * Get issuingDate
     *
     * @return \DateTime 
     */
    public function getIssuingDate()
    {
        return $this->issuingDate;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return MedicalLicense
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
     * @return MedicalLicense
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
     * @return MedicalLicense
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
     * Set medicalRegistrationType
     *
     * @param \UtilBundle\Entity\MedicalRegistrationType $medicalRegistrationType
     * @return MedicalLicense
     */
    public function setMedicalRegistrationType(\UtilBundle\Entity\MedicalRegistrationType $medicalRegistrationType = null)
    {
        $this->medicalRegistrationType = $medicalRegistrationType;

        return $this;
    }

    /**
     * Get medicalRegistrationType
     *
     * @return \UtilBundle\Entity\MedicalRegistrationType 
     */
    public function getMedicalRegistrationType()
    {
        return $this->medicalRegistrationType;
    }
     /**
     * Gets triggered only on insert

     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdOn = new \DateTime("now");
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
