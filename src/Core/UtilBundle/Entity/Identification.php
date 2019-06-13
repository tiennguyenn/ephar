<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Identification
 *
 * @ORM\Table(name="identification")
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\IdentificationRepository")
 */
class Identification
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
     * @ORM\Column(name="identification_type_id", type="integer", nullable=true)
     */
    private $identificationTypeId;

    /**
     * @var integer
     *
     * @ORM\Column(name="identity_number", type="string", length=100, nullable=true)
     */
    private $identityNumber;
    
    /**
     * @var \integer
     *
     * @ORM\Column(name="issuing_country_id", type="integer",nullable=true)
     */
    private $issuingCountryId;
    
    /**
     * @var string
     *
     * @ORM\Column(name="issue_date", type="string", length=10, nullable=true)
     */
    private $issueDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expiry_date", type="date", nullable=true)
     */
    private $expiryDate;

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
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Doctor", mappedBy="identification")
     */
    private $doctor;
     /**
     * @ORM\ManyToMany(targetEntity="Agent", mappedBy="identifications",cascade={"persist", "remove" })
     * @ORM\JoinTable(name="agent_identification")
    */
    private $agents;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->doctor = new \Doctrine\Common\Collections\ArrayCollection();
        $this->agents = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set identificationTypeId
     *
     * @param integer $identificationTypeId
     * @return Identification
     */
    public function setIdentificationTypeId($identificationTypeId)
    {
        $this->identificationTypeId = $identificationTypeId;

        return $this;
    }

    /**
     * Get identificationTypeId
     *
     * @return integer 
     */
    public function getIdentificationTypeId()
    {
        return $this->identificationTypeId;
    }

    /**
     * Set identityNumber
     *
     * @param integer $identityNumber
     * @return Identification
     */
    public function setIdentityNumber($identityNumber)
    {
        $this->identityNumber = $identityNumber;

        return $this;
    }

    /**
     * Get identityNumber
     *
     * @return integer 
     */
    public function getIdentityNumber()
    {
        return $this->identityNumber;
    }

    /**
     * Set issuingCountryId
     *
     * @param string $issuingCountryId
     * @return Identification
     */
    public function setIssuingCountryId($issuingCountryId)
    {
        $this->issuingCountryId = $issuingCountryId;

        return $this;
    }

    /**
     * Get issuingCountryId
     *
     * @return string 
     */
    public function getIssuingCountryId()
    {
        return $this->issuingCountryId;
    }

    /**
     * Set issueDate
     *
     * @param string $issueDate
     * @return Identification
     */
    public function setIssueDate($issueDate)
    {
        $this->issueDate = $issueDate;

        return $this;
    }

    /**
     * Get issueDate
     *
     * @return string 
     */
    public function getIssueDate()
    {
        return $this->issueDate;
    }

    /**
     * Set expiryDate
     *
     * @param \DateTime $expiryDate
     * @return Identification
     */
    public function setExpiryDate($expiryDate)
    {
        $this->expiryDate = $expiryDate;

        return $this;
    }

    /**
     * Get expiryDate
     *
     * @return \DateTime 
     */
    public function getExpiryDate()
    {
        return $this->expiryDate;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return Identification
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
     * @return Identification
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
     * @return Identification
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
     * Add doctor
     *
     * @param \UtilBundle\Entity\Doctor $doctor
     * @return Identification
     */
    public function addDoctor(\UtilBundle\Entity\Doctor $doctor)
    {
        $this->doctor[] = $doctor;

        return $this;
    }

    /**
     * Remove doctor
     *
     * @param \UtilBundle\Entity\Doctor $doctor
     */
    public function removeDoctor(\UtilBundle\Entity\Doctor $doctor)
    {
        $this->doctor->removeElement($doctor);
    }

    /**
     * Get doctor
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDoctor()
    {
        return $this->doctor;
    }

    /**
     * Add agents
     *
     * @param \UtilBundle\Entity\Agent $agents
     * @return Identification
     */
    public function addAgent(\UtilBundle\Entity\Agent $agents)
    {
        $this->agents[] = $agents;

        return $this;
    }

    /**
     * Remove agents
     *
     * @param \UtilBundle\Entity\Agent $agents
     */
    public function removeAgent(\UtilBundle\Entity\Agent $agents)
    {
        $this->agents->removeElement($agents);
    }

    /**
     * Get agents
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAgents()
    {
        return $this->agents;
    }
}
