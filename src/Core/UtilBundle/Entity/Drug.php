<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Drug
 *
 * @ORM\Table(name="drug", indexes={@ORM\Index(name="FK_drug_packing_type", columns={"packing_type_id"}), @ORM\Index(name="FK_drug_pharmacy", columns={"pharmacy_id"}), @ORM\Index(name="FK_drug_stock_status", columns={"stock_status_id"}), @ORM\Index(name="FK_drug_manufacturer", columns={"manufacturer_id"}), @ORM\Index(name="FK_drug_class", columns={"class"}), @ORM\Index(name="FK_drug_dose_unit", columns={"dose_unit_id"}), @ORM\Index(name="FK_drug_mc", columns={"manufacturing_country_id"}), @ORM\Index(name="FK_drug_ti", columns={"therapeutic_index_id"}), @ORM\Index(name="FK_drug_dfr", columns={"dosage_form_route_id"}), @ORM\Index(name="IDX_drug_upi", columns={"UPI"}), @ORM\Index(name="FK_drug_gst_code", columns={"gst_code_id"}), @ORM\Index(name="FK_drug", columns={"country_of_manufacturer_id"}), @ORM\Index(name="FK_drug_group", columns={"group_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\DrugRepository")
 */
class Drug
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
     * @ORM\Column(name="reference_code", type="string", length=20, nullable=true)
     */
    private $referenceCode;

    /**
     * @var string
     *
     * @ORM\Column(name="UPI", type="string", length=50, nullable=false)
     */
    private $upi;

    /**
     * @var string
     *
     * @ORM\Column(name="SKU", type="string", length=50, nullable=false)
     */
    private $sku;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=150, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=500, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="cost_price", type="decimal", precision=13, scale=2, nullable=false)
     */
    private $costPrice = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="list_price_domestic", type="decimal", precision=13, scale=2, nullable=false)
     */
    private $listPriceDomestic = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="list_price_international", type="decimal", precision=13, scale=2, nullable=false)
     */
    private $listPriceInternational = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="cost_price_to_clinic", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $costPriceToClinic = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="cost_price_to_clinic_oversea", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $costPriceToClinicOversea = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="packing_length", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $packingLength = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="packing_width", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $packingWidth = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="packing_height", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $packingHeight = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="packing_weight", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $packingWeight = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="pack_quantity", type="integer", nullable=false)
     */
    private $packQuantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="strip_quantity", type="integer", nullable=true)
     */
    private $stripQuantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="main_medical_therapy_id", type="integer", nullable=true)
     */
    private $mainMedicalTherapyId;

    /**
     * @var integer
     *
     * @ORM\Column(name="sub_medical_therapy_id", type="integer", nullable=true)
     */
    private $subMedicalTherapyId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="discontinued_on", type="datetime", nullable=true)
     */
    private $discontinuedOn;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_short_life", type="integer", nullable=true)
     */
    private $isShortLife = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="minimum_order_quantity", type="integer", nullable=false)
     */
    private $minimumOrderQuantity;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_cold_chain", type="boolean", nullable=true)
     */
    private $isColdChain;

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
     * @var \DateTime
     *
     * @ORM\Column(name="deleted_on", type="datetime", nullable=true)
     */
    private $deletedOn;

    /**
     * @var \CountryOfManufacturer
     *
     * @ORM\ManyToOne(targetEntity="CountryOfManufacturer")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="country_of_manufacturer_id", referencedColumnName="id")
     * })
     */
    private $countryOfManufacturer;

    /**
     * @var \DrugType
     *
     * @ORM\ManyToOne(targetEntity="DrugType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="class", referencedColumnName="id")
     * })
     */
    private $class;

    /**
     * @var \DosageFormRoute
     *
     * @ORM\ManyToOne(targetEntity="DosageFormRoute")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="dosage_form_route_id", referencedColumnName="id")
     * })
     */
    private $dosageFormRoute;

    /**
     * @var \PackMeasure
     *
     * @ORM\ManyToOne(targetEntity="PackMeasure")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="dose_unit_id", referencedColumnName="id")
     * })
     */
    private $doseUnit;

    /**
     * @var \DrugGroup
     *
     * @ORM\ManyToOne(targetEntity="DrugGroup")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="group_id", referencedColumnName="id")
     * })
     */
    private $group;

    /**
     * @var \GstCode
     *
     * @ORM\ManyToOne(targetEntity="GstCode")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="gst_code_id", referencedColumnName="id")
     * })
     */
    private $gstCode;

    /**
     * @var \Manufacturer
     *
     * @ORM\ManyToOne(targetEntity="Manufacturer")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="manufacturer_id", referencedColumnName="id")
     * })
     */
    private $manufacturer;

    /**
     * @var \Country
     *
     * @ORM\ManyToOne(targetEntity="Country")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="manufacturing_country_id", referencedColumnName="id")
     * })
     */
    private $manufacturingCountry;

    /**
     * @var \PackingType
     *
     * @ORM\ManyToOne(targetEntity="PackingType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="packing_type_id", referencedColumnName="id")
     * })
     */
    private $packingType;

    /**
     * @var \Pharmacy
     *
     * @ORM\ManyToOne(targetEntity="Pharmacy")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pharmacy_id", referencedColumnName="id")
     * })
     */
    private $pharmacy;

    /**
     * @var \StockStatus
     *
     * @ORM\ManyToOne(targetEntity="StockStatus")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="stock_status_id", referencedColumnName="id")
     * })
     */
    private $stockStatus;

    /**
     * @var \TherapeuticIndex
     *
     * @ORM\ManyToOne(targetEntity="TherapeuticIndex")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="therapeutic_index_id", referencedColumnName="id")
     * })
     */
    private $therapeuticIndex;



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
     * Set referenceCode
     *
     * @param string $referenceCode
     * @return Drug
     */
    public function setReferenceCode($referenceCode)
    {
        $this->referenceCode = $referenceCode;

        return $this;
    }

    /**
     * Get referenceCode
     *
     * @return string 
     */
    public function getReferenceCode()
    {
        return $this->referenceCode;
    }

    /**
     * Set upi
     *
     * @param string $upi
     * @return Drug
     */
    public function setUpi($upi)
    {
        $this->upi = $upi;

        return $this;
    }

    /**
     * Get upi
     *
     * @return string 
     */
    public function getUpi()
    {
        return $this->upi;
    }

    /**
     * Set sku
     *
     * @param string $sku
     * @return Drug
     */
    public function setSku($sku)
    {
        $this->sku = $sku;

        return $this;
    }

    /**
     * Get sku
     *
     * @return string 
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Drug
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
     * Set description
     *
     * @param string $description
     * @return Drug
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set costPrice
     *
     * @param string $costPrice
     * @return Drug
     */
    public function setCostPrice($costPrice)
    {
        $this->costPrice = $costPrice;

        return $this;
    }

    /**
     * Get costPrice
     *
     * @return string 
     */
    public function getCostPrice()
    {
        return $this->costPrice;
    }

    /**
     * Set listPriceDomestic
     *
     * @param string $listPriceDomestic
     * @return Drug
     */
    public function setListPriceDomestic($listPriceDomestic)
    {
        $this->listPriceDomestic = $listPriceDomestic;

        return $this;
    }

    /**
     * Get listPriceDomestic
     *
     * @return string 
     */
    public function getListPriceDomestic()
    {
        return $this->listPriceDomestic;
    }

    /**
     * Set listPriceInternational
     *
     * @param string $listPriceInternational
     * @return Drug
     */
    public function setListPriceInternational($listPriceInternational)
    {
        $this->listPriceInternational = $listPriceInternational;

        return $this;
    }

    /**
     * Get listPriceInternational
     *
     * @return string 
     */
    public function getListPriceInternational()
    {
        return $this->listPriceInternational;
    }

    /**
     * Set costPriceToClinic
     *
     * @param string $costPriceToClinic
     * @return Drug
     */
    public function setCostPriceToClinic($costPriceToClinic)
    {
        $this->costPriceToClinic = $costPriceToClinic;

        return $this;
    }

    /**
     * Get costPriceToClinic
     *
     * @return string 
     */
    public function getCostPriceToClinic()
    {
        return $this->costPriceToClinic;
    }

    /**
     * Set costPriceToClinicOversea
     *
     * @param string $costPriceToClinicOversea
     * @return Drug
     */
    public function setCostPriceToClinicOversea($costPriceToClinicOversea)
    {
        $this->costPriceToClinicOversea = $costPriceToClinicOversea;

        return $this;
    }

    /**
     * Get costPriceToClinicOversea
     *
     * @return string 
     */
    public function getCostPriceToClinicOversea()
    {
        return $this->costPriceToClinicOversea;
    }

    /**
     * Set packingLength
     *
     * @param string $packingLength
     * @return Drug
     */
    public function setPackingLength($packingLength)
    {
        $this->packingLength = $packingLength;

        return $this;
    }

    /**
     * Get packingLength
     *
     * @return string 
     */
    public function getPackingLength()
    {
        return $this->packingLength;
    }

    /**
     * Set packingWidth
     *
     * @param string $packingWidth
     * @return Drug
     */
    public function setPackingWidth($packingWidth)
    {
        $this->packingWidth = $packingWidth;

        return $this;
    }

    /**
     * Get packingWidth
     *
     * @return string 
     */
    public function getPackingWidth()
    {
        return $this->packingWidth;
    }

    /**
     * Set packingHeight
     *
     * @param string $packingHeight
     * @return Drug
     */
    public function setPackingHeight($packingHeight)
    {
        $this->packingHeight = $packingHeight;

        return $this;
    }

    /**
     * Get packingHeight
     *
     * @return string 
     */
    public function getPackingHeight()
    {
        return $this->packingHeight;
    }

    /**
     * Set packingWeight
     *
     * @param string $packingWeight
     * @return Drug
     */
    public function setPackingWeight($packingWeight)
    {
        $this->packingWeight = $packingWeight;

        return $this;
    }

    /**
     * Get packingWeight
     *
     * @return string 
     */
    public function getPackingWeight()
    {
        return $this->packingWeight;
    }

    /**
     * Set packQuantity
     *
     * @param integer $packQuantity
     * @return Drug
     */
    public function setPackQuantity($packQuantity)
    {
        $this->packQuantity = $packQuantity;

        return $this;
    }

    /**
     * Get packQuantity
     *
     * @return integer 
     */
    public function getPackQuantity()
    {
        return $this->packQuantity;
    }

    /**
     * Set stripQuantity
     *
     * @param integer $stripQuantity
     * @return Drug
     */
    public function setStripQuantity($stripQuantity)
    {
        $this->stripQuantity = $stripQuantity;

        return $this;
    }

    /**
     * Get stripQuantity
     *
     * @return integer 
     */
    public function getStripQuantity()
    {
        return $this->stripQuantity;
    }

    /**
     * Set mainMedicalTherapyId
     *
     * @param integer $mainMedicalTherapyId
     * @return Drug
     */
    public function setMainMedicalTherapyId($mainMedicalTherapyId)
    {
        $this->mainMedicalTherapyId = $mainMedicalTherapyId;

        return $this;
    }

    /**
     * Get mainMedicalTherapyId
     *
     * @return integer 
     */
    public function getMainMedicalTherapyId()
    {
        return $this->mainMedicalTherapyId;
    }

    /**
     * Set subMedicalTherapyId
     *
     * @param integer $subMedicalTherapyId
     * @return Drug
     */
    public function setSubMedicalTherapyId($subMedicalTherapyId)
    {
        $this->subMedicalTherapyId = $subMedicalTherapyId;

        return $this;
    }

    /**
     * Get subMedicalTherapyId
     *
     * @return integer 
     */
    public function getSubMedicalTherapyId()
    {
        return $this->subMedicalTherapyId;
    }

    /**
     * Set discontinuedOn
     *
     * @param \DateTime $discontinuedOn
     * @return Drug
     */
    public function setDiscontinuedOn($discontinuedOn)
    {
        $this->discontinuedOn = $discontinuedOn;

        return $this;
    }

    /**
     * Get discontinuedOn
     *
     * @return \DateTime 
     */
    public function getDiscontinuedOn()
    {
        return $this->discontinuedOn;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return Drug
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
     * @return Drug
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
     * @return Drug
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
     * Set countryOfManufacturer
     *
     * @param \UtilBundle\Entity\CountryOfManufacturer $countryOfManufacturer
     * @return Drug
     */
    public function setCountryOfManufacturer(\UtilBundle\Entity\CountryOfManufacturer $countryOfManufacturer = null)
    {
        $this->countryOfManufacturer = $countryOfManufacturer;

        return $this;
    }

    /**
     * Get countryOfManufacturer
     *
     * @return \UtilBundle\Entity\CountryOfManufacturer 
     */
    public function getCountryOfManufacturer()
    {
        return $this->countryOfManufacturer;
    }

    /**
     * Set class
     *
     * @param \UtilBundle\Entity\DrugType $class
     * @return Drug
     */
    public function setClass(\UtilBundle\Entity\DrugType $class = null)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Get class
     *
     * @return \UtilBundle\Entity\DrugType 
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set dosageFormRoute
     *
     * @param \UtilBundle\Entity\DosageFormRoute $dosageFormRoute
     * @return Drug
     */
    public function setDosageFormRoute(\UtilBundle\Entity\DosageFormRoute $dosageFormRoute = null)
    {
        $this->dosageFormRoute = $dosageFormRoute;

        return $this;
    }

    /**
     * Get dosageFormRoute
     *
     * @return \UtilBundle\Entity\DosageFormRoute 
     */
    public function getDosageFormRoute()
    {
        return $this->dosageFormRoute;
    }

    /**
     * Set doseUnit
     *
     * @param \UtilBundle\Entity\PackMeasure $doseUnit
     * @return Drug
     */
    public function setDoseUnit(\UtilBundle\Entity\PackMeasure $doseUnit = null)
    {
        $this->doseUnit = $doseUnit;

        return $this;
    }

    /**
     * Get doseUnit
     *
     * @return \UtilBundle\Entity\PackMeasure 
     */
    public function getDoseUnit()
    {
        return $this->doseUnit;
    }

    /**
     * Set group
     *
     * @param \UtilBundle\Entity\DrugGroup $group
     * @return Drug
     */
    public function setGroup(\UtilBundle\Entity\DrugGroup $group = null)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return \UtilBundle\Entity\DrugGroup 
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set gstCode
     *
     * @param \UtilBundle\Entity\GstCode $gstCode
     * @return Drug
     */
    public function setGstCode(\UtilBundle\Entity\GstCode $gstCode = null)
    {
        $this->gstCode = $gstCode;

        return $this;
    }

    /**
     * Get gstCode
     *
     * @return \UtilBundle\Entity\GstCode 
     */
    public function getGstCode()
    {
        return $this->gstCode;
    }

    /**
     * Set manufacturer
     *
     * @param \UtilBundle\Entity\Manufacturer $manufacturer
     * @return Drug
     */
    public function setManufacturer(\UtilBundle\Entity\Manufacturer $manufacturer = null)
    {
        $this->manufacturer = $manufacturer;

        return $this;
    }

    /**
     * Get manufacturer
     *
     * @return \UtilBundle\Entity\Manufacturer 
     */
    public function getManufacturer()
    {
        return $this->manufacturer;
    }

    /**
     * Set manufacturingCountry
     *
     * @param \UtilBundle\Entity\Country $manufacturingCountry
     * @return Drug
     */
    public function setManufacturingCountry(\UtilBundle\Entity\Country $manufacturingCountry = null)
    {
        $this->manufacturingCountry = $manufacturingCountry;

        return $this;
    }

    /**
     * Get manufacturingCountry
     *
     * @return \UtilBundle\Entity\Country 
     */
    public function getManufacturingCountry()
    {
        return $this->manufacturingCountry;
    }

    /**
     * Set packingType
     *
     * @param \UtilBundle\Entity\PackingType $packingType
     * @return Drug
     */
    public function setPackingType(\UtilBundle\Entity\PackingType $packingType = null)
    {
        $this->packingType = $packingType;

        return $this;
    }

    /**
     * Get packingType
     *
     * @return \UtilBundle\Entity\PackingType 
     */
    public function getPackingType()
    {
        return $this->packingType;
    }

    /**
     * Set pharmacy
     *
     * @param \UtilBundle\Entity\Pharmacy $pharmacy
     * @return Drug
     */
    public function setPharmacy(\UtilBundle\Entity\Pharmacy $pharmacy = null)
    {
        $this->pharmacy = $pharmacy;

        return $this;
    }

    /**
     * Get pharmacy
     *
     * @return \UtilBundle\Entity\Pharmacy 
     */
    public function getPharmacy()
    {
        return $this->pharmacy;
    }

    /**
     * Set stockStatus
     *
     * @param \UtilBundle\Entity\StockStatus $stockStatus
     * @return Drug
     */
    public function setStockStatus(\UtilBundle\Entity\StockStatus $stockStatus = null)
    {
        $this->stockStatus = $stockStatus;

        return $this;
    }

    /**
     * Get stockStatus
     *
     * @return \UtilBundle\Entity\StockStatus 
     */
    public function getStockStatus()
    {
        return $this->stockStatus;
    }

    /**
     * Set therapeuticIndex
     *
     * @param \UtilBundle\Entity\TherapeuticIndex $therapeuticIndex
     * @return Drug
     */
    public function setTherapeuticIndex(\UtilBundle\Entity\TherapeuticIndex $therapeuticIndex = null)
    {
        $this->therapeuticIndex = $therapeuticIndex;

        return $this;
    }

    /**
     * Get therapeuticIndex
     *
     * @return \UtilBundle\Entity\TherapeuticIndex 
     */
    public function getTherapeuticIndex()
    {
        return $this->therapeuticIndex;
    }
    /**
     * Set isShortLife
     *
     * @param string $isShortLife
     *
     * @return Drug
     */
    public function setIsShortLife($isShortLife)
    {
        $this->isShortLife = $isShortLife;

        return $this;
    }

    /**
     * Get isShortLife
     *
     * @return string
     */
    public function getIsShortLife()
    {
        return $this->isShortLife;
    }


    /**
     * Set minimumOrderQuantity
     *
     * @param integer $minimumOrderQuantity
     * @return Drug
     */
    public function setMinimumOrderQuantity($minimumOrderQty)
    {
        $this->minimumOrderQuantity = $minimumOrderQty;

        return $this;
    }

    /**
     * Get minimumOrderQuantity
     *
     * @return integer
     */
    public function getMinimumOrderQuantity()
    {
        return $this->minimumOrderQuantity;
    }
    
    /**
     * Set coldChain
     *
     * @param boolean $coldChain
     * @return Drug
     */
    public function setcoldChain($coldChain)
    {
        $this->isColdChain = $coldChain;

        return $this;
    }

    /**
     * Get coldChain
     *
     * @return boolean
     */
    public function getcoldChain()
    {
        return $this->isColdChain;
    }

    /**
     * Set isColdChain
     *
     * @param boolean $isColdChain
     *
     * @return Drug
     */
    public function setIsColdChain($isColdChain)
    {
        $this->isColdChain = $isColdChain;

        return $this;
    }

    /**
     * Get isColdChain
     *
     * @return boolean
     */
    public function getIsColdChain()
    {
        return $this->isColdChain;
    }
}
