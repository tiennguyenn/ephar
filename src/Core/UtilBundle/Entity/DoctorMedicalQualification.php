<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DoctorMedicalQualification
 *
 * @ORM\Table(name="doctor_medical_qualification", indexes={@ORM\Index(name="FK_doctor_medical_qualification", columns={"medical_qualification_id"}), @ORM\Index(name="FK_doctor_medical_qualification_1", columns={"doctor_id"})})
 * @ORM\Entity
 */
class DoctorMedicalQualification
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
     * @ORM\Column(name="country_id", type="integer", nullable=false)
     */
    private $countryId;

    /**
     * @var string
     *
     * @ORM\Column(name="institution_name", type="string", length=250, nullable=false)
     */
    private $institutionName;

    /**
     * @var integer
     *
     * @ORM\Column(name="year_of_graduation", type="integer", nullable=false)
     */
    private $yearOfGraduation;

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
     * @var \Doctor
     *
     * @ORM\ManyToOne(targetEntity="Doctor")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="doctor_id", referencedColumnName="id")
     * })
     */
    private $doctor;

    /**
     * @var \MedicalQualification
     *
     * @ORM\ManyToOne(targetEntity="MedicalQualification")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="medical_qualification_id", referencedColumnName="id")
     * })
     */
    private $medicalQualification;



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
     * Set countryId
     *
     * @param integer $countryId
     * @return DoctorMedicalQualification
     */
    public function setCountryId($countryId)
    {
        $this->countryId = $countryId;

        return $this;
    }

    /**
     * Get countryId
     *
     * @return integer 
     */
    public function getCountryId()
    {
        return $this->countryId;
    }

    /**
     * Set institutionName
     *
     * @param string $institutionName
     * @return DoctorMedicalQualification
     */
    public function setInstitutionName($institutionName)
    {
        $this->institutionName = $institutionName;

        return $this;
    }

    /**
     * Get institutionName
     *
     * @return string 
     */
    public function getInstitutionName()
    {
        return $this->institutionName;
    }

    /**
     * Set yearOfGraduation
     *
     * @param integer $yearOfGraduation
     * @return DoctorMedicalQualification
     */
    public function setYearOfGraduation($yearOfGraduation)
    {
        $this->yearOfGraduation = $yearOfGraduation;

        return $this;
    }

    /**
     * Get yearOfGraduation
     *
     * @return integer 
     */
    public function getYearOfGraduation()
    {
        return $this->yearOfGraduation;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return DoctorMedicalQualification
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
     * @return DoctorMedicalQualification
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
     * Set doctor
     *
     * @param \UtilBundle\Entity\Doctor $doctor
     * @return DoctorMedicalQualification
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
     * Set medicalQualification
     *
     * @param \UtilBundle\Entity\MedicalQualification $medicalQualification
     * @return DoctorMedicalQualification
     */
    public function setMedicalQualification(\UtilBundle\Entity\MedicalQualification $medicalQualification = null)
    {
        $this->medicalQualification = $medicalQualification;

        return $this;
    }

    /**
     * Get medicalQualification
     *
     * @return \UtilBundle\Entity\MedicalQualification 
     */
    public function getMedicalQualification()
    {
        return $this->medicalQualification;
    }
}
