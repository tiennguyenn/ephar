<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Courier
 *
 * @ORM\Table(name="courier", indexes={@ORM\Index(name="FK_courier_personal", columns={"personal_information_id"}), @ORM\Index(name="FK_courier_phone", columns={"phone_id"}), @ORM\Index(name="FK_courier", columns={"bank_account_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\CourierRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Courier
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
     * @ORM\Column(name="name", type="string", length=250, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="short_name", type="string", length=50, nullable=false)
     */
    private $shortName;

    /**
     * @var string
     *
     * @ORM\Column(name="courier_code", type="string", length=50, nullable=false)
     */
    private $courierCode;

    /**
     * @var float
     *
     * @ORM\Column(name="margin", type="float", precision=10, scale=0, nullable=false)
     */
    private $margin;

    /**
     * @var float
     *
     * @ORM\Column(name="new_margin", type="float", precision=10, scale=0, nullable=true)
     */
    private $newMargin;

    /**
     * @var string
     *
     * @ORM\Column(name="business_registration_number", type="string", length=20, nullable=true)
     */
    private $businessRegistrationNumber;

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
     * @var \DateTime
     *
     * @ORM\Column(name="margin_effect_date", type="datetime", nullable=true)
     */
    private $marginEffectDate;

    /**
     * @var string
     *
     * @ORM\Column(name="api_token", type="string", length=30, nullable=true)
     */
    private $apiToken;

    /**
     * @var string
     *
     * @ORM\Column(name="secret_key", type="string", length=30, nullable=true)
     */
    private $secretKey;

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
     * @var \BankAccount
     *
     * @ORM\ManyToOne(targetEntity="BankAccount", cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="bank_account_id", referencedColumnName="id")
     * })
     */
    private $bankAccount;

    /**
     * @var \PersonalInformation
     *
     * @ORM\ManyToOne(targetEntity="PersonalInformation", cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="personal_information_id", referencedColumnName="id")
     * })
     */
    private $personalInformation;

    /**
     * @var \Phone
     *
     * @ORM\ManyToOne(targetEntity="Phone", cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="phone_id", referencedColumnName="id")
     * })
     */
    private $phone;

    /**
     * @ORM\OneToMany(targetEntity="CourierAddress", mappedBy="courier", cascade={"persist", "remove" })
     */
    private $courierAddresses;

    /**
     * @ORM\ManyToMany(targetEntity="Address", inversedBy="couriers",cascade={"persist", "remove" })
     * @ORM\JoinTable(name="courier_address")
    */
    private $addresses;

    /**
     * @ORM\OneToMany(targetEntity="CourierRate", mappedBy="courier", cascade={"persist", "remove" })
     */
    private $courierRates;

    public function __construct()
    {
        $this->courierAddresses = new ArrayCollection();
        $this->addresses = new ArrayCollection();
        $this->courierRates = new ArrayCollection();
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
     * Set courier code
     *
     * @param string $courierCode
     *
     * @return Courier
     */
    public function setCourierCode($courierCode)
    {
        $this->courierCode = $courierCode;

        return $this;
    }

    /**
     * Get courier code
     *
     * @return string
     */
    public function getCourierCode()
    {
        return $this->courierCode;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Courier
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
     * Set margin
     *
     * @param float $margin
     *
     * @return Courier
     */
    public function setMargin($margin)
    {
        $this->margin = $margin;

        return $this;
    }

    /**
     * Get margin
     *
     * @return float
     */
    public function getMargin()
    {
        return $this->margin;
    }

    /**
     * Set newMargin
     *
     * @param float $newMargin
     *
     * @return Courier
     */
    public function setNewMargin($newMargin)
    {
        $this->newMargin = $newMargin;

        return $this;
    }

    /**
     * Get newMargin
     *
     * @return float
     */
    public function getNewMargin()
    {
        return $this->newMargin;
    }

    /**
     * Set businessRegistrationNumber
     *
     * @param string $businessRegistrationNumber
     *
     * @return Courier
     */
    public function setBusinessRegistrationNumber($businessRegistrationNumber)
    {
        $this->businessRegistrationNumber = $businessRegistrationNumber;

        return $this;
    }

    /**
     * Get businessRegistrationNumber
     *
     * @return string
     */
    public function getBusinessRegistrationNumber()
    {
        return $this->businessRegistrationNumber;
    }

    /**
     * Set isGst
     *
     * @param boolean $isGst
     *
     * @return Courier
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
     *
     * @return Courier
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
     * Set marginEffectDate
     *
     * @param \DateTime $marginEffectDate
     *
     * @return Courier
     */
    public function setMarginEffectDate($marginEffectDate)
    {
        $this->marginEffectDate = $marginEffectDate;

        return $this;
    }

    /**
     * Get marginEffectDate
     *
     * @return \DateTime
     */
    public function getMarginEffectDate()
    {
        return $this->marginEffectDate;
    }

    /**
     * Set apiToken
     *
     * @param string $apiToken
     *
     * @return Courier
     */
    public function setApiToken($apiToken)
    {
        $this->apiToken = $apiToken;

        return $this;
    }

    /**
     * Get apiToken
     *
     * @return string
     */
    public function getApiToken()
    {
        return $this->apiToken;
    }

    /**
     * Set secretKey
     *
     * @param string $secretKey
     *
     * @return Courier
     */
    public function setSecretKey($secretKey)
    {
        $this->secretKey = $secretKey;

        return $this;
    }

    /**
     * Get secretKey
     *
     * @return string
     */
    public function getSecretKey()
    {
        return $this->secretKey;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return Courier
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
     *
     * @return Courier
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
     * Set personalInformation
     *
     * @param \UtilBundle\Entity\PersonalInformation $personalInformation
     *
     * @return Courier
     */
    public function setPersonalInformation(\UtilBundle\Entity\PersonalInformation $personalInformation = null)
    {
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
     * Set phone
     *
     * @param \UtilBundle\Entity\Phone $phone
     *
     * @return Courier
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
     * Add courierAddress
     *
     * @param \UtilBundle\Entity\CourierAddress $courierAddress
     *
     * @return Courier
     */
    public function addCourierAddress(\UtilBundle\Entity\CourierAddress $courierAddress)
    {
        $this->courierAddresses[] = $courierAddress;

        return $this;
    }

    /**
     * Remove courierAddress
     *
     * @param \UtilBundle\Entity\CourierAddress $courierAddress
     */
    public function removeCourierAddress(\UtilBundle\Entity\CourierAddress $courierAddress)
    {
        $this->courierAddresses->removeElement($courierAddress);
    }

    /**
     * Get courierAddresses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCourierAddresses()
    {
        return $this->courierAddresses;
    }

    /**
     * Add address
     *
     * @param \UtilBundle\Entity\Address $address
     *
     * @return Courier
     */
    public function addAddress(\UtilBundle\Entity\Address $address)
    {
        $this->addresses[] = $address;

        return $this;
    }

    /**
     * Remove address
     *
     * @param \UtilBundle\Entity\Address $address
     */
    public function removeAddress(\UtilBundle\Entity\Address $address)
    {
        $this->addresses->removeElement($address);
    }

    /**
     * Get addresses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAddresses()
    {
        return $this->addresses;
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
     * Add courierRate
     *
     * @param \UtilBundle\Entity\Courier $courierRate
     *
     * @return Courier
     */
    public function addCourierRate(\UtilBundle\Entity\Courier $courierRate)
    {
        $this->courierRates[] = $courierRate;

        return $this;
    }

    /**
     * Remove courierRate
     *
     * @param \UtilBundle\Entity\Courier $courierRate
     */
    public function removeCourierRate(\UtilBundle\Entity\Courier $courierRate)
    {
        $this->courierRates->removeElement($courierRate);
    }

    /**
     * Get courierRates
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCourierRates()
    {
        return $this->courierRates;
    }

    /**
     * Set bankAccount
     *
     * @param \UtilBundle\Entity\BankAccount $bankAccount
     *
     * @return Courier
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
     * Set shortName
     *
     * @param string $shortName
     *
     * @return Courier
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
