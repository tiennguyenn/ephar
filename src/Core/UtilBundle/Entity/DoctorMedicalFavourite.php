<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DoctorMedicalFavourite
 *
 * @ORM\Table(name="doctor_medical_favourite", indexes={@ORM\Index(name="FK_doctor_medical_favourite", columns={"doctor_id"}), @ORM\Index(name="FK_doctor_medical_favourite_drug", columns={"drug_id"})})
 * @ORM\Entity
 */
class DoctorMedicalFavourite
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
     * @var \Drug
     *
     * @ORM\ManyToOne(targetEntity="Drug")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="drug_id", referencedColumnName="id")
     * })
     */
    private $drug;



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
     * @return DoctorMedicalFavourite
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
     * @return DoctorMedicalFavourite
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
     * Set drug
     *
     * @param \UtilBundle\Entity\Drug $drug
     * @return DoctorMedicalFavourite
     */
    public function setDrug(\UtilBundle\Entity\Drug $drug = null)
    {
        $this->drug = $drug;

        return $this;
    }

    /**
     * Get drug
     *
     * @return \UtilBundle\Entity\Drug 
     */
    public function getDrug()
    {
        return $this->drug;
    }
}
