<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Pharmacy
 *
 * @ORM\Table(name="pharmacy", indexes={@ORM\Index(name="pickup_address_id", columns={"pickup_address_id"}), @ORM\Index(name="billing_address_id", columns={"billing_address_id"}), @ORM\Index(name="physical_address_id", columns={"physical_address_id"}), @ORM\Index(name="mailing_address_id", columns={"mailing_address_id"}), @ORM\Index(name="registered_address_id", columns={"registered_address_id"}), @ORM\Index(name="FK_pharmacy_bank_account", columns={"bank_account_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\PharmacyRepository")
 */
class Pharmacy
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
     * @ORM\Column(name="pharmacy_code", type="string", length=20, nullable=true)
     */
    private $pharmacyCode;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_gst", type="boolean", nullable=true)
     */
    private $isGst;

    /**
     * @var string
     *
     * @ORM\Column(name="gst_no", type="string", length=50, nullable=true)
     */
    private $gstNo;

    /**
     * @var string
     *
     * @ORM\Column(name="new_gst_no", type="string", length=50, nullable=true)
     */
    private $newGstNo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="gst_effective_date", type="datetime", nullable=true)
     */
    private $gstEffectiveDate;

    /**
     * @var string
     *
     * @ORM\Column(name="short_name", type="string", length=50, nullable=false)
     */
    private $shortName;

    /**
     * @var string
     *
     * @ORM\Column(name="business_name", type="string", length=150, nullable=true)
     */
    private $businessName;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=150, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="UEN", type="string", length=50, nullable=false)
     */
    private $uen;

    /**
     * @var string
     *
     * @ORM\Column(name="permit_number", type="string", length=50, nullable=false)
     */
    private $permitNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="email_address", type="string", length=50, nullable=false)
     */
    private $emailAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="pharmacist_name", type="string", length=50, nullable=false)
     */
    private $pharmacistName;

    /**
     * @var string
     *
     * @ORM\Column(name="pharmacist_license", type="string", length=50, nullable=false)
     */
    private $pharmacistLicense;

    /**
     * @var \BankAccount
     *
     * @ORM\ManyToOne(targetEntity="BankAccount", cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="bank_account_id", referencedColumnName="id")
     * })
     */
    private $bankAccount;

    /**
     * @var \Address
     *
     * @ORM\ManyToOne(targetEntity="Address")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pickup_address_id", referencedColumnName="id")
     * })
     */
    private $pickupAddress;

    /**
     * @var \Address
     *
     * @ORM\ManyToOne(targetEntity="Address")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="billing_address_id", referencedColumnName="id")
     * })
     */
    private $billingAddress;

    /**
     * @var \Address
     *
     * @ORM\ManyToOne(targetEntity="Address")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="physical_address_id", referencedColumnName="id")
     * })
     */
    private $physicalAddress;

    /**
     * @var \Address
     *
     * @ORM\ManyToOne(targetEntity="Address")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="mailing_address_id", referencedColumnName="id")
     * })
     */
    private $mailingAddress;

    /**
     * @var \Address
     *
     * @ORM\ManyToOne(targetEntity="Address",cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="registered_address_id", referencedColumnName="id")
     * })
     */
    private $registeredAddress;

    /**
     * @var \Drug
     *
     * @ORM\OneToMany(targetEntity="Drug", mappedBy="pharmacy")
     */
    private $drugs;
    /**
     * @ORM\ManyToMany(targetEntity="Phone", inversedBy="pharmacies",cascade={"persist", "remove" })
     * 
     * @ORM\JoinTable(name="pharmacy_phone")
    */
    private $phones;
    
    /**
     * @var string
     *
     * @ORM\Column(name="contact_firstname", type="string", length=250, nullable=false)
     */
    private $contactFirstname;
    
    /**
     * @var string
     *
     * @ORM\Column(name="contact_lastname", type="string", length=250, nullable=false)
     */
    private $contactLastname;
   

    public function __construct() {
        $this->drugs = new ArrayCollection();
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
     * Set pharmacyCode
     *
     * @param string $pharmacyCode
     * @return Pharmacy
     */
    public function setPharmacyCode($pharmacyCode)
    {
        $this->pharmacyCode = $pharmacyCode;

        return $this;
    }

    /**
     * Get pharmacyCode
     *
     * @return string 
     */
    public function getPharmacyCode()
    {
        return $this->pharmacyCode;
    }

    /**
     * Set isGst
     *
     * @param boolean $isGst
     * @return Pharmacy
     */
    public function setIsGst($isGst)
    {
        $this->isGst = $isGst;

        return $this;
    }

    /**
     * Get isGst
     *
     * @return boolean 
     */
    public function getIsGst()
    {
        return $this->isGst;
    }

    /**
     * Set gstNo
     *
     * @param string $gstNo
     * @return Pharmacy
     */
    public function setGstNo($gstNo)
    {
        $this->gstNo = $gstNo;

        return $this;
    }

    /**
     * Get gstNo
     *
     * @return string 
     */
    public function getGstNo()
    {
        return $this->gstNo;
    }

    /**
     * Set newGstNo
     *
     * @param string $newGstNo
     * @return Pharmacy
     */
    public function setNewGstNo($newGstNo)
    {
        $this->newGstNo = $newGstNo;

        return $this;
    }

    /**
     * Get newGstNo
     *
     * @return string 
     */
    public function getNewGstNo()
    {
        return $this->newGstNo;
    }

    /**
     * Set gst effective date
     *
     * @param \DateTime $gstEffectiveDate
     * @return PharmacyPhone
     */
    public function setGstEffectiveDate($gstEffectiveDate)
    {
        $this->gstEffectiveDate = $gstEffectiveDate;

        return $this;
    }

    /**
     * Get gst effective date
     *
     * @return \DateTime 
     */
    public function getGstEffectiveDate()
    {
        return $this->gstEffectiveDate;
    }

    /**
     * Set businessName
     *
     * @param string $businessName
     * @return Pharmacy
     */
    public function setBusinessName($businessName)
    {
        $this->businessName = $businessName;

        return $this;
    }

    /**
     * Get businessName
     *
     * @return string 
     */
    public function getBusinessName()
    {
        return $this->businessName;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Pharmacy
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set uen
     *
     * @param string $uen
     * @return Pharmacy
     */
    public function setUen($uen)
    {
        $this->uen = $uen;

        return $this;
    }

    /**
     * Get uen
     *
     * @return string 
     */
    public function getUen()
    {
        return $this->uen;
    }

    /**
     * Set permitNumber
     *
     * @param string $permitNumber
     * @return Pharmacy
     */
    public function setPermitNumber($permitNumber)
    {
        $this->permitNumber = $permitNumber;

        return $this;
    }

    /**
     * Get permitNumber
     *
     * @return string 
     */
    public function getPermitNumber()
    {
        return $this->permitNumber;
    }

    /**
     * Set emailAddress
     *
     * @param string $emailAddress
     * @return Pharmacy
     */
    public function setEmailAddress($emailAddress)
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    /**
     * Get emailAddress
     *
     * @return string 
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * Set pharmacistName
     *
     * @param string $pharmacistName
     * @return Pharmacy
     */
    public function setPharmacistName($pharmacistName)
    {
        $this->pharmacistName = $pharmacistName;

        return $this;
    }

    /**
     * Get pharmacistName
     *
     * @return string 
     */
    public function getPharmacistName()
    {
        return $this->pharmacistName;
    }

    /**
     * Set pharmacistLicense
     *
     * @param string $pharmacistLicense
     * @return Pharmacy
     */
    public function setPharmacistLicense($pharmacistLicense)
    {
        $this->pharmacistLicense = $pharmacistLicense;

        return $this;
    }

    /**
     * Get pharmacistLicense
     *
     * @return string 
     */
    public function getPharmacistLicense()
    {
        return $this->pharmacistLicense;
    }

    /**
     * Set bankAccount
     *
     * @param \UtilBundle\Entity\BankAccount $bankAccount
     * @return Pharmacy
     */
    public function setBankAccount(\UtilBundle\Entity\BankAccount $bankAccount = null)
    {
        $this->bankAccount = $bankAccount;

        return $this;
    }

    /**
     * Get bankAccount
     *
     * @return \UtilBundle\Entity\BankAccount 
     */
    public function getBankAccount()
    {
        return $this->bankAccount;
    }

    /**
     * Set pickupAddress
     *
     * @param \UtilBundle\Entity\Address $pickupAddress
     * @return Pharmacy
     */
    public function setPickupAddress(\UtilBundle\Entity\Address $pickupAddress = null)
    {
        $this->pickupAddress = $pickupAddress;

        return $this;
    }

    /**
     * Get pickupAddress
     *
     * @return \UtilBundle\Entity\Address 
     */
    public function getPickupAddress()
    {
        return $this->pickupAddress;
    }

    /**
     * Set billingAddress
     *
     * @param \UtilBundle\Entity\Address $billingAddress
     * @return Pharmacy
     */
    public function setBillingAddress(\UtilBundle\Entity\Address $billingAddress = null)
    {
        $this->billingAddress = $billingAddress;

        return $this;
    }

    /**
     * Get billingAddress
     *
     * @return \UtilBundle\Entity\Address 
     */
    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    /**
     * Set physicalAddress
     *
     * @param \UtilBundle\Entity\Address $physicalAddress
     * @return Pharmacy
     */
    public function setPhysicalAddress(\UtilBundle\Entity\Address $physicalAddress = null)
    {
        $this->physicalAddress = $physicalAddress;

        return $this;
    }

    /**
     * Get physicalAddress
     *
     * @return \UtilBundle\Entity\Address 
     */
    public function getPhysicalAddress()
    {
        return $this->physicalAddress;
    }

    /**
     * Set mailingAddress
     *
     * @param \UtilBundle\Entity\Address $mailingAddress
     * @return Pharmacy
     */
    public function setMailingAddress(\UtilBundle\Entity\Address $mailingAddress = null)
    {
        $this->mailingAddress = $mailingAddress;

        return $this;
    }

    /**
     * Get mailingAddress
     *
     * @return \UtilBundle\Entity\Address 
     */
    public function getMailingAddress()
    {
        return $this->mailingAddress;
    }

    /**
     * Set registeredAddress
     *
     * @param \UtilBundle\Entity\Address $registeredAddress
     * @return Pharmacy
     */
    public function setRegisteredAddress(\UtilBundle\Entity\Address $registeredAddress = null)
    {
        $this->registeredAddress = $registeredAddress;

        return $this;
    }

    /**
     * Get registeredAddress
     *
     * @return \UtilBundle\Entity\Address 
     */
    public function getRegisteredAddress()
    {
        return $this->registeredAddress;
    }


    /**
     * Add drugs
     *
     * @param \UtilBundle\Entity\Drug $drugs
     * @return Pharmacy
     */
    public function addDrug(\UtilBundle\Entity\Drug $drugs)
    {
        $this->drugs[] = $drugs;

        return $this;
    }

    /**
     * Remove drugs
     *
     * @param \UtilBundle\Entity\Drug $drugs
     */
    public function removeDrug(\UtilBundle\Entity\Drug $drugs)
    {
        $this->drugs->removeElement($drugs);
    }

    /**
     * Get drugs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDrugs()
    {
        return $this->drugs;
    }





    /**
     * Add phone
     *
     * @param \UtilBundle\Entity\Phone $phone
     *
     * @return Pharmacy
     */
    public function addPhone(\UtilBundle\Entity\Phone $phone)
    {
        $this->phones[] = $phone;

        return $this;
    }

    /**
     * Remove phone
     *
     * @param \UtilBundle\Entity\Phone $phone
     */
    public function removePhone(\UtilBundle\Entity\Phone $phone)
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
    
    /**
     * Set contactFirstname
     *
     * @param string $contactFirstname
     * @return Pharmacy
     */
    public function setContactFirstname($contactFirstname)
    {
        $this->contactFirstname = $contactFirstname;

        return $this;
    }

    /**
     * Get contactFirstname
     *
     * @return string 
     */
    public function getContactFirstname()
    {
        return $this->contactFirstname;
    }
    
    /**
     * Set contactLastname
     *
     * @param string $contactLastname
     * @return Pharmacy
     */
    public function setContactLastname($contactLastname)
    {
        $this->contactLastname = $contactLastname;

        return $this;
    }

    /**
     * Get contactLastname
     *
     * @return string 
     */
    public function getContactLastname()
    {
        return $this->contactLastname;
    }

    /**
     * Set shortName
     *
     * @param string $shortName
     *
     * @return Pharmacy
     */
    public function setShortName($shortName)
    {
        $this->shortName = $shortName;

        return $this;
    }

    /**
     * Get shortName
     *
     * @return string
     */
    public function getShortName()
    {
        return $this->shortName;
    }
}
