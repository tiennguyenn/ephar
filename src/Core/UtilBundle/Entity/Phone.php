<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Phone
 *
 * @ORM\Table(name="phone", indexes={@ORM\Index(name="phone_type_id", columns={"phone_type_id"}), @ORM\Index(name="country_id", columns={"country_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\PhoneRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Phone
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
     * @ORM\Column(name="area_code", type="string", length=4, nullable=true)
     */
    private $areaCode;

    /**
     * @var string
     *
     * @ORM\Column(name="number", type="string", length=50, nullable=true)
     */
    private $number;

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
     * @var \PhoneType
     *
     * @ORM\ManyToOne(targetEntity="PhoneType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="phone_type_id", referencedColumnName="id")
     * })
     */
    private $phoneType;

    /**
     * @var \Country
     *
     * @ORM\ManyToOne(targetEntity="Country")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="country_id", referencedColumnName="id")
     * })
     */
    private $country;
    
     /**
     * @ORM\ManyToMany(targetEntity="Agent", mappedBy="phones",cascade={"persist", "remove" })
     * @ORM\JoinTable(name="agent_phone")
     */
    private $agents;

    /**
     * @ORM\ManyToMany(targetEntity="Patient", mappedBy="phones", cascade={"persist"})
     * @ORM\JoinTable(name="patient_phone")
     */
    private $patients;

    /**
     * @ORM\ManyToMany(targetEntity="CareGiver", mappedBy="phones", cascade={"persist"})
     * @ORM\JoinTable(name="care_giver_phone")
     */
    private $caregivers;
     /**
     * @ORM\ManyToMany(targetEntity="Pharmacy", mappedBy="phones", cascade={"persist", "remove" })
     * 
    */
    private $pharmacies;


    /**
     * Constructor
     */
    public function __construct() {
        $this->patients = new ArrayCollection();
        $this->agents = new ArrayCollection();
        $this->caregivers = new ArrayCollection();
        $this->pharmacies = new ArrayCollection();
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
    
    public function removeId()
    {
        $this->id = null ;
    }
    /**
     * Set areaCode
     *
     * @param string $areaCode
     * @return Phone
     */
    public function setAreaCode($areaCode)
    {
        $this->areaCode = $areaCode;

        return $this;
    }

    /**
     * Get areaCode
     *
     * @return string 
     */
    public function getAreaCode()
    {
        return $this->areaCode;
    }

    /**
     * Set number
     *
     * @param string $number
     * @return Phone
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string 
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return Phone
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
     * @return Phone
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
     * Set phoneType
     *
     * @param \UtilBundle\Entity\PhoneType $phoneType
     * @return Phone
     */
    public function setPhoneType(\UtilBundle\Entity\PhoneType $phoneType = null)
    {
        $this->phoneType = $phoneType;

        return $this;
    }

    /**
     * Get phoneType
     *
     * @return \UtilBundle\Entity\PhoneType 
     */
    public function getPhoneType()
    {
        return $this->phoneType;
    }

    /**
     * Set country
     *
     * @param \UtilBundle\Entity\Country $country
     * @return Phone
     */
    public function setCountry(\UtilBundle\Entity\Country $country = null)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return \UtilBundle\Entity\Country 
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Add agents
     *
     * @param \UtilBundle\Entity\Agent $agents
     * @return Phone
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

    /**
     * Add patient
     *
     * @param \UtilBundle\Entity\Patient $patient
     * @return Phone
     */
    public function addPatient(\UtilBundle\Entity\Patient $patient)
    {
        $this->patients[] = $patient;

        return $this;
    }

    /**
     * Remove patient
     *
     * @param \UtilBundle\Entity\Patient $patient
     */
    public function removePatient(\UtilBundle\Entity\Patient $patient)
    {
        $this->patients->removeElement($patient);
    }

    /**
     * Get patients
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPatients()
    {
        return $this->patients;
    }

    /**
     * Add caregiver
     *
     * @param \UtilBundle\Entity\CareGiver $caregiver
     * @return Phone
     */
    public function addCareGiver(\UtilBundle\Entity\CareGiver $caregiver)
    {
        $this->caregivers[] = $caregiver;

        return $this;
    }

    /**
     * Remove caregiver
     *
     * @param \UtilBundle\Entity\CareGiver $caregiver
     */
    public function removeCareGiver(\UtilBundle\Entity\Patient $caregiver)
    {
        $this->caregivers->removeElement($caregiver);
    }

    /**
     * Get caregivers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCareGivers()
    {
        return $this->caregivers;
    }
    
    /*
     *  get full phone number
     */
    public function getPhoneNumber(){
        $loc = $this->getCountry()->getPhoneCode();        
        return '+'.$loc.' '. $this->getAreaCode().' '.$this->getNumber();        
    }

    /**
     * Add pharmacy
     *
     * @param \UtilBundle\Entity\Pharmacy $pharmacy
     *
     * @return Phone
     */
    public function addPharmacy(\UtilBundle\Entity\Pharmacy $pharmacy)
    {
        $this->pharmacies[] = $pharmacy;

        return $this;
    }

    /**
     * Remove pharmacy
     *
     * @param \UtilBundle\Entity\Pharmacy $pharmacy
     */
    public function removePharmacy(\UtilBundle\Entity\Pharmacy $pharmacy)
    {
        $this->pharmacies->removeElement($pharmacy);
    }

    /**
     * Get pharmacies
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPharmacies()
    {
        return $this->pharmacies;
    }
     /**
     * Gets triggered only on insert

     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdOn = new \DateTime("now");
    }

    
}
