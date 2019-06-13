<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Address
 *
 * @ORM\Table(name="address", indexes={@ORM\Index(name="FK_address_sity", columns={"city_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\AddressRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Address
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
     * @ORM\Column(name="address_type_id", type="integer", nullable=true)
     */
    private $addressTypeId;

    /**
     * @var string
     *
     * @ORM\Column(name="line1", type="string", length=100, nullable=true)
     */
    private $line1;

    /**
     * @var string
     *
     * @ORM\Column(name="line2", type="string", length=100, nullable=true)
     */
    private $line2;

    /**
     * @var string
     *
     * @ORM\Column(name="line3", type="string", length=100, nullable=true)
     */
    private $line3;

    /**
     * @var string
     *
     * @ORM\Column(name="postal_code", type="string", length=10, nullable=true)
     */
    private $postalCode;

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
     * @var \City
     *
     * @ORM\ManyToOne(targetEntity="City")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     * })
     */
    private $city;

        /**
     * @var \Area
     *
     * @ORM\ManyToOne(targetEntity="Area")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="area_id", referencedColumnName="id")
     * })
     */
    private $area;
    
    /**
     * @ORM\ManyToMany(targetEntity="Agent", mappedBy="adresses",cascade={"persist", "remove" })
     * 
    */
    private $agents;
    /**
     * @ORM\ManyToMany(targetEntity="Agent", mappedBy="companyAdresses",cascade={"persist", "remove" })
     * 
    */
    private $cagents;
    /**
     * @ORM\ManyToMany(targetEntity="Doctor", mappedBy="adresses",cascade={"persist", "remove" })
     * 
    */
    private $doctors;
    /**
     * @ORM\ManyToMany(targetEntity="Courier", mappedBy="addresses",cascade={"persist", "remove" })
     * 
    */
    private $couriers;
    /**
     * @ORM\ManyToMany(targetEntity="Courier", mappedBy="addresses",cascade={"persist", "remove" })
     * @ORM\JoinTable(name="resolve_change_address")
     *
     */
    private $resolves;

    /**
     * @ORM\ManyToMany(targetEntity="Patient", mappedBy="addresses",cascade={"persist", "remove" })
     * @ORM\JoinTable(name="patient_address")
     *
     */
    private $patients;
     /**
     * Constructor
     */
    public function __construct()
    {
       
        $this->agents = new ArrayCollection();
        $this->doctors = new ArrayCollection();
        $this->couriers = new ArrayCollection();
        $this->cagents = new ArrayCollection();
        $this->resolves = new ArrayCollection();
        $this->patients = new ArrayCollection();
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
     * Set addressTypeId
     *
     * @param integer $addressTypeId
     * @return Address
     */
    public function setAddressTypeId($addressTypeId)
    {
        $this->addressTypeId = $addressTypeId;

        return $this;
    }

    /**
     * Get addressTypeId
     *
     * @return integer 
     */
    public function getAddressTypeId()
    {
        return $this->addressTypeId;
    }

    /**
     * Set line1
     *
     * @param string $line1
     * @return Address
     */
    public function setLine1($line1)
    {
        $this->line1 = $line1;

        return $this;
    }

    /**
     * Get line1
     *
     * @return string 
     */
    public function getLine1()
    {
        return $this->line1;
    }

    /**
     * Set line2
     *
     * @param string $line2
     * @return Address
     */
    public function setLine2($line2)
    {
        $this->line2 = $line2;

        return $this;
    }

    /**
     * Get line2
     *
     * @return string 
     */
    public function getLine2()
    {
        return $this->line2;
    }

    /**
     * Set line3
     *
     * @param string $line3
     * @return Address
     */
    public function setLine3($line3)
    {
        $this->line3 = $line3;

        return $this;
    }

    /**
     * Get line3
     *
     * @return string 
     */
    public function getLine3()
    {
        return $this->line3;
    }

    /**
     * Set postalCode
     *
     * @param string $postalCode
     * @return Address
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * Get postalCode
     *
     * @return string 
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return Address
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
     * @return Address
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
     * @return Address
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
     * Gets triggered only on insert

     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdOn = new \DateTime("now");
    }

    /**
     * Gets triggered every time on update

     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->updatedOn = new \DateTime("now");
    }


    /**
     * Set city
     *
     * @param \UtilBundle\Entity\City $city
     * @return Address
     */
    public function setCity(\UtilBundle\Entity\City $city = null)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return \UtilBundle\Entity\City 
     */
    public function getCity()
    {
        return $this->city;
    }


    /**
     * Add agents
     *
     * @param \UtilBundle\Entity\Agent $agents
     * @return Address
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
     * Add doctor
     *
     * @param \UtilBundle\Entity\Doctor $doctor
     *
     * @return Address
     */
    public function addDoctor(\UtilBundle\Entity\Doctor $doctor)
    {
        $this->doctors[] = $doctor;

        return $this;
    }

    /**
     * Remove doctor
     *
     * @param \UtilBundle\Entity\Doctor $doctor
     */
    public function removeDoctor(\UtilBundle\Entity\Doctor $doctor)
    {
        $this->doctors->removeElement($doctor);
    }

    /**
     * Get doctors
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDoctors()
    {
        return $this->doctors;
    }

    /**
     * Add courier
     *
     * @param \UtilBundle\Entity\Courier $courier
     *
     * @return Address
     */
    public function addCourier(\UtilBundle\Entity\Courier $courier)
    {
        $this->couriers[] = $courier;

        return $this;
    }

    /**
     * Remove courier
     *
     * @param \UtilBundle\Entity\Courier $courier
     */
    public function removeCourier(\UtilBundle\Entity\Courier $courier)
    {
        $this->couriers->removeElement($courier);
    }

    /**
     * Get couriers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCouriers()
    {
        return $this->couriers;
    }

    /**
     * Add cagent
     *
     * @param \UtilBundle\Entity\Agent $cagent
     *
     * @return Address
     */
    public function addCagent(\UtilBundle\Entity\Agent $cagent)
    {
        $this->cagents[] = $cagent;

        return $this;
    }

    /**
     * Remove cagent
     *
     * @param \UtilBundle\Entity\Agent $cagent
     */
    public function removeCagent(\UtilBundle\Entity\Agent $cagent)
    {
        $this->cagents->removeElement($cagent);
    }

    /**
     * Get cagents
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCagents()
    {
        return $this->cagents;
    }

    /**
     * Add resolf
     *
     * @param \UtilBundle\Entity\Courier $resolf
     *
     * @return Address
     */
    public function addResolf(\UtilBundle\Entity\Courier $resolf)
    {
        $this->resolves[] = $resolf;

        return $this;
    }

    /**
     * Remove resolf
     *
     * @param \UtilBundle\Entity\Courier $resolf
     */
    public function removeResolf(\UtilBundle\Entity\Courier $resolf)
    {
        $this->resolves->removeElement($resolf);
    }

    /**
     * Get resolves
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getResolves()
    {
        return $this->resolves;
    }

    /**
     * Add patient
     *
     * @param \UtilBundle\Entity\Patient $patient
     *
     * @return Address
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
     * Set area
     *
     * @param \UtilBundle\Entity\Area $area
     *
     * @return Address
     */
    public function setArea(\UtilBundle\Entity\Area $area = null)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * Get area
     *
     * @return \UtilBundle\Entity\Area
     */
    public function getArea()
    {
        return $this->area;
    }
}
