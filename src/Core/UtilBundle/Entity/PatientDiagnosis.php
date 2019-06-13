<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PatientDiagnosis
 *
 * @ORM\Table(name="patient_diagnosis", indexes={@ORM\Index(name="patient_id", columns={"patient_id"}), @ORM\Index(name="diagnosis_id", columns={"diagnosis_id"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class PatientDiagnosis
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
     * @ORM\Column(name="options", type="text", nullable=true)
     */
    private $options;

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
     * @ORM\ManyToOne(targetEntity="Patient")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="patient_id", referencedColumnName="id")
     * })
     */
    private $patient;

    /**
     * @var \Diagnosis
     *
     * @ORM\ManyToOne(targetEntity="Diagnosis")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="diagnosis_id", referencedColumnName="id")
     * })
     */
    private $diagnosis;

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
     *
     * @return PatientDiagnosis
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
     *
     * @return PatientDiagnosis
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
     *
     * @return PatientDiagnosis
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
     * Set diagnosis
     *
     * @param \UtilBundle\Entity\Diagnosis $diagnosis
     *
     * @return PatientDiagnosis
     */
    public function setDiagnosis(\UtilBundle\Entity\Diagnosis $diagnosis = null)
    {
        $this->diagnosis = $diagnosis;

        return $this;
    }

    /**
     * Get diagnosis
     *
     * @return \UtilBundle\Entity\Diagnosis
     */
    public function getDiagnosis()
    {
        return $this->diagnosis;
    }

    /**
     * Gets triggered only on insert

     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdOn = new \DateTime();
    }

    /**
     * Set optons
     *
     * @param string $options
     * @return PatientDiagnosis
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Get options
     *
     * @return string
     */
    public function getOptions()
    {
        return $this->options;
    }
}
