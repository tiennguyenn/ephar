<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PatientPhone
 *
 * @ORM\Table(name="patient_phone", indexes={@ORM\Index(name="FK_patient_phone", columns={"patient_id"}), @ORM\Index(name="FK_patient_phone_phone", columns={"phone_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\PatientPhoneRepository")
 * @ORM\HasLifecycleCallbacks
 */
class PatientPhone
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
     * @var \DateTime
     *
     * @ORM\Column(name="deleted_on", type="datetime", nullable=true)
     */
    private $deletedOn;

    /**
     * @var \Phone
     *
     * @ORM\ManyToOne(targetEntity="Phone")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="phone_id", referencedColumnName="id")
     * })
     */
    private $phone;

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
     * @return PatientPhone
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
     * Set deletedOn
     *
     * @param \DateTime $deletedOn
     * @return PatientPhone
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
     * Set phone
     *
     * @param \UtilBundle\Entity\Phone $phone
     * @return PatientPhone
     */
    public function setPhone(\UtilBundle\Entity\Phone $phone = null)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return \UtilBundle\Entity\Phone 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set patient
     *
     * @param \UtilBundle\Entity\Patient $patient
     * @return PatientPhone
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
    public function prePersist()
    {
        $this->createdOn = new \DateTime();
    }
}
