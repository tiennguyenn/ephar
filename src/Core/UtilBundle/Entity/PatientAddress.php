<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PatientAddress
 *
 * @ORM\Table(name="patient_address", indexes={@ORM\Index(name="address_id", columns={"address_id"}), @ORM\Index(name="patient_id", columns={"patient_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\PatientAddressRepository")
 */
class PatientAddress
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
     * @ORM\Column(name="nickname", type="string", length=50, nullable=false)
     */
    private $nickname;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_primary", type="boolean", nullable=false)
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
     * @var \Address
     *
     * @ORM\ManyToOne(targetEntity="Address", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="address_id", referencedColumnName="id")
     * })
     */
    private $address;



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
     * Set nickname
     *
     * @param string $nickname
     * @return PatientAddress
     */
    public function setNickname($nickname)
    {
        $this->nickname = $nickname;

        return $this;
    }

    /**
     * Get nickname
     *
     * @return string 
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * Set isPrimary
     *
     * @param boolean $isPrimary
     * @return PatientAddress
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
     * @return PatientAddress
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
     * @return PatientAddress
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
     * @return PatientAddress
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
     * Set address
     *
     * @param \UtilBundle\Entity\Address $address
     * @return PatientAddress
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
}
