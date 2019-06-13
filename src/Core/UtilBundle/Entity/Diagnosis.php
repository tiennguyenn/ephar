<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Diagnosis
 *
 * @ORM\Table(name="diagnosis")
 * @ORM\Entity
 */
class Diagnosis
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
     * @ORM\Column(name="diagnosis", type="string", length=255, nullable=false)
     */
    private $diagnosis;

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
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Patient", mappedBy="diagnosis")
     */
    private $patient;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->patient = new \Doctrine\Common\Collections\ArrayCollection();
    }


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
     * Set diagnosis
     *
     * @param string $diagnosis
     * @return Diagnosis
     */
    public function setDiagnosis($diagnosis)
    {
        $this->diagnosis = $diagnosis;

        return $this;
    }

    /**
     * Get diagnosis
     *
     * @return string 
     */
    public function getDiagnosis()
    {
        return $this->diagnosis;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return Diagnosis
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
     * @return Diagnosis
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
     * Add patient
     *
     * @param \UtilBundle\Entity\Patient $patient
     * @return Diagnosis
     */
    public function addPatient(\UtilBundle\Entity\Patient $patient)
    {
        $this->patient[] = $patient;

        return $this;
    }

    /**
     * Remove patient
     *
     * @param \UtilBundle\Entity\Patient $patient
     */
    public function removePatient(\UtilBundle\Entity\Patient $patient)
    {
        $this->patient->removeElement($patient);
    }

    /**
     * Get patient
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPatient()
    {
        return $this->patient;
    }
}
