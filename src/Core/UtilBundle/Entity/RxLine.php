<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RxLine
 *
 * @ORM\Table(name="rx_line", indexes={@ORM\Index(name="frequency_duration_unit", columns={"frequency_duration_unit"}), @ORM\Index(name="drug_id", columns={"drug_id"}), @ORM\Index(name="rx_id", columns={"rx_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\RxLineRepository")
 * @ORM\HasLifecycleCallbacks
 */
class RxLine
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
     * @ORM\Column(name="name", type="string", length=150, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=500, nullable=false)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer", nullable=true)
     */
    private $quantity = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="dosage_action", type="string", length=45, nullable=true)
     */
    private $dosageAction;

    /**
     * @var string
     *
     * @ORM\Column(name="dosage_quantity", type="string", length=45, nullable=true)
     */
    private $dosageQuantity;

    /**
     * @var string
     *
     * @ORM\Column(name="dosage_form", type="string", length=45, nullable=true)
     */
    private $dosageForm;

    /**
     * @var string
     *
     * @ORM\Column(name="frequency_quantity", type="string", length=45, nullable=true)
     */
    private $frequencyQuantity;

    /**
     * @var string
     *
     * @ORM\Column(name="frequency_duration_unit", type="string", length=45, nullable=true)
     */
    private $frequencyDurationUnit;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_taken_with_food", type="string", length=45, nullable=true)
     */
    private $isTakenWithFood;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_taken_as_needed", type="boolean", nullable=true)
     */
    private $isTakenAsNeeded;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_to_complete_course", type="boolean", nullable=true)
     */
    private $isToCompleteCourse;

    /**
     * @var boolean
     *
     * @ORM\Column(name="can_cause_drowsiness", type="boolean", nullable=true)
     */
    private $canCauseDrowsiness;

    /**
     * @var string
     *
     * @ORM\Column(name="special_instructions", type="string", length=500, nullable=true)
     */
    private $specialInstructions;

    /**
     * @var string
     *
     * @ORM\Column(name="origin_price", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $originPrice = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="cost_price_to_clinic", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $costPriceToClinic = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="cost_price", type="decimal", precision=13, scale=2, nullable=false)
     */
    private $costPrice = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="cost_price_gst", type="decimal", precision=13, scale=2, nullable=false)
     */
    private $costPriceGst = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="list_price", type="decimal", precision=13, scale=2, nullable=false)
     */
    private $listPrice = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="list_price_gst", type="decimal", precision=13, scale=2, nullable=false)
     */
    private $listPriceGst = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="doctor_medicine_fee", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $doctorMedicineFee;

    /**
     * @var string
     *
     * @ORM\Column(name="doctor_service_fee", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $doctorServiceFee = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="agent_medicine_fee", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $agentMedicineFee = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="agent_service_fee", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $agentServiceFee = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="agent_3pa_medicine_fee", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $agent3paMedicineFee = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="agent_3pa_service_fee", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $agent3paServiceFee = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="platform_medicine_fee", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $platformMedicineFee = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="platform_service_fee", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $platformServiceFee = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="line_type", type="integer", nullable=false)
     */
    private $lineType;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_drug_stock_ordered", type="integer", nullable=false)
     */
    private $isDrugStockOrdered = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=false)
     */
    private $createdOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_on", type="datetime", nullable=false)
     */
    private $updatedOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted_on", type="datetime", nullable=true)
     */
    private $deletedOn;

    /**
     * @var \Drug
     *
     * @ORM\ManyToOne(targetEntity="Drug")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="drug_id", referencedColumnName="id")
     * })
     */
    private $drug;

    /**
     * @var \Rx
     *
     * @ORM\ManyToOne(targetEntity="Rx")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="rx_id", referencedColumnName="id")
     * })
     */
    private $rx;

    /**
     * @var \RxLineAmendment
     *
     * @ORM\OneToOne(targetEntity="RxLineAmendment", mappedBy="rxLine", cascade={"remove"}, orphanRemoval=true)
     */
    private $rxLineAmendment;


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
     * Set name
     *
     * @param string $name
     * @return RxLine
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
     * @return RxLine
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
     * Set quantity
     *
     * @param integer $quantity
     * @return RxLine
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer 
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set dosageAction
     *
     * @param string $dosageAction
     * @return RxLine
     */
    public function setDosageAction($dosageAction)
    {
        $this->dosageAction = $dosageAction;

        return $this;
    }

    /**
     * Get dosageAction
     *
     * @return string 
     */
    public function getDosageAction()
    {
        return $this->dosageAction;
    }

    /**
     * Set dosageQuantity
     *
     * @param string $dosageQuantity
     * @return RxLine
     */
    public function setDosageQuantity($dosageQuantity)
    {
        $this->dosageQuantity = $dosageQuantity;

        return $this;
    }

    /**
     * Get dosageQuantity
     *
     * @return string 
     */
    public function getDosageQuantity()
    {
        return $this->dosageQuantity;
    }

    /**
     * Set dosageForm
     *
     * @param string $dosageForm
     * @return RxLine
     */
    public function setDosageForm($dosageForm)
    {
        $this->dosageForm = $dosageForm;

        return $this;
    }

    /**
     * Get dosageForm
     *
     * @return string 
     */
    public function getDosageForm()
    {
        return $this->dosageForm;
    }

    /**
     * Set frequencyQuantity
     *
     * @param string $frequencyQuantity
     * @return RxLine
     */
    public function setFrequencyQuantity($frequencyQuantity)
    {
        $this->frequencyQuantity = $frequencyQuantity;

        return $this;
    }

    /**
     * Get frequencyQuantity
     *
     * @return string 
     */
    public function getFrequencyQuantity()
    {
        return $this->frequencyQuantity;
    }

    /**
     * Set frequencyDurationUnit
     *
     * @param string $frequencyDurationUnit
     * @return RxLine
     */
    public function setFrequencyDurationUnit($frequencyDurationUnit)
    {
        $this->frequencyDurationUnit = $frequencyDurationUnit;

        return $this;
    }

    /**
     * Get frequencyDurationUnit
     *
     * @return string 
     */
    public function getFrequencyDurationUnit()
    {
        return $this->frequencyDurationUnit;
    }

    /**
     * Set isTakenWithFood
     *
     * @param string $isTakenWithFood
     * @return RxLine
     */
    public function setIsTakenWithFood($isTakenWithFood)
    {
        $this->isTakenWithFood = $isTakenWithFood;

        return $this;
    }

    /**
     * Get isTakenWithFood
     *
     * @return string 
     */
    public function getIsTakenWithFood()
    {
        return $this->isTakenWithFood;
    }

    /**
     * Set isTakenAsNeeded
     *
     * @param boolean $isTakenAsNeeded
     * @return RxLine
     */
    public function setIsTakenAsNeeded($isTakenAsNeeded)
    {
        $this->isTakenAsNeeded = $isTakenAsNeeded;

        return $this;
    }

    /**
     * Get isTakenAsNeeded
     *
     * @return boolean 
     */
    public function getIsTakenAsNeeded()
    {
        return $this->isTakenAsNeeded;
    }

    /**
     * Set isToCompleteCourse
     *
     * @param boolean $isToCompleteCourse
     * @return RxLine
     */
    public function setIsToCompleteCourse($isToCompleteCourse)
    {
        $this->isToCompleteCourse = $isToCompleteCourse;

        return $this;
    }

    /**
     * Get isToCompleteCourse
     *
     * @return boolean 
     */
    public function getIsToCompleteCourse()
    {
        return $this->isToCompleteCourse;
    }

    /**
     * Set canCauseDrowsiness
     *
     * @param boolean $canCauseDrowsiness
     * @return RxLine
     */
    public function setCanCauseDrowsiness($canCauseDrowsiness)
    {
        $this->canCauseDrowsiness = $canCauseDrowsiness;

        return $this;
    }

    /**
     * Get canCauseDrowsiness
     *
     * @return boolean 
     */
    public function getCanCauseDrowsiness()
    {
        return $this->canCauseDrowsiness;
    }

    /**
     * Set specialInstructions
     *
     * @param string $specialInstructions
     * @return RxLine
     */
    public function setSpecialInstructions($specialInstructions)
    {
        $this->specialInstructions = $specialInstructions;

        return $this;
    }

    /**
     * Get specialInstructions
     *
     * @return string 
     */
    public function getSpecialInstructions()
    {
        return $this->specialInstructions;
    }

    /**
     * Set originPrice
     *
     * @param string $originPrice
     * @return RxLine
     */
    public function setOriginPrice($originPrice)
    {
        $this->originPrice = $originPrice;

        return $this;
    }

    /**
     * Get originPrice
     *
     * @return string 
     */
    public function getOriginPrice()
    {
        return $this->originPrice;
    }

    /**
     * Set costPriceToClinic
     *
     * @param string $costPriceToClinic
     * @return RxLine
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
     * Set costPrice
     *
     * @param string $costPrice
     * @return RxLine
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
     * Set costPriceGst
     *
     * @param string $costPriceGst
     * @return RxLine
     */
    public function setCostPriceGst($costPriceGst)
    {
        $this->costPriceGst = $costPriceGst;

        return $this;
    }

    /**
     * Get costPriceGst
     *
     * @return string 
     */
    public function getCostPriceGst()
    {
        return $this->costPriceGst;
    }

    /**
     * Set listPrice
     *
     * @param string $listPrice
     * @return RxLine
     */
    public function setListPrice($listPrice)
    {
        $this->listPrice = $listPrice;

        return $this;
    }

    /**
     * Get listPrice
     *
     * @return string 
     */
    public function getListPrice()
    {
        return $this->listPrice;
    }

    /**
     * Set listPriceGst
     *
     * @param string $listPriceGst
     * @return RxLine
     */
    public function setListPriceGst($listPriceGst)
    {
        $this->listPriceGst = $listPriceGst;

        return $this;
    }

    /**
     * Get listPriceGst
     *
     * @return string 
     */
    public function getListPriceGst()
    {
        return $this->listPriceGst;
    }

    /**
     * Set doctorMedicineFee
     *
     * @param string $doctorMedicineFee
     * @return RxLine
     */
    public function setDoctorMedicineFee($doctorMedicineFee)
    {
        $this->doctorMedicineFee = $doctorMedicineFee;

        return $this;
    }

    /**
     * Get doctorMedicineFee
     *
     * @return string 
     */
    public function getDoctorMedicineFee()
    {
        return $this->doctorMedicineFee;
    }

    /**
     * Set doctorServiceFee
     *
     * @param string $doctorServiceFee
     * @return RxLine
     */
    public function setDoctorServiceFee($doctorServiceFee)
    {
        $this->doctorServiceFee = $doctorServiceFee;

        return $this;
    }

    /**
     * Get doctorServiceFee
     *
     * @return string 
     */
    public function getDoctorServiceFee()
    {
        return $this->doctorServiceFee;
    }

    /**
     * Set agentMedicineFee
     *
     * @param string $agentMedicineFee
     * @return RxLine
     */
    public function setAgentMedicineFee($agentMedicineFee)
    {
        $this->agentMedicineFee = $agentMedicineFee;

        return $this;
    }

    /**
     * Get agentMedicineFee
     *
     * @return string 
     */
    public function getAgentMedicineFee()
    {
        return $this->agentMedicineFee;
    }

    /**
     * Set agentServiceFee
     *
     * @param string $agentServiceFee
     * @return RxLine
     */
    public function setAgentServiceFee($agentServiceFee)
    {
        $this->agentServiceFee = $agentServiceFee;

        return $this;
    }

    /**
     * Get agentServiceFee
     *
     * @return string 
     */
    public function getAgentServiceFee()
    {
        return $this->agentServiceFee;
    }

    /**
     * Set platformMedicineFee
     *
     * @param string $platformMedicineFee
     * @return RxLine
     */
    public function setPlatformMedicineFee($platformMedicineFee)
    {
        $this->platformMedicineFee = $platformMedicineFee;

        return $this;
    }

    /**
     * Get platformMedicineFee
     *
     * @return string 
     */
    public function getPlatformMedicineFee()
    {
        return $this->platformMedicineFee;
    }

    /**
     * Set platformServiceFee
     *
     * @param string $platformServiceFee
     * @return RxLine
     */
    public function setPlatformServiceFee($platformServiceFee)
    {
        $this->platformServiceFee = $platformServiceFee;

        return $this;
    }

    /**
     * Get platformServiceFee
     *
     * @return string 
     */
    public function getPlatformServiceFee()
    {
        return $this->platformServiceFee;
    }

    /**
     * Set lineType
     *
     * @param boolean $lineType
     * @return RxLine
     */
    public function setLineType($lineType)
    {
        $this->lineType = $lineType;

        return $this;
    }

    /**
     * Get lineType
     *
     * @return boolean 
     */
    public function getLineType()
    {
        return $this->lineType;
    }

    /**
     * Set agent3paServiceFee
     *
     * @param string $agent3paServiceFee
     *
     * @return Rx
     */
    public function setAgent3paServiceFee($agent3paServiceFee)
    {
        $this->agent3paServiceFee = $agent3paServiceFee;

        return $this;
    }

    /**
     * Get agent3paServiceFee
     *
     * @return string
     */
    public function getAgent3paServiceFee()
    {
        return $this->agent3paServiceFee;
    }

    /**
     * Set agent3paMedicineFee
     *
     * @param string $agent3paMedicineFee
     *
     * @return Rx
     */
    public function setAgent3paMedicineFee($agent3paMedicineFee)
    {
        $this->agent3paMedicineFee = $agent3paMedicineFee;

        return $this;
    }

    /**
     * Get agent3paMedicineFee
     *
     * @return string
     */
    public function getAgent3paMedicineFee()
    {
        return $this->agent3paMedicineFee;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return RxLine
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
     * @return RxLine
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
     * @return RxLine
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
     * Set drug
     *
     * @param \UtilBundle\Entity\Drug $drug
     * @return RxLine
     */
    public function setDrug(\UtilBundle\Entity\Drug $drug = null)
    {
        $this->drug = $drug;

        return $this;
    }

    /**
     * Get drug
     *
     * @return \UtilBundle\Entity\Drug 
     */
    public function getDrug()
    {
        return $this->drug;
    }

    /**
     * Set rx
     *
     * @param \UtilBundle\Entity\Rx $rx
     * @return RxLine
     */
    public function setRx(\UtilBundle\Entity\Rx $rx = null)
    {
        $this->rx = $rx;

        return $this;
    }

    /**
     * Get rx
     *
     * @return \UtilBundle\Entity\Rx 
     */
    public function getRx()
    {
        return $this->rx;
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
     * Set isDrugStockOrdered
     *
     * @param integer $isDrugStockOrdered
     *
     * @return RxLine
     */
    public function setIsDrugStockOrdered($isDrugStockOrdered)
    {
        $this->isDrugStockOrdered = $isDrugStockOrdered;

        return $this;
    }

    /**
     * Get isDrugStockOrdered
     *
     * @return integer
     */
    public function getIsDrugStockOrdered()
    {
        return $this->isDrugStockOrdered;
    }

    public function getRxLineAmendment()
    {
        return $this->rxLineAmendment;
    }

    /**
     * Set rxLineAmendment
     *
     * @param \UtilBundle\Entity\RxLineAmendment $rxLineAmendment
     *
     * @return RxLine
     */
    public function setRxLineAmendment(\UtilBundle\Entity\RxLineAmendment $rxLineAmendment = null)
    {
        $this->rxLineAmendment = $rxLineAmendment;

        return $this;
    }
}
