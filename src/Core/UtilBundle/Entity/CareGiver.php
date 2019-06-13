<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * CareGiver
 *
 * @ORM\Table(name="care_giver", indexes={@ORM\Index(name="personal_information_id", columns={"personal_information_id"}), @ORM\Index(name="relationship_type_id", columns={"relationship_type_id"}), @ORM\Index(name="care_giver_patient_1", columns={"patient_id"})})
 * @ORM\Entity
 */
class CareGiver
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
     * @ORM\Column(name="custom_relationship_type", type="string", length=10, nullable=true)
     */
    private $customRelationshipType;

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
     * @ORM\ManyToOne(targetEntity="Patient", inversedBy="caregivers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="patient_id", referencedColumnName="id")
     * })
     */
    private $patient;

    /**
     * @var \PersonalInformation
     *
     * @ORM\ManyToOne(targetEntity="PersonalInformation", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="personal_information_id", referencedColumnName="id")
     * })
     */
    private $personalInformation;

    /**
     * @var \RelationshipType
     *
     * @ORM\ManyToOne(targetEntity="RelationshipType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="relationship_type_id", referencedColumnName="id")
     * })
     */
    private $relationshipType;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Phone", inversedBy="care_giver", cascade={"persist"})
     * @ORM\JoinTable(name="care_giver_phone")
     */
    private $phones;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->phones = new ArrayCollection();
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
     * Set customRelationshipType
     *
     * @param string $customRelationshipType
     * @return CareGiver
     */
    public function setCustomRelationshipType($customRelationshipType)
    {
        $this->customRelationshipType = $customRelationshipType;

        return $this;
    }

    /**
     * Get customRelationshipType
     *
     * @return string 
     */
    public function getCustomRelationshipType()
    {
        return $this->customRelationshipType;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return CareGiver
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
     * @return CareGiver
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
     * @return CareGiver
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
     * Set personalInformation
     *
     * @param \UtilBundle\Entity\PersonalInformation $personalInformation
     * @return CareGiver
     */
    public function setPersonalInformation(\UtilBundle\Entity\PersonalInformation $personalInformation = null)
    {
        $personalInformation->setPassportNo('');
        $this->personalInformation = $personalInformation;

        return $this;
    }

    /**
     * Get personalInformation
     *
     * @return \UtilBundle\Entity\PersonalInformation 
     */
    public function getPersonalInformation()
    {
        return $this->personalInformation;
    }

    /**
     * Set relationshipType
     *
     * @param \UtilBundle\Entity\RelationshipType $relationshipType
     * @return CareGiver
     */
    public function setRelationshipType(\UtilBundle\Entity\RelationshipType $relationshipType = null)
    {
        $this->relationshipType = $relationshipType;

        return $this;
    }

    /**
     * Get relationshipType
     *
     * @return \UtilBundle\Entity\RelationshipType 
     */
    public function getRelationshipType()
    {
        return $this->relationshipType;
    }

    /**
     * Add phone
     *
     * @param Phone $phone
     * @return CareGiver
     */
    public function addPhone(Phone $phone)
    {
        $this->phones[] = $phone;

        return $this;
    }

    /**
     * Remove phone
     *
     * @param Phone $phone
     */
    public function removePhone(Phone $phone)
    {
        $this->phones->removeElement($phone);
    }

    /**
     * Get phones
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPhones()
    {
        return $this->phones;
    }
}
