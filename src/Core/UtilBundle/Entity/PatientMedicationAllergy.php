<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PatientMedicationAllergy
 *
 * @ORM\Table(name="patient_medication_allergy", indexes={@ORM\Index(name="FK_patient_medication_allergy_1", columns={"medication_allergy"}), @ORM\Index(name="FK_patient_medication_allergy", columns={"patient_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\PatientMedicationAllergyRepository")
 * @ORM\HasLifecycleCallbacks
 */
class PatientMedicationAllergy
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
     * @ORM\Column(name="medication_allergy", type="string", length=255, nullable=false)
     */
    private $medicationAllergy;

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
     * @var \Patient
     *
     * @ORM\ManyToOne(targetEntity="Patient", inversedBy="allergies")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="patient_id", referencedColumnName="id")
     * })
     */
    private $patient;



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
     * Set medicationAllergy
     *
     * @param string $medicationAllergy
     * @return PatientMedicationAllergy
     */
    public function setMedicationAllergy($medicationAllergy)
    {
        $this->medicationAllergy = $medicationAllergy;

        return $this;
    }

    /**
     * Get medicationAllergy
     *
     * @return string 
     */
    public function getMedicationAllergy()
    {
        return $this->medicationAllergy;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return PatientMedicationAllergy
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
     * @return PatientMedicationAllergy
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
     * Set patient
     *
     * @param \UtilBundle\Entity\Patient $patient
     * @return PatientMedicationAllergy
     */
    public function setPatient(\UtilBundle\Entity\Patient $patient = null)
    {
        $this->patient = $patient;

        return $this;
    }

    /**
     * Get patient
     *
     * @return \UtilBundle\Entity\Patient 
     */
    public function getPatient()
    {
        return $this->patient;
    }

    /**
     * Gets triggered only on insert

     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdOn = new \DateTime();
    }
}
