<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PlatformSettings
 *
 * @ORM\Table(name="platform_settings")
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\PlatformSettingsRepository")
 */
class PlatformSettings
{
    /**
     * @var integer
     *
     * @ORM\Column(name="operations_country_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $operationsCountryId;

    /**
     * @var string
     *
     * @ORM\Column(name="currency_code", type="string", length=4, nullable=true)
     */
    private $currencyCode;

    /**
     * @var string
     *
     * @ORM\Column(name="currency_number", type="string", length=3, nullable=true)
     */
    private $currencyNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="country_prefix", type="string", length=4, nullable=true)
     */
    private $countryPrefix;

    /**
     * @var integer
     *
     * @ORM\Column(name="pharmacy_country_id", type="integer", nullable=true)
     */
    private $pharmacyCountryId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="in_production_mode", type="boolean", nullable=true)
     */
    private $inProductionMode;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_gst", type="boolean", nullable=true)
     */
    private $isGst;

    /**
     * @var integer
     *
     * @ORM\Column(name="platform_share_percentages_id", type="integer", nullable=true)
     */
    private $platformSharePercentagesId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_sales_restricted_to_domestic", type="boolean", nullable=true)
     */
    private $isSalesRestrictedToDomestic;

    /**
     * @var string
     *
     * @ORM\Column(name="patient_help_from_name", type="string", length=150, nullable=true)
     */
    private $patientHelpFromName;

    /**
     * @var string
     *
     * @ORM\Column(name="patient_help_email_address", type="string", length=250, nullable=true)
     */
    private $patientHelpEmailAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="gst_no", type="string", length=50, nullable=true)
     */
    private $gstNo;

    /**
     * @var string
     *
     * @ORM\Column(name="website_from_name", type="string", length=150, nullable=true)
     */
    private $websiteFromName;

    /**
     * @var string
     *
     * @ORM\Column(name="website_help_email_address", type="string", length=250, nullable=true)
     */
    private $websiteHelpEmailAddress;

    /**
     * @var float
     *
     * @ORM\Column(name="gst_rate", type="float", precision=10, scale=0, nullable=true)
     */
    private $gstRate;

    /**
     * @var float
     *
     * @ORM\Column(name="new_gst_rate", type="float", precision=10, scale=0, nullable=true)
     */
    private $newGstRate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="gst_rate_affect_date", type="datetime", nullable=true)
     */
    private $gstRateAffectDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="gst_affect_date", type="datetime", nullable=true)
     */
    private $gstAffectDate;

    /**
     * @var float
     *
     * @ORM\Column(name="overseas", type="float", precision=10, scale=0, nullable=true)
     */
    private $overseas;

    /**
     * @var integer
     *
     * @ORM\Column(name="agent_statement_date", type="integer", nullable=true)
     */
    private $agentStatementDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="doctor_statement_date", type="integer", nullable=true)
     */
    private $doctorStatementDate;

    /**
     * @var float
     *
     * @ORM\Column(name="local", type="float", precision=10, scale=0, nullable=true)
     */
    private $local;

    /**
     * @var integer
     *
     * @ORM\Column(name="pharmacy_weekly_po_day", type="integer", nullable=true)
     */
    private $pharmacyWeeklyPoDay;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="pharmacy_target_date", type="datetime", nullable=true)
     */
    private $pharmacyTargetDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="pharmacy_weekly_po_time", type="integer", nullable=true)
     */
    private $pharmacyWeeklyPoTime;

    /**
     * @var integer
     *
     * @ORM\Column(name="delivery_fortnightly_po_day", type="integer", nullable=true)
     */
    private $deliveryFortnightlyPoDay;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="delivery_target_date", type="datetime", nullable=true)
     */
    private $deliveryTargetDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="delivery_fortnightly_po_time", type="integer", nullable=true)
     */
    private $deliveryFortnightlyPoTime;

    /**
     * @var float
     *
     * @ORM\Column(name="buffer_rate", type="float", precision=10, scale=0, nullable=true)
     */
    private $bufferRate;

    /**
     * @var integer
     *
     * @ORM\Column(name="reminder_rx_refill_30", type="integer", nullable=true)
     */
    private $reminderRxRefill30;

    /**
     * @var integer
     *
     * @ORM\Column(name="reminder_rx_refill_60", type="integer", nullable=true)
     */
    private $reminderRxRefill60;

    /**
     * @var \integer
     *
     * @ORM\Column(name="schedule_declaration_time", type="integer", nullable=true)
     */
    private $scheduleDeclarationTime;
    
    /**
     * @var string
     *
     * @ORM\Column(name="min_fee_agent_for_local_rx", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $minFeeAgentForLocalRx;

    /**
     * @var string
     *
     * @ORM\Column(name="min_fee_agent_for_overseas_rx", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $minFeeAgentForOverseasRx;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_on", type="datetime", nullable=true)
     */
    private $updatedOn;



    /**
     * Get operationsCountryId
     *
     * @return integer
     */
    public function getOperationsCountryId()
    {
        return $this->operationsCountryId;
    }

    /**
     * Set currencyCode
     *
     * @param string $currencyCode
     * @return PlatformSettings
     */
    public function setCurrencyCode($currencyCode)
    {
        $this->currencyCode = $currencyCode;

        return $this;
    }

    /**
     * Get currencyCode
     *
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }


    /**
     * Set gstNo
     *
     * @param string $gstNo
     * @return PlatformSettings
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
     * Set currencyNumber
     *
     * @param string $currencyNumber
     * @return PlatformSettings
     */
    public function setCurrencyNumber($currencyNumber)
    {
        $this->currencyNumber = $currencyNumber;

        return $this;
    }

    /**
     * Get currencyNumber
     *
     * @return string
     */
    public function getCurrencyNumber()
    {
        return $this->currencyNumber;
    }

    /**
     * Set countryPrefix
     *
     * @param string $countryPrefix
     * @return PlatformSettings
     */
    public function setCountryPrefix($countryPrefix)
    {
        $this->countryPrefix = $countryPrefix;

        return $this;
    }

    /**
     * Get countryPrefix
     *
     * @return string
     */
    public function getCountryPrefix()
    {
        return $this->countryPrefix;
    }

    /**
     * Set pharmacyCountryId
     *
     * @param integer $pharmacyCountryId
     * @return PlatformSettings
     */
    public function setPharmacyCountryId($pharmacyCountryId)
    {
        $this->pharmacyCountryId = $pharmacyCountryId;

        return $this;
    }

    /**
     * Get pharmacyCountryId
     *
     * @return integer
     */
    public function getPharmacyCountryId()
    {
        return $this->pharmacyCountryId;
    }

    /**
     * Set inProductionMode
     *
     * @param boolean $inProductionMode
     * @return PlatformSettings
     */
    public function setInProductionMode($inProductionMode)
    {
        $this->inProductionMode = $inProductionMode;

        return $this;
    }

    /**
     * Get inProductionMode
     *
     * @return boolean
     */
    public function getInProductionMode()
    {
        return $this->inProductionMode;
    }


    /**
     * Set isGst
     *
     * @param boolean $isGst
     * @return PlatformSettings
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
     * Set platformSharePercentagesId
     *
     * @param integer $platformSharePercentagesId
     * @return PlatformSettings
     */
    public function setPlatformSharePercentagesId($platformSharePercentagesId)
    {
        $this->platformSharePercentagesId = $platformSharePercentagesId;

        return $this;
    }

    /**
     * Get platformSharePercentagesId
     *
     * @return integer
     */
    public function getPlatformSharePercentagesId()
    {
        return $this->platformSharePercentagesId;
    }

    /**
     * Set isSalesRestrictedToDomestic
     *
     * @param boolean $isSalesRestrictedToDomestic
     * @return PlatformSettings
     */
    public function setIsSalesRestrictedToDomestic($isSalesRestrictedToDomestic)
    {
        $this->isSalesRestrictedToDomestic = $isSalesRestrictedToDomestic;

        return $this;
    }

    /**
     * Get isSalesRestrictedToDomestic
     *
     * @return boolean
     */
    public function getIsSalesRestrictedToDomestic()
    {
        return $this->isSalesRestrictedToDomestic;
    }

    /**
     * Set patientHelpFromName
     *
     * @param string $patientHelpFromName
     * @return PlatformSettings
     */
    public function setPatientHelpFromName($patientHelpFromName)
    {
        $this->patientHelpFromName = $patientHelpFromName;

        return $this;
    }

    /**
     * Get patientHelpFromName
     *
     * @return string
     */
    public function getPatientHelpFromName()
    {
        return $this->patientHelpFromName;
    }

    /**
     * Set patientHelpEmailAddress
     *
     * @param string $patientHelpEmailAddress
     * @return PlatformSettings
     */
    public function setPatientHelpEmailAddress($patientHelpEmailAddress)
    {
        $this->patientHelpEmailAddress = $patientHelpEmailAddress;

        return $this;
    }

    /**
     * Get patientHelpEmailAddress
     *
     * @return string
     */
    public function getPatientHelpEmailAddress()
    {
        return $this->patientHelpEmailAddress;
    }

    /**
     * Set websiteFromName
     *
     * @param string $websiteFromName
     * @return PlatformSettings
     */
    public function setWebsiteFromName($websiteFromName)
    {
        $this->websiteFromName = $websiteFromName;

        return $this;
    }

    /**
     * Get websiteFromName
     *
     * @return string
     */
    public function getWebsiteFromName()
    {
        return $this->websiteFromName;
    }

    /**
     * Set websiteHelpEmailAddress
     *
     * @param string $websiteHelpEmailAddress
     * @return PlatformSettings
     */
    public function setWebsiteHelpEmailAddress($websiteHelpEmailAddress)
    {
        $this->websiteHelpEmailAddress = $websiteHelpEmailAddress;

        return $this;
    }

    /**
     * Get websiteHelpEmailAddress
     *
     * @return string
     */
    public function getWebsiteHelpEmailAddress()
    {
        return $this->websiteHelpEmailAddress;
    }

    /**
     * Set gstRate
     *
     * @param float $gstRate
     * @return PlatformSettings
     */
    public function setGstRate($gstRate)
    {
        $this->gstRate = $gstRate;

        return $this;
    }

    /**
     * Get gstRate
     *
     * @return float
     */
    public function getGstRate()
    {
        return $this->gstRate;
    }

    /**
     * Set newGstRate
     *
     * @param float $newGstRate
     * @return PlatformSettings
     */
    public function setNewGstRate($newGstRate)
    {
        $this->newGstRate = $newGstRate;

        return $this;
    }

    /**
     * Get newGstRate
     *
     * @return float
     */
    public function getNewGstRate()
    {
        return $this->newGstRate;
    }

    /**
     * Set gstRateAffectDate
     *
     * @param \DateTime $gstRateAffectDate
     * @return PlatformSettings
     */
    public function setGstRateAffectDate($gstRateAffectDate)
    {
        $this->gstRateAffectDate = $gstRateAffectDate;

        return $this;
    }

    /**
     * Get gstRateAffectDate
     *
     * @return \DateTime
     */
    public function getGstRateAffectDate()
    {
        return $this->gstRateAffectDate;
    }

    /**
     * Set gstAffectDate
     *
     * @param \DateTime $gstRateAffectDate
     * @return PlatformSettings
     */
    public function setGstAffectDate($gstAffectDate)
    {
        $this->gstAffectDate = $gstAffectDate;

        return $this;
    }

    /**
     * Get gstAffectDate
     *
     * @return \DateTime
     */
    public function getGstAffectDate()
    {
        return $this->gstAffectDate;
    }

    /**
     * Set overseas
     *
     * @param float $overseas
     * @return PlatformSettings
     */
    public function setOverseas($overseas)
    {
        $this->overseas = $overseas;

        return $this;
    }

    /**
     * Get overseas
     *
     * @return float
     */
    public function getOverseas()
    {
        return $this->overseas;
    }

    /**
     * Set agentStatementDate
     *
     * @param integer $agentStatementDate
     * @return PlatformSettings
     */
    public function setAgentStatementDate($agentStatementDate)
    {
        $this->agentStatementDate = $agentStatementDate;

        return $this;
    }

    /**
     * Get agentStatementDate
     *
     * @return integer
     */
    public function getAgentStatementDate()
    {
        return $this->agentStatementDate;
    }

    /**
     * Set doctorStatementDate
     *
     * @param integer $doctorStatementDate
     * @return PlatformSettings
     */
    public function setDoctorStatementDate($doctorStatementDate)
    {
        $this->doctorStatementDate = $doctorStatementDate;

        return $this;
    }

    /**
     * Get doctorStatementDate
     *
     * @return integer
     */
    public function getDoctorStatementDate()
    {
        return $this->doctorStatementDate;
    }

    /**
     * Set local
     *
     * @param float $local
     * @return PlatformSettings
     */
    public function setLocal($local)
    {
        $this->local = $local;

        return $this;
    }

    /**
     * Get local
     *
     * @return float
     */
    public function getLocal()
    {
        return $this->local;
    }

    /**
     * Set pharmacyWeeklyPoDay
     *
     * @param integer $pharmacyWeeklyPoDay
     * @return PlatformSettings
     */
    public function setPharmacyWeeklyPoDay($pharmacyWeeklyPoDay)
    {
        $this->pharmacyWeeklyPoDay = $pharmacyWeeklyPoDay;

        return $this;
    }

    /**
     * Get pharmacyWeeklyPoDay
     *
     * @return integer
     */
    public function getPharmacyWeeklyPoDay()
    {
        return $this->pharmacyWeeklyPoDay;
    }

    /**
     * Set pharmacyTargetDate
     *
     * @param \DateTime $pharmacyTargetDate
     * @return PlatformSettings
     */
    public function setPharmacyTargetDate($pharmacyTargetDate)
    {
        $this->pharmacyTargetDate = $pharmacyTargetDate;

        return $this;
    }

    /**
     * Get pharmacyTargetDate
     *
     * @return \DateTime
     */
    public function getPharmacyTargetDate()
    {
        return $this->pharmacyTargetDate;
    }

    /**
     * Set pharmacyWeeklyPoTime
     *
     * @param integer $pharmacyWeeklyPoTime
     * @return PlatformSettings
     */
    public function setPharmacyWeeklyPoTime($pharmacyWeeklyPoTime)
    {
        $this->pharmacyWeeklyPoTime = $pharmacyWeeklyPoTime;

        return $this;
    }

    /**
     * Get pharmacyWeeklyPoTime
     *
     * @return integer
     */
    public function getPharmacyWeeklyPoTime()
    {
        return $this->pharmacyWeeklyPoTime;
    }

    /**
     * Set deliveryFortnightlyPoDay
     *
     * @param integer $deliveryFortnightlyPoDay
     * @return PlatformSettings
     */
    public function setDeliveryFortnightlyPoDay($deliveryFortnightlyPoDay)
    {
        $this->deliveryFortnightlyPoDay = $deliveryFortnightlyPoDay;

        return $this;
    }

    /**
     * Get deliveryFortnightlyPoDay
     *
     * @return integer
     */
    public function getDeliveryFortnightlyPoDay()
    {
        return $this->deliveryFortnightlyPoDay;
    }

    /**
     * Set deliveryTargetDate
     *
     * @param \DateTime $deliveryTargetDate
     * @return PlatformSettings
     */
    public function setDeliveryTargetDate($deliveryTargetDate)
    {
        $this->deliveryTargetDate = $deliveryTargetDate;

        return $this;
    }

    /**
     * Get deliveryTargetDate
     *
     * @return \DateTime
     */
    public function getDeliveryTargetDate()
    {
        return $this->deliveryTargetDate;
    }

    /**
     * Set deliveryFortnightlyPoTime
     *
     * @param integer $deliveryFortnightlyPoTime
     * @return PlatformSettings
     */
    public function setDeliveryFortnightlyPoTime($deliveryFortnightlyPoTime)
    {
        $this->deliveryFortnightlyPoTime = $deliveryFortnightlyPoTime;

        return $this;
    }

    /**
     * Get deliveryFortnightlyPoTime
     *
     * @return integer
     */
    public function getDeliveryFortnightlyPoTime()
    {
        return $this->deliveryFortnightlyPoTime;
    }

    /**
     * Set bufferRate
     *
     * @param float $bufferRate
     * @return PlatformSettings
     */
    public function setBufferRate($bufferRate)
    {
        $this->bufferRate = $bufferRate;

        return $this;
    }

    /**
     * Get bufferRate
     *
     * @return float
     */
    public function getBufferRate()
    {
        return $this->bufferRate;
    }

    /**
     * Set reminderRxRefill30
     *
     * @param integer $reminderRxRefill30
     * @return PlatformSettings
     */
    public function setReminderRxRefill30($reminderRxRefill30)
    {
        $this->reminderRxRefill30 = $reminderRxRefill30;

        return $this;
    }

    /**
     * Get reminderRxRefill30
     *
     * @return integer
     */
    public function getReminderRxRefill30()
    {
        return $this->reminderRxRefill30;
    }

    /**
     * Set reminderRxRefill60
     *
     * @param integer $reminderRxRefill60
     * @return PlatformSettings
     */
    public function setReminderRxRefill60($reminderRxRefill60)
    {
        $this->reminderRxRefill60 = $reminderRxRefill60;

        return $this;
    }

    /**
     * Get reminderRxRefill60
     *
     * @return integer
     */
    public function getReminderRxRefill60()
    {
        return $this->reminderRxRefill60;
    }

    /**
     * Set scheduleDeclarationTime
     * @param integer $scheduleDeclarationTime
     * @return PlatformSettings
     */
    public function setScheduleDeclarationTime($scheduleDeclarationTime)
    {
        $this->scheduleDeclarationTime = $scheduleDeclarationTime;

        return $this;
    }

    /**
     * Get scheduleDeclarationTime
     *
     * @return integer
     */
    public function getScheduleDeclarationTime()
    {
        return $this->scheduleDeclarationTime;
    }

    /**
     * Set updatedOn
     *
     * @param \DateTime $updatedOn
     * @return PlatformSettings
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
     * Set minFeeAgentForLocalRx
     *
     * @param string $minFeeAgentForLocalRx
     *
     * @return PlatformSettings
     */
    public function setMinFeeAgentForLocalRx($minFeeAgentForLocalRx)
    {
        $this->minFeeAgentForLocalRx = $minFeeAgentForLocalRx;

        return $this;
    }

    /**
     * Get minFeeAgentForLocalRx
     *
     * @return string
     */
    public function getMinFeeAgentForLocalRx()
    {
        return $this->minFeeAgentForLocalRx;
    }

    /**
     * Set minFeeAgentForOverseasRx
     *
     * @param string $minFeeAgentForOverseasRx
     *
     * @return PlatformSettings
     */
    public function setMinFeeAgentForOverseasRx($minFeeAgentForOverseasRx)
    {
        $this->minFeeAgentForOverseasRx = $minFeeAgentForOverseasRx;

        return $this;
    }

    /**
     * Get minFeeAgentForOverseasRx
     *
     * @return string
     */
    public function getMinFeeAgentForOverseasRx()
    {
        return $this->minFeeAgentForOverseasRx;
    }
}
