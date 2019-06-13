<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use UtilBundle\Utility\Constant;

/**
 * Rx
 *
 * @ORM\Table(name="rx", uniqueConstraints={@ORM\UniqueConstraint(name="IDX_rx_onumber", columns={"order_number"})}, indexes={@ORM\Index(name="FK_rx_reminder_code", columns={"reminder_code"}), @ORM\Index(name="IDX_rx_created_on", columns={"created_on"}), @ORM\Index(name="IDX_rx_pnumber", columns={"order_physical_number"}), @ORM\Index(name="FK_rx_doctor", columns={"doctor_id"}), @ORM\Index(name="FK_rx_patient", columns={"patient_id"}), @ORM\Index(name="FK_rx_agent", columns={"agent_id"}), @ORM\Index(name="FK_rx_baddress", columns={"billing_address_id"}), @ORM\Index(name="FK_rx_saddress", columns={"shipping_address_id"}), @ORM\Index(name="FK_rx_parent", columns={"parent_rx_id"}), @ORM\Index(name="IDX_rx_status", columns={"status"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\RxRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Rx
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
     * @var float
     *
     * @ORM\Column(name="doctor_medicine_percentage", type="float", precision=10, scale=0, nullable=true)
     */
    private $doctorMedicinePercentage = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="platform_medicine_percentage", type="float", precision=10, scale=0, nullable=true)
     */
    private $platformMedicinePercentage = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="order_number", type="string", length=50, nullable=true)
     */
    private $orderNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="order_physical_number", type="string", length=50, nullable=true)
     */
    private $orderPhysicalNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="order_suffix", type="string", length=10, nullable=false)
     */
    private $orderSuffix;

    /**
     * @var string
     *
     * @ORM\Column(name="order_value", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $orderValue = '0';

    /**
     * @var decimal
     * @ORM\Column(name="tax_import_duty", type="float", precision=10, scale=0, nullable=true)
     */
    private $taxImportDuty;

    /**
     * @var decimal
     * @ORM\Column(name="tax_income", type="float", precision=10, scale=0, nullable=true)
     */
    private $taxIncome;

    /**
     * @var decimal
     * @ORM\Column(name="tax_income_without_tax", type="float", precision=10, scale=0, nullable=true)
     */
    private $taxIncomeWithoutTax;

    /**
     * @var decimal
     * @ORM\Column(name="tax_vat", type="float", precision=10, scale=0, nullable=true)
     */
    private $taxVat;

    /**
     * @var string
     *
     * @ORM\Column(name="receipt_no", type="string", length=10, nullable=true)
     */
    private $receiptNo;

    /**
     * @var string
     *
     * @ORM\Column(name="patient_number", type="string", length=50, nullable=true)
     */
    private $patientNumber;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_care_giver_authorised", type="boolean", nullable=false)
     */
    private $isCareGiverAuthorised = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="has_rx_review_fee", type="boolean", nullable=false)
     */
    private $hasRxReviewFee;

    /**
     * @var string
     *
     * @ORM\Column(name="cif_value", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $cifValue = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="rx_period", type="integer", nullable=true)
     */
    private $rxPeriod;

    /**
     * @var string
     *
     * @ORM\Column(name="shipping_cost", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $shippingCost = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="shipping_list", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $shippingList = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="shipping_list_gst", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $shippingListGst = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="shipping_cost_gst", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $shippingCostGst = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="to_doctor_shipping_gst_code", type="string", length=5, nullable=true)
     */
    private $toDoctorShippingGstCode;

    /**
     * @var string
     *
     * @ORM\Column(name="to_patient_shipping_gst_code", type="string", length=5, nullable=true)
     */
    private $toPatientShippingGstCode;

    /**
     * @var string
     *
     * @ORM\Column(name="fee_gst", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $feeGst = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="gst_rate", type="float", precision=10, scale=0, nullable=true)
     */
    private $gstRate;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_gateway_fee_bank_mdr", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $paymentGatewayFeeBankMdr = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="payment_gateway_fee_bank_mdr_gst_code", type="string", length=5, nullable=true)
     */
    private $paymentGatewayFeeBankMdrGstCode;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_gateway_fee_bank_gst", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $paymentGatewayFeeBankGst = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="payment_gateway_fee_variable", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $paymentGatewayFeeVariable = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="payment_gateway_fee_variable_gst", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $paymentGatewayFeeVariableGst = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="payment_gateway_fee_variable_gst_code", type="string", length=5, nullable=true)
     */
    private $paymentGatewayFeeVariableGstCode;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_gateway_fee_fixed", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $paymentGatewayFeeFixed = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="payment_gateway_fee_fixed_gst", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $paymentGatewayFeeFixedGst = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="payment_gateway_fee_fixed_gst_code", type="string", length=5, nullable=true)
     */
    private $paymentGatewayFeeFixedGstCode;

    /**
     * @var string
     *
     * @ORM\Column(name="doctor_medicine_fee", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $doctorMedicineFee = '0';

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
     * @ORM\Column(name="agent_3pa_service_fee", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $agent3paServiceFee;

    /**
     * @var string
     *
     * @ORM\Column(name="agent_3pa_medicine_fee", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $agent3paMedicineFee;

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
     * @var string
     *
     * @ORM\Column(name="platform_service_fee_gst_code", type="string", length=5, nullable=true)
     */
    private $platformServiceFeeGstCode;

    /**
     * @var string
     *
     * @ORM\Column(name="prescribing_revenue_fee_gst", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $prescribingRevenueFeeGst = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="prescribing_revenue_fee_gst_code", type="string", length=5, nullable=true)
     */
    private $prescribingRevenueFeeGstCode;

    /**
     * @var string
     *
     * @ORM\Column(name="medicine_gross_margin_gst", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $medicineGrossMarginGst = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="medicine_gross_margin_gst_code", type="string", length=5, nullable=true)
     */
    private $medicineGrossMarginGstCode;

    /**
     * @var string
     *
     * @ORM\Column(name="live_consult_revenue_share_gst_code", type="string", length=5, nullable=true)
     */
    private $liveConsultRevenueShareGstCode;

    /**
     * @var string
     *
     * @ORM\Column(name="ig_permit_fee", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $igPermitFee = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="ig_permit_fee_gst", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $igPermitFeeGst = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="ig_permit_fee_gst_code", type="string", length=5, nullable=true)
     */
    private $igPermitFeeGstCode;

    /**
     * @var string
     *
     * @ORM\Column(name="ig_permit_no", type="string", length=20, nullable=true)
     */
    private $igPermitNo;

    /**
     * @var string
     *
     * @ORM\Column(name="ig_permit_fee_by_courier", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $igPermitFeeByCourier = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="custom_tax_by_courier", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $customTaxByCourier = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="customs_tax", type="decimal", precision=13, scale=2, nullable=false)
     */
    private $customsTax = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="customs_tax_gst", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $customsTaxGst = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="customs_tax_gst_code", type="string", length=5, nullable=true)
     */
    private $customsTaxGstCode;

    /**
     * @var string
     *
     * @ORM\Column(name="tax_invoice_no", type="string", length=50, nullable=true)
     */
    private $taxInvoiceNo;

    /**
     * @var string
     *
     * @ORM\Column(name="proforma_invoice_no", type="string", length=30, nullable=true)
     */
    private $proformaInvoiceNo;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status = '1';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="paid_on", type="datetime", nullable=true)
     */
    private $paidOn;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_on_hold", type="integer", nullable=false)
     */
    private $isOnHold = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="redispensing_status", type="integer", nullable=false)
     */
    private $redispensingStatus = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="processing_batch_number", type="string", length=36, nullable=true)
     */
    private $processingBatchNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="processed_by", type="string", length=50, nullable=true)
     */
    private $processedBy;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_gate", type="string", length=50, nullable=true)
     */
    private $paymentGate;

    /**
     * @var string
     *
     * @ORM\Column(name="customs_clearance_platform_fee", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $customsClearancePlatformFee = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="customs_clearance_platform_fee_gst", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $customsClearancePlatformFeeGst = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="customs_clearance_platform_fee_gst_code", type="string", length=5, nullable=true)
     */
    private $customsClearancePlatformFeeGstCode;

    /**
     * @var string
     *
     * @ORM\Column(name="customs_clearance_doctor_fee", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $customsClearanceDoctorFee = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="customs_clearance_doctor_fee_gst", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $customsClearanceDoctorFeeGst = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="customs_clearance_doctor_fee_gst_code", type="string", length=5, nullable=true)
     */
    private $customsClearanceDoctorFeeGstCode;

    /**
     * @var integer
     *
     * @ORM\Column(name="group_id", type="integer", nullable=true)
     */
    private $groupId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=false)
     */
    private $createdOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_by",  type="string", length=255, nullable=true)
     */
    private $createdBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="sent_on", type="datetime", nullable=true)
     */
    private $sentOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="resent_on", type="datetime", nullable=true)
     */
    private $resentOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastest_reminder", type="datetime", nullable=true)
     */
    private $lastestReminder;

    /**
     * @var string
     *
     * @ORM\Column(name="estimated_delivery_timeline", type="string", length=100, nullable=true)
     */
    private $estimatedDeliveryTimeline;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_special_indent", type="integer", nullable=false)
     */
    private $isSpecialIndent = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_billing_shiping_address_same", type="integer", nullable=false)
     */
    private $isBillingShipingAddressSame = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_on", type="datetime", nullable=true)
     */
    private $updatedOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_status_on", type="datetime", nullable=true)
     */
    private $updatedStatusOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted_on", type="datetime", nullable=true)
     */
    private $deletedOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="latest_activity_on", type="datetime", nullable=true)
     */
    private $latestActivityOn;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_cold_chain", type="integer", nullable=true)
     */
    private $isColdChain;

    /**
     * @var string
     *
     * @ORM\Column(name="last_updated_by", type="string", length=50, nullable=true)
     */
    private $lastUpdatedBy;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_scheduled_rx", type="boolean", nullable=true)
     */
    private $isScheduledRx = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="scheduled_send_date", type="date", nullable=true)
     */
    private $scheduledSendDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="scheduled_sent_on", type="datetime", nullable=true)
     */
    private $scheduledSentOn;

    /**
     * @var integer
     *
     * @ORM\Column(name="partner_client_id", type="integer", nullable=true)
     */
    private $partnerClientId;

    /**
     * @var \Address
     *
     * @ORM\ManyToOne(targetEntity="Address", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="billing_address_id", referencedColumnName="id")
     * })
     */
    private $billingAddress;

    /**
     * @var \Doctor
     *
     * @ORM\ManyToOne(targetEntity="Doctor")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="doctor_id", referencedColumnName="id")
     * })
     */
    private $doctor;    

    /**
     * @var \Rx
     *
     * @ORM\ManyToOne(targetEntity="Rx")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parent_rx_id", referencedColumnName="id")
     * })
     */
    private $parentRx;

    /**
     * @var \Agent
     *
     * @ORM\ManyToOne(targetEntity="Agent")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="agent_id", referencedColumnName="id")
     * })
     */
    private $agent;

    /**
     * @var \Agent
     *
     * @ORM\ManyToOne(targetEntity="Agent")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="secondary_agent_id", referencedColumnName="id")
     * })
     */
    private $secondaryAgent;

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
     * @var \RxReminderSetting
     *
     * @ORM\ManyToOne(targetEntity="RxReminderSetting")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="reminder_code", referencedColumnName="reminder_code")
     * })
     */
    private $reminderCode;

    /**
     * @var Site
     *
     * @ORM\ManyToOne(targetEntity="Site")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="site_id", referencedColumnName="id")
     * })
     */
    private $site;

    /**
     * @var \Address
     *
     * @ORM\ManyToOne(targetEntity="Address", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="shipping_address_id", referencedColumnName="id")
     * })
     */
    private $shippingAddress;


    /**
     * @var \Rx
     *
     * @ORM\OneToMany(targetEntity="RxLine", mappedBy="rx", cascade={"persist"}, orphanRemoval=true)
     */
    private $rxLines;

    /**
     * @var \Rx
     *
     * @ORM\OneToMany(targetEntity="Rx", mappedBy="parentRx", cascade={"persist", "remove" })
     */
    private $children;

    /**
     * @var \Rx
     *
     * @ORM\OneToMany(targetEntity="Box",mappedBy="rx", cascade={"persist", "remove" })
     */
    private $boxes;

    /**
     * @var \Rx
     *
     * @ORM\OneToMany(targetEntity="RxStatusLog",mappedBy="rx", cascade={"persist", "remove" })
     */
    private $rxStatusLogs;

    /**
     * @var \Rx
     *
     * @ORM\OneToMany(targetEntity="Issue", mappedBy="rx", cascade={"persist", "remove" })
     */
    private $issues;

     /**
     * @ORM\OneToMany(targetEntity="Resolve", mappedBy="rx", cascade={"persist", "remove" })
     */
    private $resolves;

    /**
     * @ORM\OneToMany(targetEntity="RxCounter", mappedBy="rx", cascade={"persist", "remove" })
     */
    private $rxCounter;
    /**
     * @ORM\OneToMany(targetEntity="RxPaymentLog", mappedBy="rx", cascade={"persist", "remove" })
     */
    private $rxPaymentLogs;

    /**
     * @ORM\OneToMany(targetEntity="RxLog", mappedBy="rx", cascade={"persist", "remove" })
     */
    private $rxDeliveryLogs;

    /**
     * @ORM\OneToMany(targetEntity="Dispensing", mappedBy="rx", cascade={"persist", "remove" })
     */
    private $dispensingLogs;

    /**
     * @var \PatientSignature
     *
     * @ORM\ManyToOne(targetEntity="PatientSignature", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="patient_signature_id", referencedColumnName="id")
     * })
     */
    private $patientSignature;

     /**
     * @ORM\OneToMany(targetEntity="XeroBatchRx", mappedBy="rx", cascade={"persist", "remove" })
     */
    private $batchRxes;

    /**
     * @ORM\OneToMany(targetEntity="MarginShare", mappedBy="rx", cascade={"persist", "remove" })
     */
    private $marginShares;

    /**
     * @ORM\OneToOne(targetEntity="RxNote", mappedBy="rx")
     */
    private $rxNote;


    public function __construct()
    {
        $this->rxLines = new ArrayCollection();
        $this->rxPaymentLogs = new ArrayCollection();
        $this->boxes = new ArrayCollection();
        $this->rxStatusLogs = new ArrayCollection();
        $this->issues = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->resolves = new ArrayCollection();
        $this->rxCounter = new ArrayCollection();
        $this->rxDeliveryLogs = new ArrayCollection();
        $this->dispensingLogs = new ArrayCollection();
        $this->batchRxes = new ArrayCollection();
        $this->marginShares = new ArrayCollection();
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
     * Set doctorMedicinePercentage
     *
     * @param string $doctorMedicinePercentage
     *
     * @return Rx
     */
    public function setDoctorMedicinePercentage($doctorMedicinePercentage)
    {
        $this->doctorMedicinePercentage = $doctorMedicinePercentage;

        return $this;
    }

    /**
     * Get platformMedicinePercentage
     *
     * @return string
     */
    public function getPlatformMedicinePercentage()
    {
        return $this->platformMedicinePercentage;
    }

    /**
     * Set platformMedicinePercentage
     *
     * @param string $platformMedicinePercentage
     *
     * @return Rx
     */
    public function setPlatformMedicinePercentage($platformMedicinePercentage)
    {
        $this->platformMedicinePercentage = $platformMedicinePercentage;

        return $this;
    }

    /**
     * Get doctorMedicinePercentage
     *
     * @return string
     */
    public function getDoctorMedicinePercentage()
    {
        return $this->doctorMedicinePercentage;
    }

    /**
     * Set orderNumber
     *
     * @param string $orderNumber
     *
     * @return Rx
     */
    public function setOrderNumber($orderNumber)
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }

    /**
     * Get orderNumber
     *
     * @return string
     */
    public function getOrderNumber()
    {
        return $this->orderNumber;
    }

    /**
     * Set orderPhysicalNumber
     *
     * @param string $orderPhysicalNumber
     *
     * @return Rx
     */
    public function setOrderPhysicalNumber($orderPhysicalNumber)
    {
        $this->orderPhysicalNumber = $orderPhysicalNumber;

        return $this;
    }

    /**
     * Get orderPhysicalNumber
     *
     * @return string
     */
    public function getOrderPhysicalNumber()
    {
        return $this->orderPhysicalNumber;
    }

    /**
     * Set orderSuffix
     *
     * @param string $orderSuffix
     *
     * @return Rx
     */
    public function setOrderSuffix($orderSuffix)
    {
        $this->orderSuffix = $orderSuffix;

        return $this;
    }

    /**
     * Get orderSuffix
     *
     * @return string
     */
    public function getOrderSuffix()
    {
        return $this->orderSuffix;
    }

    /**
     * Get taxImportDuty
     *
     * @return string
     */
    public function getTaxImportDuty()
    {
        return $this->taxImportDuty;
    }
    /**
     * Set taxImportDuty
     *
     * @param string
     *
     * @return Rx
     */
    public function setTaxImportDuty($taxImportDuty)
    {
        $this->taxImportDuty = $taxImportDuty;

        return $this;
    }


    /**
     * Get taxIncomeWithoutTax
     *
     * @return string
     */
    public function getTaxIncomeWithoutTax()
    {
        return $this->taxIncomeWithoutTax;
    }

    /**
     * Set taxIncome
     *
     * @param string
     *
     * @return Rx
     */
    public function setTaxIncomeWithoutTax($taxIncomeWithoutTax)
    {
        $this->taxIncomeWithoutTax = $taxIncomeWithoutTax;

        return $this;
    }

    /**
     * Get taxIncome
     *
     * @return string
     */
    public function getTaxIncome()
    {
        return $this->taxIncome;
    }

    /**
     * Set taxIncome
     *
     * @param string
     *
     * @return Rx
     */
    public function setTaxIncome($taxIncome)
    {
        $this->taxIncome = $taxIncome;

        return $this;
    }

    /**
     * Get taxVat
     *
     * @return string
     */
    public function getTaxVat()
    {
        return $this->taxVat;
    }

    /**
     * Set taxVat
     *
     * @param string
     *
     * @return Rx
     */
    public function setTaxVat($taxVat)
    {
        $this->taxVat = $taxVat;

        return $this;
    }



    /**
     * Set orderValue
     *
     * @param string $orderValue
     *
     * @return Rx
     */
    public function setOrderValue($orderValue)
    {
        $this->orderValue = $orderValue;

        return $this;
    }

    /**
     * Get orderValue
     *
     * @return string
     */
    public function getOrderValue()
    {
        return $this->orderValue;
    }

    /**
     * Set receiptNo
     *
     * @param string $receiptNo
     *
     * @return Rx
     */
    public function setReceiptNo($receiptNo)
    {
        $this->receiptNo = $receiptNo;

        return $this;
    }

    /**
     * Get receiptNo
     *
     * @return string
     */
    public function getReceiptNo()
    {
        return $this->receiptNo;
    }

    /**
     * Set patientNumber
     *
     * @param string $patientNumber
     *
     * @return Rx
     */
    public function setPatientNumber($patientNumber)
    {
        $this->patientNumber = $patientNumber;

        return $this;
    }

    /**
     * Get patientNumber
     *
     * @return string
     */
    public function getPatientNumber()
    {
        return $this->patientNumber;
    }

    /**
     * Set isCareGiverAuthorised
     *
     * @param boolean $isCareGiverAuthorised
     *
     * @return Rx
     */
    public function setIsCareGiverAuthorised($isCareGiverAuthorised)
    {
        $this->isCareGiverAuthorised = $isCareGiverAuthorised;

        return $this;
    }

    /**
     * Get isCareGiverAuthorised
     *
     * @return boolean
     */
    public function getIsCareGiverAuthorised()
    {
        return $this->isCareGiverAuthorised;
    }

    /**
     * Set hasRxReviewFee
     *
     * @param boolean $hasRxReviewFee
     *
     * @return Rx
     */
    public function setHasRxReviewFee($hasRxReviewFee)
    {
        $this->hasRxReviewFee = $hasRxReviewFee;

        return $this;
    }

    /**
     * Get hasRxReviewFee
     *
     * @return boolean
     */
    public function getHasRxReviewFee()
    {
        return $this->hasRxReviewFee;
    }

    /**
     * Set cifValue
     *
     * @param string $cifValue
     *
     * @return Rx
     */
    public function setCifValue($cifValue)
    {
        $this->cifValue = $cifValue;

        return $this;
    }

    /**
     * Get cifValue
     *
     * @return string
     */
    public function getCifValue()
    {
        return $this->cifValue;
    }

    /**
     * Set rxPeriod
     *
     * @param integer $rxPeriod
     *
     * @return Rx
     */
    public function setRxPeriod($rxPeriod)
    {
        $this->rxPeriod = $rxPeriod;

        return $this;
    }

    /**
     * Get rxPeriod
     *
     * @return integer
     */
    public function getRxPeriod()
    {
        return $this->rxPeriod;
    }

    /**
     * Set shippingCost
     *
     * @param string $shippingCost
     *
     * @return Rx
     */
    public function setShippingCost($shippingCost)
    {
        $this->shippingCost = $shippingCost;

        return $this;
    }

    /**
     * Get shippingCost
     *
     * @return string
     */
    public function getShippingCost()
    {
        return $this->shippingCost;
    }

    /**
     * Set shippingList
     *
     * @param string $shippingList
     *
     * @return Rx
     */
    public function setShippingList($shippingList)
    {
        $this->shippingList = $shippingList;

        return $this;
    }

    /**
     * Get shippingList
     *
     * @return string
     */
    public function getShippingList()
    {
        return $this->shippingList;
    }

    /**
     * Set shippingListGst
     *
     * @param string $shippingListGst
     *
     * @return Rx
     */
    public function setShippingListGst($shippingListGst)
    {
        $this->shippingListGst = $shippingListGst;

        return $this;
    }

    /**
     * Get shippingListGst
     *
     * @return string
     */
    public function getShippingListGst()
    {
        return $this->shippingListGst;
    }

    /**
     * Set shippingCostGst
     *
     * @param string $shippingCostGst
     *
     * @return Rx
     */
    public function setShippingCostGst($shippingCostGst)
    {
        $this->shippingCostGst = $shippingCostGst;

        return $this;
    }

    /**
     * Get shippingCostGst
     *
     * @return string
     */
    public function getShippingCostGst()
    {
        return $this->shippingCostGst;
    }

    /**
     * Set toDoctorShippingGstCode
     *
     * @param string $toDoctorShippingGstCode
     *
     * @return Rx
     */
    public function setToDoctorShippingGstCode($toDoctorShippingGstCode)
    {
        $this->toDoctorShippingGstCode = $toDoctorShippingGstCode;

        return $this;
    }

    /**
     * Get toDoctorShippingGstCode
     *
     * @return string
     */
    public function getToDoctorShippingGstCode()
    {
        return $this->toDoctorShippingGstCode;
    }

    /**
     * Set toPatientShippingGstCode
     *
     * @param string $toPatientShippingGstCode
     *
     * @return Rx
     */
    public function setToPatientShippingGstCode($toPatientShippingGstCode)
    {
        $this->toPatientShippingGstCode = $toPatientShippingGstCode;

        return $this;
    }

    /**
     * Get toPatientShippingGstCode
     *
     * @return string
     */
    public function getToPatientShippingGstCode()
    {
        return $this->toPatientShippingGstCode;
    }

    /**
     * Set feeGst
     *
     * @param string $feeGst
     *
     * @return Rx
     */
    public function setFeeGst($feeGst)
    {
        $this->feeGst = $feeGst;

        return $this;
    }

    /**
     * Get feeGst
     *
     * @return string
     */
    public function getFeeGst()
    {
        return $this->feeGst;
    }

    /**
     * Set gstRate
     *
     * @param float $gstRate
     * @return Rx
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
     * Set paymentGatewayFeeBankMdr
     *
     * @param string $paymentGatewayFeeBankMdr
     *
     * @return Rx
     */
    public function setPaymentGatewayFeeBankMdr($paymentGatewayFeeBankMdr)
    {
        $this->paymentGatewayFeeBankMdr = $paymentGatewayFeeBankMdr;

        return $this;
    }

    /**
     * Get paymentGatewayFeeBankMdr
     *
     * @return string
     */
    public function getPaymentGatewayFeeBankMdr()
    {
        return $this->paymentGatewayFeeBankMdr;
    }

    /**
     * Set paymentGatewayFeeBankMdrGstCode
     *
     * @param string $paymentGatewayFeeBankMdrGstCode
     *
     * @return Rx
     */
    public function setPaymentGatewayFeeBankMdrGstCode($paymentGatewayFeeBankMdrGstCode)
    {
        $this->paymentGatewayFeeBankMdrGstCode = $paymentGatewayFeeBankMdrGstCode;

        return $this;
    }

    /**
     * Get paymentGatewayFeeBankMdrGstCode
     *
     * @return string
     */
    public function getPaymentGatewayFeeBankMdrGstCode()
    {
        return $this->paymentGatewayFeeBankMdrGstCode;
    }

    /**
     * Set paymentGate
     *
     * @param string $paymentGate
     *
     * @return Rx
     */
    public function setPaymentGate($paymentGate)
    {
        $this->paymentGate = $paymentGate;

        return $this;
    }

    /**
     * Get paymentGate
     *
     * @return string
     */
    public function getPaymentGate()
    {
        return $this->paymentGate;
    }

    /**
     * Set paymentGatewayFeeBankGst
     *
     * @param string $paymentGatewayFeeBankGst
     *
     * @return Rx
     */
    public function setPaymentGatewayFeeBankGst($paymentGatewayFeeBankGst)
    {
        $this->paymentGatewayFeeBankGst = $paymentGatewayFeeBankGst;

        return $this;
    }

    /**
     * Get paymentGatewayFeeBankGst
     *
     * @return string
     */
    public function getPaymentGatewayFeeBankGst()
    {
        return $this->paymentGatewayFeeBankGst;
    }

    /**
     * Set paymentGatewayFeeVariable
     *
     * @param string $paymentGatewayFeeVariable
     *
     * @return Rx
     */
    public function setPaymentGatewayFeeVariable($paymentGatewayFeeVariable)
    {
        $this->paymentGatewayFeeVariable = $paymentGatewayFeeVariable;

        return $this;
    }

    /**
     * Get paymentGatewayFeeVariable
     *
     * @return string
     */
    public function getPaymentGatewayFeeVariable()
    {
        return $this->paymentGatewayFeeVariable;
    }

    /**
     * Set paymentGatewayFeeVariableGst
     *
     * @param string $paymentGatewayFeeVariableGst
     *
     * @return Rx
     */
    public function setPaymentGatewayFeeVariableGst($paymentGatewayFeeVariableGst)
    {
        $this->paymentGatewayFeeVariableGst = $paymentGatewayFeeVariableGst;

        return $this;
    }

    /**
     * Get paymentGatewayFeeVariableGst
     *
     * @return string
     */
    public function getPaymentGatewayFeeVariableGst()
    {
        return $this->paymentGatewayFeeVariableGst;
    }

    /**
     * Set paymentGatewayFeeVariableGstCode
     *
     * @param string $paymentGatewayFeeVariableGstCode
     *
     * @return Rx
     */
    public function setPaymentGatewayFeeVariableGstCode($paymentGatewayFeeVariableGstCode)
    {
        $this->paymentGatewayFeeVariableGstCode = $paymentGatewayFeeVariableGstCode;

        return $this;
    }

    /**
     * Get paymentGatewayFeeVariableGstCode
     *
     * @return string
     */
    public function getPaymentGatewayFeeVariableGstCode()
    {
        return $this->paymentGatewayFeeVariableGstCode;
    }

    /**
     * Set paymentGatewayFeeFixed
     *
     * @param string $paymentGatewayFeeFixed
     *
     * @return Rx
     */
    public function setPaymentGatewayFeeFixed($paymentGatewayFeeFixed)
    {
        $this->paymentGatewayFeeFixed = $paymentGatewayFeeFixed;

        return $this;
    }

    /**
     * Get paymentGatewayFeeFixed
     *
     * @return string
     */
    public function getPaymentGatewayFeeFixed()
    {
        return $this->paymentGatewayFeeFixed;
    }

    /**
     * Set paymentGatewayFeeFixedGst
     *
     * @param string $paymentGatewayFeeFixedGst
     *
     * @return Rx
     */
    public function setPaymentGatewayFeeFixedGst($paymentGatewayFeeFixedGst)
    {
        $this->paymentGatewayFeeFixedGst = $paymentGatewayFeeFixedGst;

        return $this;
    }

    /**
     * Get paymentGatewayFeeFixedGst
     *
     * @return string
     */
    public function getPaymentGatewayFeeFixedGst()
    {
        return $this->paymentGatewayFeeFixedGst;
    }

    /**
     * Set paymentGatewayFeeFixedGstCode
     *
     * @param string $paymentGatewayFeeFixedGstCode
     *
     * @return Rx
     */
    public function setPaymentGatewayFeeFixedGstCode($paymentGatewayFeeFixedGstCode)
    {
        $this->paymentGatewayFeeFixedGstCode = $paymentGatewayFeeFixedGstCode;

        return $this;
    }

    /**
     * Get paymentGatewayFeeFixedGstCode
     *
     * @return string
     */
    public function getPaymentGatewayFeeFixedGstCode()
    {
        return $this->paymentGatewayFeeFixedGstCode;
    }

    /**
     * Set doctorMedicineFee
     *
     * @param string $doctorMedicineFee
     *
     * @return Rx
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
     *
     * @return Rx
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
     *
     * @return Rx
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
     *
     * @return Rx
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
     *
     * @return Rx
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
     *
     * @return Rx
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
     * Set platformServiceFeeGstCode
     *
     * @param string $platformServiceFeeGstCode
     *
     * @return Rx
     */
    public function setPlatformServiceFeeGstCode($platformServiceFeeGstCode)
    {
        $this->platformServiceFeeGstCode = $platformServiceFeeGstCode;

        return $this;
    }

    /**
     * Get platformServiceFeeGstCode
     *
     * @return string
     */
    public function getPlatformServiceFeeGstCode()
    {
        return $this->platformServiceFeeGstCode;
    }

    /**
     * Set prescribingRevenueFeeGst
     *
     * @param string $prescribingRevenueFeeGst
     *
     * @return Rx
     */
    public function setPrescribingRevenueFeeGst($prescribingRevenueFeeGst)
    {
        $this->prescribingRevenueFeeGst = $prescribingRevenueFeeGst;

        return $this;
    }

    /**
     * Get prescribingRevenueFeeGst
     *
     * @return string
     */
    public function getPrescribingRevenueFeeGst()
    {
        return $this->prescribingRevenueFeeGst;
    }

    /**
     * Set prescribingRevenueFeeGstCode
     *
     * @param string $prescribingRevenueFeeGstCode
     *
     * @return Rx
     */
    public function setPrescribingRevenueFeeGstCode($prescribingRevenueFeeGstCode)
    {
        $this->prescribingRevenueFeeGstCode = $prescribingRevenueFeeGstCode;

        return $this;
    }

    /**
     * Get prescribingRevenueFeeGstCode
     *
     * @return string
     */
    public function getPrescribingRevenueFeeGstCode()
    {
        return $this->prescribingRevenueFeeGstCode;
    }

    /**
     * Set medicineGrossMarginGst
     *
     * @param string $medicineGrossMarginGst
     *
     * @return Rx
     */
    public function setMedicineGrossMarginGst($medicineGrossMarginGst)
    {
        $this->medicineGrossMarginGst = $medicineGrossMarginGst;

        return $this;
    }

    /**
     * Get medicineGrossMarginGst
     *
     * @return string
     */
    public function getMedicineGrossMarginGst()
    {
        return $this->medicineGrossMarginGst;
    }

    /**
     * Set medicineGrossMarginGstCode
     *
     * @param string $medicineGrossMarginGstCode
     *
     * @return Rx
     */
    public function setMedicineGrossMarginGstCode($medicineGrossMarginGstCode)
    {
        $this->medicineGrossMarginGstCode = $medicineGrossMarginGstCode;

        return $this;
    }

    /**
     * Get medicineGrossMarginGstCode
     *
     * @return string
     */
    public function getMedicineGrossMarginGstCode()
    {
        return $this->medicineGrossMarginGstCode;
    }

    /**
     * Set liveConsultRevenueShareGstCode
     *
     * @param string $liveConsultRevenueShareGstCode
     *
     * @return Rx
     */
    public function setLiveConsultRevenueShareGstCode($liveConsultRevenueShareGstCode)
    {
        $this->liveConsultRevenueShareGstCode = $liveConsultRevenueShareGstCode;

        return $this;
    }

    /**
     * Get liveConsultRevenueShareGstCode
     *
     * @return string
     */
    public function getLiveConsultRevenueShareGstCode()
    {
        return $this->liveConsultRevenueShareGstCode;
    }

    /**
     * Set igPermitFee
     *
     * @param string $igPermitFee
     *
     * @return Rx
     */
    public function setIgPermitFee($igPermitFee)
    {
        $this->igPermitFee = $igPermitFee;

        return $this;
    }

    /**
     * Get igPermitFee
     *
     * @return string
     */
    public function getIgPermitFee()
    {
        return $this->igPermitFee;
    }

    /**
     * Set igPermitFeeGst
     *
     * @param string $igPermitFeeGst
     *
     * @return Rx
     */
    public function setIgPermitFeeGst($igPermitFeeGst)
    {
        $this->igPermitFeeGst = $igPermitFeeGst;

        return $this;
    }

    /**
     * Get igPermitFeeGst
     *
     * @return string
     */
    public function getIgPermitFeeGst()
    {
        return $this->igPermitFeeGst;
    }

    /**
     * Set igPermitFeeGstCode
     *
     * @param string $igPermitFeeGstCode
     *
     * @return Rx
     */
    public function setIgPermitFeeGstCode($igPermitFeeGstCode)
    {
        $this->igPermitFeeGstCode = $igPermitFeeGstCode;

        return $this;
    }

    /**
     * Get igPermitFeeGstCode
     *
     * @return string
     */
    public function getIgPermitFeeGstCode()
    {
        return $this->igPermitFeeGstCode;
    }

    /**
     * Set igPermitNo
     *
     * @param string $igPermitNo
     *
     * @return Rx
     */
    public function setIgPermitNo($igPermitNo)
    {
        $this->igPermitNo = $igPermitNo;

        return $this;
    }

    /**
     * Get igPermitNo
     *
     * @return string
     */
    public function getIgPermitNo()
    {
        return $this->igPermitNo;
    }

    /**
     * Set igPermitFeeByCourier
     *
     * @param string $igPermitFeeByCourier
     *
     * @return Rx
     */
    public function setIgPermitFeeByCourier($igPermitFeeByCourier)
    {
        $this->igPermitFeeByCourier = $igPermitFeeByCourier;

        return $this;
    }

    /**
     * Get igPermitFeeByCourier
     *
     * @return string
     */
    public function getIgPermitFeeByCourier()
    {
        return $this->igPermitFeeByCourier;
    }

    /**
     * Set customTaxByCourier
     *
     * @param string $customTaxByCourier
     *
     * @return Rx
     */
    public function setCustomTaxByCourier($customTaxByCourier)
    {
        $this->customTaxByCourier = $customTaxByCourier;

        return $this;
    }

    /**
     * Get customTaxByCourier
     *
     * @return string
     */
    public function getCustomTaxByCourier()
    {
        return $this->customTaxByCourier;
    }

    /**
     * Set customsTax
     *
     * @param string $customsTax
     *
     * @return Rx
     */
    public function setCustomsTax($customsTax)
    {
        $this->customsTax = $customsTax;

        return $this;
    }

    /**
     * Get customsTax
     *
     * @return string
     */
    public function getCustomsTax()
    {
        return $this->customsTax;
    }

    /**
     * Set customsTaxGst
     *
     * @param string $customsTaxGst
     *
     * @return Rx
     */
    public function setCustomsTaxGst($customsTaxGst)
    {
        $this->customsTaxGst = $customsTaxGst;

        return $this;
    }

    /**
     * Get customsTaxGst
     *
     * @return string
     */
    public function getCustomsTaxGst()
    {
        return $this->customsTaxGst;
    }

    /**
     * Set customsTaxGstCode
     *
     * @param string $customsTaxGstCode
     *
     * @return Rx
     */
    public function setCustomsTaxGstCode($customsTaxGstCode)
    {
        $this->customsTaxGstCode = $customsTaxGstCode;

        return $this;
    }

    /**
     * Get customsTaxGstCode
     *
     * @return string
     */
    public function getCustomsTaxGstCode()
    {
        return $this->customsTaxGstCode;
    }

    /**
     * Set taxInvoiceNo
     *
     * @param string $taxInvoiceNo
     *
     * @return Rx
     */
    public function setTaxInvoiceNo($taxInvoiceNo)
    {
        $this->taxInvoiceNo = $taxInvoiceNo;

        return $this;
    }

    /**
     * Get taxInvoiceNo
     *
     * @return string
     */
    public function getTaxInvoiceNo()
    {
        return $this->taxInvoiceNo;
    }

    /**
     * Set proformaInvoiceNo
     *
     * @param string $proformaInvoiceNo
     *
     * @return Rx
     */
    public function setProformaInvoiceNo($proformaInvoiceNo)
    {
        $this->proformaInvoiceNo = $proformaInvoiceNo;

        return $this;
    }

    /**
     * Get proformaInvoiceNo
     *
     * @return string
     */
    public function getProformaInvoiceNo()
    {
        return $this->proformaInvoiceNo;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Rx
     */
    public function setStatus($status)
    {
        $this->status = $status;

        $this->setUpdatedStatusOn(new \DateTime("now"));

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set paidOn
     *
     * @param \DateTime $paidOn
     *
     * @return Rx
     */
    public function setPaidOn($paidOn)
    {
        $this->paidOn = $paidOn;

        return $this;
    }

    /**
     * Get paidOn
     *
     * @return \DateTime
     */
    public function getPaidOn()
    {
        return $this->paidOn;
    }

    /**
     * Set isOnHold
     *
     * @param integer $isOnHold
     *
     * @return Rx
     */
    public function setIsOnHold($isOnHold)
    {
        $this->isOnHold = $isOnHold;

        return $this;
    }

    /**
     * Get isOnHold
     *
     * @return integer
     */
    public function getIsOnHold()
    {
        return $this->isOnHold;
    }

    /**
     * Set processingBatchNumber
     *
     * @param string $processingBatchNumber
     *
     * @return Rx
     */
    public function setProcessingBatchNumber($processingBatchNumber)
    {
        $this->processingBatchNumber = $processingBatchNumber;

        return $this;
    }

    /**
     * Get processingBatchNumber
     *
     * @return string
     */
    public function getProcessingBatchNumber()
    {
        return $this->processingBatchNumber;
    }

    /**
     * Set processedBy
     *
     * @param string $processedBy
     *
     * @return Rx
     */
    public function setProcessedBy($processedBy)
    {
        $this->processedBy = $processedBy;

        return $this;
    }

    /**
     * Get processedBy
     *
     * @return string
     */
    public function getProcessedBy()
    {
        return $this->processedBy;
    }

    /**
     * Set customsClearancePlatformFee
     *
     * @param string $customsClearancePlatformFee
     *
     * @return Rx
     */
    public function setCustomsClearancePlatformFee($customsClearancePlatformFee)
    {
        $this->customsClearancePlatformFee = $customsClearancePlatformFee;

        return $this;
    }

    /**
     * Get customsClearancePlatformFee
     *
     * @return string
     */
    public function getCustomsClearancePlatformFee()
    {
        return $this->customsClearancePlatformFee;
    }

    /**
     * Set customsClearancePlatformFeeGst
     *
     * @param string $customsClearancePlatformFeeGst
     *
     * @return Rx
     */
    public function setCustomsClearancePlatformFeeGst($customsClearancePlatformFeeGst)
    {
        $this->customsClearancePlatformFeeGst = $customsClearancePlatformFeeGst;

        return $this;
    }

    /**
     * Get customsClearancePlatformFeeGst
     *
     * @return string
     */
    public function getCustomsClearancePlatformFeeGst()
    {
        return $this->customsClearancePlatformFeeGst;
    }

    /**
     * Set customsClearancePlatformFeeGstCode
     *
     * @param string $customsClearancePlatformFeeGstCode
     *
     * @return Rx
     */
    public function setCustomsClearancePlatformFeeGstCode($customsClearancePlatformFeeGstCode)
    {
        $this->customsClearancePlatformFeeGstCode = $customsClearancePlatformFeeGstCode;

        return $this;
    }

    /**
     * Get customsClearancePlatformFeeGstCode
     *
     * @return string
     */
    public function getCustomsClearancePlatformFeeGstCode()
    {
        return $this->customsClearancePlatformFeeGstCode;
    }

    /**
     * Set customsClearanceDoctorFee
     *
     * @param string $customsClearanceDoctorFee
     *
     * @return Rx
     */
    public function setCustomsClearanceDoctorFee($customsClearanceDoctorFee)
    {
        $this->customsClearanceDoctorFee = $customsClearanceDoctorFee;

        return $this;
    }

    /**
     * Get customsClearanceDoctorFee
     *
     * @return string
     */
    public function getCustomsClearanceDoctorFee()
    {
        return $this->customsClearanceDoctorFee;
    }

    /**
     * Set customsClearanceDoctorFeeGst
     *
     * @param string $customsClearanceDoctorFeeGst
     *
     * @return Rx
     */
    public function setCustomsClearanceDoctorFeeGst($customsClearanceDoctorFeeGst)
    {
        $this->customsClearanceDoctorFeeGst = $customsClearanceDoctorFeeGst;

        return $this;
    }

    /**
     * Get customsClearanceDoctorFeeGst
     *
     * @return string
     */
    public function getCustomsClearanceDoctorFeeGst()
    {
        return $this->customsClearanceDoctorFeeGst;
    }

    /**
     * Set customsClearanceDoctorFeeGstCode
     *
     * @param string $customsClearanceDoctorFeeGstCode
     *
     * @return Rx
     */
    public function setCustomsClearanceDoctorFeeGstCode($customsClearanceDoctorFeeGstCode)
    {
        $this->customsClearanceDoctorFeeGstCode = $customsClearanceDoctorFeeGstCode;

        return $this;
    }

    /**
     * Get customsClearanceDoctorFeeGstCode
     *
     * @return string
     */
    public function getCustomsClearanceDoctorFeeGstCode()
    {
        return $this->customsClearanceDoctorFeeGstCode;
    }

    /**
     * Set groupId
     *
     * @param integer $groupId
     *
     * @return Rx
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;

        return $this;
    }

    /**
     * Get groupId
     *
     * @return integer
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return Rx
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
     * Set sentOn
     *
     * @param \DateTime $sentOn
     *
     * @return Rx
     */
    public function setSentOn($sentOn)
    {
        $this->sentOn = $sentOn;

        return $this;
    }

    /**
     * Get sentOn
     *
     * @return \DateTime
     */
    public function getSentOn()
    {
        return $this->sentOn;
    }

    /**
     * Set resentOn
     *
     * @param \DateTime $resentOn
     *
     * @return Rx
     */
    public function setResentOn($resentOn)
    {
        $this->resentOn = $resentOn;

        return $this;
    }

    /**
     * Get resentOn
     *
     * @return \DateTime
     */
    public function getResentOn()
    {
        return $this->resentOn;
    }

    /**
     * Set lastestReminder
     *
     * @param \DateTime $lastestReminder
     *
     * @return Rx
     */
    public function setLastestReminder($lastestReminder)
    {
        $this->lastestReminder = $lastestReminder;

        return $this;
    }

    /**
     * Get lastestReminder
     *
     * @return \DateTime
     */
    public function getLastestReminder()
    {
        return $this->lastestReminder;
    }

    /**
     * Set estimatedDeliveryTimeline
     *
     * @param string $estimatedDeliveryTimeline
     *
     * @return Rx
     */
    public function setEstimatedDeliveryTimeline($estimatedDeliveryTimeline)
    {
        $this->estimatedDeliveryTimeline = $estimatedDeliveryTimeline;

        return $this;
    }

    /**
     * Get estimatedDeliveryTimeline
     *
     * @return string
     */
    public function getEstimatedDeliveryTimeline()
    {
        return $this->estimatedDeliveryTimeline;
    }

    /**
     * Set updatedOn
     *
     * @param \DateTime $updatedOn
     *
     * @return Rx
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
     * Set updatedStatusOn
     *
     * @param \DateTime $updatedStatusOn
     *
     * @return Rx
     */
    public function setUpdatedStatusOn($updatedStatusOn)
    {
        $this->updatedStatusOn = $updatedStatusOn;

        return $this;
    }

    /**
     * Get updatedStatusOn
     *
     * @return \DateTime
     */
    public function getUpdatedStatusOn()
    {
        return $this->updatedStatusOn;
    }

    /**
     * Set deletedOn
     *
     * @param \DateTime $deletedOn
     *
     * @return Rx
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
     * Set latestActivityOn
     *
     * @param \DateTime $latestActivityOn
     *
     * @return Rx
     */
    public function setLatestActivityOn($latestActivityOn)
    {
        $this->latestActivityOn = $latestActivityOn;

        return $this;
    }

    /**
     * Get latestActivityOn
     *
     * @return \DateTime
     */
    public function getLatestActivityOn()
    {
        return $this->latestActivityOn;
    }

    /**
     * Set lastUpdatedBy
     *
     * @param string $lastUpdatedBy
     *
     * @return Rx
     */
    public function setLastUpdatedBy($lastUpdatedBy)
    {
        $this->lastUpdatedBy = $lastUpdatedBy;

        return $this;
    }

    /**
     * Get lastUpdatedBy
     *
     * @return string
     */
    public function getLastUpdatedBy()
    {
        return $this->lastUpdatedBy;
    }

    /**
     * Set partnerClientId
     *
     * @param string $partnerClientId
     *
     * @return Rx
     */
    public function setPartnerClientId($partnerClientId)
    {
        $this->partnerClientId = $partnerClientId;

        return $this;
    }

    /**
     * Get partnerClientId
     *
     * @return integer
     */
    public function getPartnerClientId()
    {
        return $this->partnerClientId;
    }

    /**
     * Set billingAddress
     *
     * @param \UtilBundle\Entity\Address $billingAddress
     *
     * @return Rx
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
     * Set doctor
     *
     * @param \UtilBundle\Entity\Doctor $doctor
     *
     * @return Rx
     */
    public function setDoctor(\UtilBundle\Entity\Doctor $doctor = null)
    {
        $this->doctor = $doctor;

        return $this;
    }

    /**
     * Get doctor
     *
     * @return \UtilBundle\Entity\Doctor
     */
    public function getDoctor()
    {
        return $this->doctor;
    }
    
    /**
     * Set agent
     *
     * @param \UtilBundle\Entity\Agent $agent
     *
     * @return Rx
     */
    public function setAgent(\UtilBundle\Entity\Agent $agent = null)
    {
        $this->agent = $agent;

        return $this;
    }

    /**
     * Get agent
     *
     * @return \UtilBundle\Entity\Agent
     */
    public function getAgent()
    {
        return $this->agent;
    }

    /**
     * Set parentRx
     *
     * @param \UtilBundle\Entity\Rx $parentRx
     *
     * @return Rx
     */
    public function setParentRx(\UtilBundle\Entity\Rx $parentRx = null)
    {
        $this->parentRx = $parentRx;

        return $this;
    }

    /**
     * Get parentRx
     *
     * @return \UtilBundle\Entity\Rx
     */
    public function getParentRx()
    {
        return $this->parentRx;
    }

    /**
     * Set patient
     *
     * @param \UtilBundle\Entity\Patient $patient
     *
     * @return Rx
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
     * Set secondary agent
     *
     * @param \UtilBundle\Entity\Agent $agent
     *
     * @return Rx
     */
    public function setSecondaryAgent(\UtilBundle\Entity\Agent $agent = null)
    {
        $this->secondaryAgent = $agent;

        return $this;
    }

    /**
     * Get secondary agent
     *
     * @return \UtilBundle\Entity\Agent
     */
    public function getSecondaryAgent()
    {
        return $this->secondaryAgent;
    }

    /**
     * Set reminderCode
     *
     * @param \UtilBundle\Entity\RxReminderSetting $reminderCode
     *
     * @return Rx
     */
    public function setReminderCode(\UtilBundle\Entity\RxReminderSetting $reminderCode = null)
    {
        $this->reminderCode = $reminderCode;

        return $this;
    }

    /**
     * Get reminderCode
     *
     * @return \UtilBundle\Entity\RxReminderSetting
     */
    public function getReminderCode()
    {
        return $this->reminderCode;
    }

    /**
     * Set site
     *
     * @param \UtilBundle\Entity\Site $site
     *
     * @return Rx
     */
    public function setSite(\UtilBundle\Entity\Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get site
     *
     * @return \UtilBundle\Entity\Site
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Set shippingAddress
     *
     * @param \UtilBundle\Entity\Address $shippingAddress
     *
     * @return Rx
     */
    public function setShippingAddress(\UtilBundle\Entity\Address $shippingAddress = null)
    {
        $this->shippingAddress = $shippingAddress;

        return $this;
    }

    /**
     * Get shippingAddress
     *
     * @return \UtilBundle\Entity\Address
     */
    public function getShippingAddress()
    {
        return $this->shippingAddress;
    }

    /**
     * Add RxLine
     * @param \UtilBundle\Entity\RxLine $rxLine
     * @return Pharmacy
     */
    public function addRxLine(\UtilBundle\Entity\RxLine $rxLine)
    {
        $rxLine->setRx($this);
        $this->rxLines[] = $rxLine;

        return $this;
    }

    /**
     * Get Drugs
     * @return ArrayCollection
     */
    public function getRxLines()
    {
        return $this->rxLines;
    }

    /**
     * Remove rxLines
     *
     * @param \UtilBundle\Entity\RxLine $rxLines
     */
    public function removeRxLine(\UtilBundle\Entity\RxLine $rxLines)
    {
        $this->rxLines->removeElement($rxLines);
    }

    /**
     * Add box
     *
     * @param \UtilBundle\Entity\RxLine $box
     *
     * @return Rx
     */
    public function addBox(\UtilBundle\Entity\RxLine $box)
    {
        $this->boxes[] = $box;

        return $this;
    }

    /**
     * Remove box
     *
     * @param \UtilBundle\Entity\RxLine $box
     */
    public function removeBox(\UtilBundle\Entity\RxLine $box)
    {
        $this->boxes->removeElement($box);
    }

    /**
     * Get boxes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBoxes()
    {
        return $this->boxes;
    }

    /**
     * Add rxStatusLog
     *
     * @param \UtilBundle\Entity\RxStatusLog $rxStatusLog
     *
     * @return Rx
     */
    public function addRxStatusLog(\UtilBundle\Entity\RxStatusLog $rxStatusLog)
    {
        $rxStatusLog->setRx($this);
        $this->rxStatusLogs[] = $rxStatusLog;

        return $this;
    }

    /**
     * Remove rxStatusLog
     *
     * @param \UtilBundle\Entity\RxStatusLog $rxStatusLog
     */
    public function removeRxStatusLog(\UtilBundle\Entity\RxStatusLog $rxStatusLog)
    {
        $this->rxStatusLogs->removeElement($rxStatusLog);
    }

    /**
     * Get rxStatusLogs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRxStatusLogs()
    {
        return $this->rxStatusLogs;
    }

    /**
     * Add issue
     *
     * @param \UtilBundle\Entity\Issue $issue
     *
     * @return Rx
     */
    public function addIssue(\UtilBundle\Entity\Issue $issue)
    {
        $issue->setRx($this);
        $this->issues[] = $issue;

        return $this;
    }

    /**
     * Remove issue
     *
     * @param \UtilBundle\Entity\Issue $issue
     */
    public function removeIssue(\UtilBundle\Entity\Issue $issue)
    {
        $this->issues->removeElement($issue);
    }

    /**
     * Get issues
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getValidIssues()
    {
        $result = new ArrayCollection();
        $iss = $this->issues;
        foreach ($iss as $obj) {
            if($obj->getStatus() == Constant::ISSUE_STATUS_ACTIVE) {
                $result[] = $obj;
            }
        }
        return $result;
    }

    /**
     * Add resolf
     *
     * @param \UtilBundle\Entity\Resolve $resolf
     *
     * @return Rx
     */
    public function addResolf(\UtilBundle\Entity\Resolve $resolf)
    {
        $resolf->setRx($this);
        $this->resolves[] = $resolf;

        return $this;
    }

    /**
     * Remove resolf
     *
     * @param \UtilBundle\Entity\Resolve $resolf
     */
    public function removeResolf(\UtilBundle\Entity\Resolve $resolf)
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
     * Add rxCounter
     *
     * @param \UtilBundle\Entity\RxCounter $rxCounter
     *
     * @return Rx
     */
    public function addRxCounter(\UtilBundle\Entity\RxCounter $rxCounter)
    {
        $rxCounter->setRx($this);
        $this->rxCounter[] = $rxCounter;

        return $this;
    }

    /**
     * Remove rxCounter
     *
     * @param \UtilBundle\Entity\RxCounter $rxCounter
     */
    public function removeRxCounter(\UtilBundle\Entity\RxCounter $rxCounter)
    {
        $this->rxCounter->removeElement($rxCounter);
    }

    /**
     * Get rxCounter
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRxCounter()
    {
        return $this->rxCounter;
    }

    /**
     * Add rxRefundLog
     *
     * @param \UtilBundle\Entity\RxPaymentLog $rxRefundLog
     *
     * @return Rx
     */
    public function addRxRefundLog(\UtilBundle\Entity\RxPaymentLog $rxRefundLog)
    {
        $rxRefundLog->setRx($this);
        $this->rxRefundLogs[] = $rxRefundLog;

        return $this;
    }

    /**
     * Remove rxRefundLog
     *
     * @param \UtilBundle\Entity\RxPaymentLog $rxRefundLog
     */
    public function removeRxRefundLog(\UtilBundle\Entity\RxPaymentLog $rxRefundLog)
    {
        $this->rxRefundLogs->removeElement($rxRefundLog);
    }

    /**
     * Get rxRefundLogs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRxRefundLogs()
    {
        return $this->rxRefundLogs;
    }

    /**
     * Get issues
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIssues()
    {
        return $this->issues;
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
     * Add rxDeliveryLog
     *
     * @param \UtilBundle\Entity\RxLog $rxDeliveryLog
     *
     * @return Rx
     */
    public function addRxDeliveryLog(\UtilBundle\Entity\RxLog $rxDeliveryLog)
    {
        $rxDeliveryLog->setRx($this);
        $this->rxDeliveryLogs[] = $rxDeliveryLog;

        return $this;
    }

    /**
     * Remove rxDeliveryLog
     *
     * @param \UtilBundle\Entity\RxLog $rxDeliveryLog
     */
    public function removeRxDeliveryLog(\UtilBundle\Entity\RxLog $rxDeliveryLog)
    {
        $this->rxDeliveryLogs->removeElement($rxDeliveryLog);
    }

    /**
     * Get rxDeliveryLogs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRxDeliveryLogs()
    {
        return $this->rxDeliveryLogs;
    }

    /**
     * Add rxPaymentLog
     *
     * @param \UtilBundle\Entity\RxPaymentLog $rxPaymentLog
     *
     * @return Rx
     */
    public function addRxPaymentLog(\UtilBundle\Entity\RxPaymentLog $rxPaymentLog)
    {
        $rxPaymentLog->setRx($this);
        $this->rxPaymentLogs[] = $rxPaymentLog;

        return $this;
    }

    /**
     * Remove rxPaymentLog
     *
     * @param \UtilBundle\Entity\RxPaymentLog $rxPaymentLog
     */
    public function removeRxPaymentLog(\UtilBundle\Entity\RxPaymentLog $rxPaymentLog)
    {
        $this->rxPaymentLogs->removeElement($rxPaymentLog);
    }

    /**
     * Get rxPaymentLogs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRxPaymentLogs()
    {
        return $this->rxPaymentLogs;
    }

    /**
     * Set isSpecialIndent
     *
     * @param integer $isSpecialIndent
     *
     * @return Rx
     */
    public function setIsSpecialIndent($isSpecialIndent)
    {
        $this->isSpecialIndent = $isSpecialIndent;

    }
    
    /*
     * Set patientSignature
     *
     * @param \UtilBundle\Entity\PatientSignature $patientSignature
     *
     * @return Rx
     */
    public function setPatientSignature(\UtilBundle\Entity\PatientSignature $patientSignature = null)
    {
        $this->patientSignature = $patientSignature;
        return $this;
    }

    /**
     * Get isSpecialIndent
     *
     * @return integer
     */
    public function getIsSpecialIndent()
    {
        return $this->isSpecialIndent;

    }

    /*
     * Get patientSignature
     *
     * @return \UtilBundle\Entity\PatientSignature
     */
    public function getPatientSignature()
    {
        return $this->patientSignature;
    }

    /**
     * Set isBillingShipingAddressSame
     *
     * @param integer $isBillingShipingAddressSame
     *
     * @return Rx
     */
    public function setIsBillingShipingAddressSame($isBillingShipingAddressSame)
    {
        $this->isBillingShipingAddressSame = $isBillingShipingAddressSame;

        return $this;
    }

    /**
     * Get isBillingShipingAddressSame
     *
     * @return integer
     */
    public function getIsBillingShipingAddressSame()
    {
        return $this->isBillingShipingAddressSame;
    }

    /**
     * Add dispensingLog
     *
     * @param \UtilBundle\Entity\Dispensing $dispensingLog
     *
     * @return Rx
     */
    public function addDispensingLog(\UtilBundle\Entity\Dispensing $dispensingLog)
    {
        $dispensingLog->setRx($this);
        $this->dispensingLogs[] = $dispensingLog;

        return $this;
    }

    /**
     * Remove dispensingLog
     *
     * @param \UtilBundle\Entity\Dispensing $dispensingLog
     */
    public function removeDispensingLog(\UtilBundle\Entity\Dispensing $dispensingLog)
    {
        $this->dispensingLogs->removeElement($dispensingLog);
    }

    /**
     * Get dispensingLogs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDispensingLogs()
    {
        return $this->dispensingLogs;
    }

    /**
     * Add child
     *
     * @param \UtilBundle\Entity\Rx $child
     *
     * @return Rx
     */
    public function addChild(\UtilBundle\Entity\Rx $child)
    {
        $child->setParentRx($this);
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \UtilBundle\Entity\Rx $child
     */
    public function removeChild(\UtilBundle\Entity\Rx $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set redispensingStatus
     *
     * @param integer $redispensingStatus
     *
     * @return Rx
     */
    public function setRedispensingStatus($redispensingStatus)
    {
        $this->redispensingStatus = $redispensingStatus;

        return $this;
    }

    /**
     * Get redispensingStatus
     *
     * @return integer
     */
    public function getRedispensingStatus()
    {
        return $this->redispensingStatus;
    }

    /**
     * Set isColdChain
     *
     * @param integer $isColdChain
     *
     * @return Rx
     */
    public function setIsColdChain($isColdChain)
    {
        $this->isColdChain = $isColdChain;

        return $this;
    }

    /**
     * Get isColdChain
     *
     * @return integer
     */
    public function getIsColdChain()
    {
        return $this->isColdChain;
    }

    /**
     * Set isScheduledRx
     *
     * @param boolean $isScheduledRx
     *
     * @return Rx
     */
    public function setIsScheduledRx($isScheduledRx)
    {
        $this->isScheduledRx = $isScheduledRx;

        return $this;
    }

    /**
     * Get isScheduledRx
     *
     * @return boolean
     */
    public function getIsScheduledRx()
    {
        return $this->isScheduledRx;
    }

    /**
     * Set scheduledSendDate
     *
     * @param \DateTime $scheduledSendDate
     *
     * @return Rx
     */
    public function setScheduledSendDate($scheduledSendDate)
    {
        $this->scheduledSendDate = $scheduledSendDate;

        return $this;
    }

    /**
     * Get scheduledSendDate
     *
     * @return \DateTime
     */
    public function getScheduledSendDate()
    {
        return $this->scheduledSendDate;
    }

    /**
     * Set scheduledSentOn
     *
     * @param \DateTime $scheduledSentOn
     *
     * @return Rx
     */
    public function setScheduledSentOn($scheduledSentOn)
    {
        $this->scheduledSentOn = $scheduledSentOn;

        return $this;
    }

    /**
     * Get scheduledSentOn
     *
     * @return \DateTime
     */
    public function getScheduledSentOn()
    {
        return $this->scheduledSentOn;
    }

    public function isColdChain()
    {
        $rxLines = $this->getRxLines();
        foreach ($rxLines as $value) {
            $drug = $value->getDrug();
            if (empty($drug)) {
                continue;
            }

            $isColdChain = $drug->getIsColdChain();
            if ($isColdChain) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add batchRx
     *
     * @param \UtilBundle\Entity\XeroBatchRx $batchRx
     *
     * @return Rx
     */
    public function addBatchRx(\UtilBundle\Entity\XeroBatchRx $batchRx)
    {
        $this->batchRxes[] = $batchRx;

        return $this;
    }

    /**
     * Remove batchRx
     *
     * @param \UtilBundle\Entity\XeroBatchRx $batchRx
     */
    public function removeBatchRx(\UtilBundle\Entity\XeroBatchRx $batchRx)
    {
        $this->batchRxes->removeElement($batchRx);
    }

    /**
     * Get batchRxes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBatchRxes()
    {
        return $this->batchRxes;
    }

    /**
     * Add marginShare
     *
     * @param \UtilBundle\Entity\MarginShare $marginShare
     *
     * @return Rx
     */
    public function addMarginShare(\UtilBundle\Entity\MarginShare $marginShare)
    {
        $this->marginShares[] = $marginShare;

        return $this;
    }

    /**
     * Remove marginShare
     *
     * @param \UtilBundle\Entity\MarginShare $marginShare
     */
    public function removeMarginShare(\UtilBundle\Entity\MarginShare $marginShare)
    {
        $this->marginShares->removeElement($marginShare);
    }

    /**
     * Get marginShares
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMarginShares()
    {
        return $this->marginShares;
    }

    public function getRxNote()
    {
        return $this->rxNote;
    }

    /**
     * Set rxNote
     *
     * @param \UtilBundle\Entity\RxNote $rxNote
     *
     * @return Rx
     */
    public function setRxNote(\UtilBundle\Entity\RxNote $rxNote = null)
    {
        $this->rxNote = $rxNote;

        return $this;
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
     * Set createdBy
     *
     * @param string $createdBy
     *
     * @return Rx
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return string
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }
}
