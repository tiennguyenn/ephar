<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DoctorMedicalSpecialty
 *
 * @ORM\Table(name="doctor_medical_specialty", indexes={@ORM\Index(name="FK_doctor_medial_specialty_medical", columns={"medical_specialty_id"}), @ORM\Index(name="FK_doctor_medial_specialty", columns={"doctor_id"})})
 * @ORM\Entity
 */
class DoctorMedicalSpecialty
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
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

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
     * @var \MedicalSpecialty
     *
     * @ORM\ManyToOne(targetEntity="MedicalSpecialty")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="medical_specialty_id", referencedColumnName="id")
     * })
     */
    private $medicalSpecialty;



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
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return DoctorMedicalSpecialty
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
     * Set doctor
     *
     * @param \UtilBundle\Entity\Doctor $doctor
     * @return DoctorMedicalSpecialty
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
     * Set medicalSpecialty
     *
     * @param \UtilBundle\Entity\MedicalSpecialty $medicalSpecialty
     * @return DoctorMedicalSpecialty
     */
    public function setMedicalSpecialty(\UtilBundle\Entity\MedicalSpecialty $medicalSpecialty = null)
    {
        $this->medicalSpecialty = $medicalSpecialty;

        return $this;
    }

    /**
     * Get medicalSpecialty
     *
     * @return \UtilBundle\Entity\MedicalSpecialty 
     */
    public function getMedicalSpecialty()
    {
        return $this->medicalSpecialty;
    }
}
