<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TransactionListing
 *
 * @ORM\Table(name="transaction_listing", uniqueConstraints={@ORM\UniqueConstraint(name="NewIndex2", columns={"order_id"}), @ORM\UniqueConstraint(name="NewIndex1", columns={"rx_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\TransactionListingRepository")
 * @ORM\HasLifecycleCallbacks
 */
class TransactionListing
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
     * @var boolean
     *
     * @ORM\Column(name="location", type="integer", nullable=true)
     */
    private $location;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_patient_pay", type="date", nullable=true)
     */
    private $datePatientPay;

    /**
     * @var string
     *
     * @ORM\Column(name="order_id", type="string", length=20, nullable=true)
     */
    private $orderId;

    /**
     * @var integer
     *
     * @ORM\Column(name="status_from_customer_service", type="integer", nullable=true)
     */
    private $statusFromCustomerService;

    /**
     * @var string
     *
     * @ORM\Column(name="reference_no", type="string", length=50, nullable=true)
     */
    private $referenceNo;

    /**
     * @var string
     *
     * @ORM\Column(name="doctor_name", type="string", length=250, nullable=true)
     */
    private $doctorName;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_clinic_gst_registered", type="boolean", nullable=true)
     */
    private $isClinicGstRegistered;

    /**
     * @var string
     *
     * @ORM\Column(name="patient_name", type="string", length=250, nullable=true)
     */
    private $patientName;

    /**
     * @var string
     *
     * @ORM\Column(name="sale_general_medicine", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $saleGeneralMedicine;

    /**
     * @var string
     *
     * @ORM\Column(name="sale_special_medicine", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $saleSpecialMedicine;

    /**
     * @var string
     *
     * @ORM\Column(name="doctor_fees_consult", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $doctorFeesConsult;

    /**
     * @var string
     *
     * @ORM\Column(name="doctor_fees_review_rx", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $doctorFeesReviewRx;

    /**
     * @var string
     *
     * @ORM\Column(name="shipping_and_handling", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $shippingAndHandling;

    /**
     * @var string
     *
     * @ORM\Column(name="import_tax_indo_overseas", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $importTaxIndoOverseas;

    /**
     * @var string
     *
     * @ORM\Column(name="import_tax_sg_overseas", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $importTaxSgOverseas;

    /**
     * @var string
     *
     * @ORM\Column(name="custom_clearance_admin_fee_indo_overseas", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $customClearanceAdminFeeIndoOverseas;

    /**
     * @var string
     *
     * @ORM\Column(name="custom_clearance_admin_fee_sg_overseas", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $customClearanceAdminFeeSgOverseas;

    /**
     * @var string
     *
     * @ORM\Column(name="custom_ig_permit_fee_sg", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $customIgPermitFeeSg;

    /**
     * @var string
     *
     * @ORM\Column(name="patient_gst", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $patientGst;

    /**
     * @var string
     *
     * @ORM\Column(name="total_prescription_advice", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $totalPrescriptionAdvice;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_gateway", type="string", length=50, nullable=true)
     */
    private $paymentGateway;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="settlement_date_by_payment_gateway", type="date", nullable=true)
     */
    private $settlementDateByPaymentGateway;

    /**
     * @var string
     *
     * @ORM\Column(name="amount_to_be_paid_by_payment_gateway", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $amountToBePaidByPaymentGateway;

    /**
     * @var string
     *
     * @ORM\Column(name="amount_paid_by_payment_gateway", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $amountPaidByPaymentGateway;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_gateway_fees_mdr_cc", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $paymentGatewayFeesMdrCc;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_gateway_fees_mdr_myclear", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $paymentGatewayFeesMdrMyclear;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_gateway_fees_gst", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $paymentGatewayFeesGst;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_gateway_invoice_pgw", type="string", length=20, nullable=true)
     */
    private $paymentGatewayInvoicePgw;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_of_invoice", type="date", nullable=true)
     */
    private $dateOfInvoice;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_gateway_pending_to_pay_gmedes", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $paymentGatewayPendingToPayGmedes;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_gateway_company", type="string", length=250, nullable=true)
     */
    private $paymentGatewayCompany;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ipay_settlement_date_by_payment_gateway", type="date", nullable=true)
     */
    private $ipaySettlementDateByPaymentGateway;

    /**
     * @var string
     *
     * @ORM\Column(name="ipay_amount_to_be_paid_by_payment_gateway", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $ipayAmountToBePaidByPaymentGateway;

    /**
     * @var string
     *
     * @ORM\Column(name="ipay_amount_paid_by_payment_gateway", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $ipayAmountPaidByPaymentGateway;

    /**
     * @var string
     *
     * @ORM\Column(name="ipay_payment_gateway_fees_mdr_cc", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $ipayPaymentGatewayFeesMdrCc;

    /**
     * @var string
     *
     * @ORM\Column(name="ipay_payment_gateway_fees_mdr_myclear", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $ipayPaymentGatewayFeesMdrMyclear;

    /**
     * @var string
     *
     * @ORM\Column(name="ipay_payment_gateway_fees_gst", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $ipayPaymentGatewayFeesGst;

    /**
     * @var string
     *
     * @ORM\Column(name="ipay_payment_gateway_invoice_pgw", type="string", length=20, nullable=true)
     */
    private $ipayPaymentGatewayInvoicePgw;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ipay_date_of_invoice", type="date", nullable=true)
     */
    private $ipayDateOfInvoice;

    /**
     * @var string
     *
     * @ORM\Column(name="ipay_payment_gateway_pending_to_pay_gmedes", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $ipayPaymentGatewayPendingToPayGmedes;

    /**
     * @var string
     *
     * @ORM\Column(name="general_medicine_cost_price", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $generalMedicineCostPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="special_medicine_cost_price", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $specialMedicineCostPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="total_product_cost_price", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $totalProductCostPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="gst_amount_for_medicine", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $gstAmountForMedicine;

    /**
     * @var string
     *
     * @ORM\Column(name="total_margin_general_medicine", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $totalMarginGeneralMedicine;

    /**
     * @var string
     *
     * @ORM\Column(name="total_margin_special_medicine", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $totalMarginSpecialMedicine;

    /**
     * @var string
     *
     * @ORM\Column(name="total_margin_medicine", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $totalMarginMedicine;

    /**
     * @var string
     *
     * @ORM\Column(name="doctor_general_medicine", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $doctorGeneralMedicine;

    /**
     * @var string
     *
     * @ORM\Column(name="doctor_special_medicine", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $doctorSpecialMedicine;

    /**
     * @var string
     *
     * @ORM\Column(name="doctors_fees_margin_on_medicine_general_gmedes", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $doctorsFeesMarginOnMedicineGeneralGmedes;

    /**
     * @var string
     *
     * @ORM\Column(name="doctors_fees_margin_on_medicine_special_gmedes", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $doctorsFeesMarginOnMedicineSpecialGmedes;

    /**
     * @var string
     *
     * @ORM\Column(name="doctor_consult", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $doctorConsult;

    /**
     * @var string
     *
     * @ORM\Column(name="doctor_review_rx", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $doctorReviewRx;

    /**
     * @var string
     *
     * @ORM\Column(name="doctor_shipping_and_handling", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $doctorShippingAndHandling;

    /**
     * @var string
     *
     * @ORM\Column(name="doctor_import_tax_indo_overseas", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $doctorImportTaxIndoOverseas;

    /**
     * @var string
     *
     * @ORM\Column(name="doctor_import_tax_sg_overseas", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $doctorImportTaxSgOverseas;

    /**
     * @var string
     *
     * @ORM\Column(name="doctor_fees_custom_clearance_admin_fee_indo_overseas", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $doctorFeesCustomClearanceAdminFeeIndoOverseas;

    /**
     * @var string
     *
     * @ORM\Column(name="doctor_fees_custom_clearance_admin_fee_sg_overseas", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $doctorFeesCustomClearanceAdminFeeSgOverseas;

    /**
     * @var string
     *
     * @ORM\Column(name="doctor_custom_ig_permit_fee_sg", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $doctorCustomIgPermitFeeSg;

    /**
     * @var string
     *
     * @ORM\Column(name="doctor_payment_gateway_fees_mdr_cc", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $doctorPaymentGatewayFeesMdrCc;

    /**
     * @var string
     *
     * @ORM\Column(name="doctor_payment_gateway_fees_mdr_myclear", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $doctorPaymentGatewayFeesMdrMyclear;

    /**
     * @var string
     *
     * @ORM\Column(name="doctor_payment_gateway_fees_gst", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $doctorPaymentGatewayFeesGst;

    /**
     * @var string
     *
     * @ORM\Column(name="ipay_doctor_payment_gateway_fees_mdr_cc", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $ipayDoctorPaymentGatewayFeesMdrCc;

    /**
     * @var string
     *
     * @ORM\Column(name="ipay_doctor_payment_gateway_fees_mdr_myclear", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $ipayDoctorPaymentGatewayFeesMdrMyclear;

    /**
     * @var string
     *
     * @ORM\Column(name="ipay_doctor_payment_gateway_fees_gst", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $ipayDoctorPaymentGatewayFeesGst;

    /**
     * @var string
     *
     * @ORM\Column(name="doctor_medicine_cost", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $doctorMedicineCost;

    /**
     * @var string
     *
     * @ORM\Column(name="doctor_gst_on_medicine", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $doctorGstOnMedicine;

    /**
     * @var string
     *
     * @ORM\Column(name="doctor_net_amount_to_be_paid", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $doctorNetAmountToBePaid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="doctor_date_of_statement", type="date", nullable=true)
     */
    private $doctorDateOfStatement;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="doctoc_date_of_invoice_gmedes_to_doctor", type="date", nullable=true)
     */
    private $doctocDateOfInvoiceGmedesToDoctor;

    /**
     * @var string
     *
     * @ORM\Column(name="doctor_invoice_number_from_gmedes", type="string", length=50, nullable=true)
     */
    private $doctorInvoiceNumberFromGmedes;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="doctor_date_of_payment", type="date", nullable=true)
     */
    private $doctorDateOfPayment;

    /**
     * @var string
     *
     * @ORM\Column(name="doctor_total_amount_paid", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $doctorTotalAmountPaid;

    /**
     * @var string
     *
     * @ORM\Column(name="doctor_pending_to_pay", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $doctorPendingToPay;

    /**
     * @var string
     *
     * @ORM\Column(name="agent_name", type="string", length=250, nullable=true)
     */
    private $agentName;

    /**
     * @var string
     *
     * @ORM\Column(name="agent_margin_share_general_medicine", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $agentMarginShareGeneralMedicine;

    /**
     * @var string
     *
     * @ORM\Column(name="agent_margin_share_special_medicine", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $agentMarginShareSpecialMedicine;

    /**
     * @var string
     *
     * @ORM\Column(name="agent_revenue_share_doctor_fees_consult", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $agentRevenueShareDoctorFeesConsult;

    /**
     * @var string
     *
     * @ORM\Column(name="agent_revenue_share_doctor_fees_review_rx", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $agentRevenueShareDoctorFeesReviewRx;

    /**
     * @var string
     *
     * @ORM\Column(name="agent_fees_total_before_gst", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $agentFeesTotalBeforeGst;

    /**
     * @var string
     *
     * @ORM\Column(name="agent_gst_amount_on_agent_fees", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $agentGstAmountOnAgentFees;

    /**
     * @var string
     *
     * @ORM\Column(name="agent_fees_after_gst", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $agentFeesAfterGst;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="agent_date_of_agent_statement", type="date", nullable=true)
     */
    private $agentDateOfAgentStatement;

    /**
     * @var string
     *
     * @ORM\Column(name="agent_invoice_number", type="string", length=50, nullable=true)
     */
    private $agentInvoiceNumber;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="agent_invoice_date", type="date", nullable=true)
     */
    private $agentInvoiceDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="agent_date_of_payment", type="date", nullable=true)
     */
    private $agentDateOfPayment;

    /**
     * @var string
     *
     * @ORM\Column(name="agent_total_amount_paid", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $agentTotalAmountPaid;

    /**
     * @var string
     *
     * @ORM\Column(name="agent_pending_to_pay", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $agentPendingToPay;

    /**
     * @var string
     *
     * @ORM\Column(name="gmedes_margin_share_general_medicine", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $gmedesMarginShareGeneralMedicine;

    /**
     * @var string
     *
     * @ORM\Column(name="gmedes_margin_share_special_medicine", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $gmedesMarginShareSpecialMedicine;

    /**
     * @var string
     *
     * @ORM\Column(name="gmedes_revenue_share_doctor_fees_consult", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $gmedesRevenueShareDoctorFeesConsult;

    /**
     * @var string
     *
     * @ORM\Column(name="gmedes_revenue_share_doctor_fees_review_rx", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $gmedesRevenueShareDoctorFeesReviewRx;

    /**
     * @var string
     *
     * @ORM\Column(name="gmedes_revenue_share_overseas", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $gmedesRevenueShareOverseas;

    /**
     * @var string
     *
     * @ORM\Column(name="gmedes_total_earnings", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $gmedesTotalEarnings;

    /**
     * @var string
     *
     * @ORM\Column(name="pharmacy_cost_of_sales_general_medicine", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $pharmacyCostOfSalesGeneralMedicine;

    /**
     * @var string
     *
     * @ORM\Column(name="pharmacy_cost_of_sales_special_medicine", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $pharmacyCostOfSalesSpecialMedicine;

    /**
     * @var string
     *
     * @ORM\Column(name="pharmacy_gst_amount", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $pharmacyGstAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="pharmacy_payment_gateway_fees_mdr_cc", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $pharmacyPaymentGatewayFeesMdrCc;

    /**
     * @var string
     *
     * @ORM\Column(name="pharmacy_payment_gateway_fees_mdr_myclear", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $pharmacyPaymentGatewayFeesMdrMyclear;

    /**
     * @var string
     *
     * @ORM\Column(name="pharmacy_payment_gateway_fees_gst", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $pharmacyPaymentGatewayFeesGst;

    /**
     * @var string
     *
     * @ORM\Column(name="ipay_pharmacy_payment_gateway_fees_mdr_cc", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $ipayPharmacyPaymentGatewayFeesMdrCc;

    /**
     * @var string
     *
     * @ORM\Column(name="ipay_pharmacy_payment_gateway_fees_mdr_myclear", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $ipayPharmacyPaymentGatewayFeesMdrMyclear;

    /**
     * @var string
     *
     * @ORM\Column(name="ipay_pharmacy_payment_gateway_fees_gst", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $ipayPharmacyPaymentGatewayFeesGst;

    /**
     * @var string
     *
     * @ORM\Column(name="pharmacy_shipping_and_handling", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $pharmacyShippingAndHandling;

    /**
     * @var string
     *
     * @ORM\Column(name="pharmacy_gst_on_shipping", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $pharmacyGstOnShipping;

    /**
     * @var string
     *
     * @ORM\Column(name="pharmacy_import_tax_indo_overseas", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $pharmacyImportTaxIndoOverseas;

    /**
     * @var string
     *
     * @ORM\Column(name="pharmacy_import_tax_sg_overseas", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $pharmacyImportTaxSgOverseas;

    /**
     * @var string
     *
     * @ORM\Column(name="pharmacy_fees_custom_clearance_admin_fee_indo_overseas", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $pharmacyFeesCustomClearanceAdminFeeIndoOverseas;

    /**
     * @var string
     *
     * @ORM\Column(name="pharmacy_fees_custom_clearance_admin_fee_sg_overseas", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $pharmacyFeesCustomClearanceAdminFeeSgOverseas;

    /**
     * @var string
     *
     * @ORM\Column(name="pharmacy_custom_ig_permit_fee_sg", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $pharmacyCustomIgPermitFeeSg;

    /**
     * @var string
     *
     * @ORM\Column(name="pharmacy_total_amount_payable", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $pharmacyTotalAmountPayable;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="pharmacy_daily_po_date", type="date", nullable=true)
     */
    private $pharmacyDailyPoDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="pharmacy_weekly_po_date", type="date", nullable=true)
     */
    private $pharmacyWeeklyPoDate;

    /**
     * @var string
     *
     * @ORM\Column(name="pharmacy_weekly_tax_invoice_number", type="string", length=50, nullable=true)
     */
    private $pharmacyWeeklyTaxInvoiceNumber;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="pharmacy_weekly_tax_invoice_date", type="date", nullable=true)
     */
    private $pharmacyWeeklyTaxInvoiceDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="pharmacy_date_of_payment", type="date", nullable=true)
     */
    private $pharmacyDateOfPayment;

    /**
     * @var string
     *
     * @ORM\Column(name="pharmacy_total_amount_paid", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $pharmacyTotalAmountPaid;

    /**
     * @var string
     *
     * @ORM\Column(name="pharmacy_pending_to_pay_pharmacy", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $pharmacyPendingToPayPharmacy;

    /**
     * @var string
     *
     * @ORM\Column(name="logistics_shipping_and_handling", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $logisticsShippingAndHandling;

    /**
     * @var string
     *
     * @ORM\Column(name="logistics_import_tax_indo_overseas", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $logisticsImportTaxIndoOverseas;

    /**
     * @var string
     *
     * @ORM\Column(name="logistics_import_tax_sg_overseas", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $logisticsImportTaxSgOverseas;

    /**
     * @var string
     *
     * @ORM\Column(name="logistics_custom_ig_permit_fee_sg", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $logisticsCustomIgPermitFeeSg;

    /**
     * @var string
     *
     * @ORM\Column(name="logistics_payment_gateway_fees_mdr_cc", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $logisticsPaymentGatewayFeesMdrCc;

    /**
     * @var string
     *
     * @ORM\Column(name="logistics_payment_gateway_fees_mdr_myclear", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $logisticsPaymentGatewayFeesMdrMyclear;

    /**
     * @var string
     *
     * @ORM\Column(name="logistics_payment_gateway_fees_gst", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $logisticsPaymentGatewayFeesGst;

    /**
     * @var string
     *
     * @ORM\Column(name="ipay_logistics_payment_gateway_fees_mdr_cc", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $ipayLogisticsPaymentGatewayFeesMdrCc;

    /**
     * @var string
     *
     * @ORM\Column(name="ipay_logistics_payment_gateway_fees_mdr_myclear", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $ipayLogisticsPaymentGatewayFeesMdrMyclear;

    /**
     * @var string
     *
     * @ORM\Column(name="ipay_logistics_payment_gateway_fees_gst", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $ipayLogisticsPaymentGatewayFeesGst;

    /**
     * @var string
     *
     * @ORM\Column(name="logistics_medicine_costs", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $logisticsMedicineCosts;

    /**
     * @var string
     *
     * @ORM\Column(name="logistics_gst_on_medicine", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $logisticsGstOnMedicine;

    /**
     * @var string
     *
     * @ORM\Column(name="logistics_gst", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $logisticsGst;

    /**
     * @var string
     *
     * @ORM\Column(name="logistics_amount_payable", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $logisticsAmountPayable;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="logistics_daily_purchase_order_date", type="date", nullable=true)
     */
    private $logisticsDailyPurchaseOrderDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="logistics_weekly_purchase_order_date", type="date", nullable=true)
     */
    private $logisticsWeeklyPurchaseOrderDate;

    /**
     * @var string
     *
     * @ORM\Column(name="logistics_invoice_number", type="string", length=50, nullable=true)
     */
    private $logisticsInvoiceNumber;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="logistics_invoice_date", type="date", nullable=true)
     */
    private $logisticsInvoiceDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="logistics_date_of_payment", type="date", nullable=true)
     */
    private $logisticsDateOfPayment;

    /**
     * @var string
     *
     * @ORM\Column(name="logistics_total_amount_paid", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $logisticsTotalAmountPaid;

    /**
     * @var string
     *
     * @ORM\Column(name="logistics_pending_to_pay_logistics", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $logisticsPendingToPayLogistics;

    /**
     * @var string
     *
     * @ORM\Column(name="custom_indo_overseas_bm_import_duty", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $customIndoOverseasBmImportDuty;

    /**
     * @var string
     *
     * @ORM\Column(name="custom_indo_overseas_ppn_vat", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $customIndoOverseasPpnVat;

    /**
     * @var string
     *
     * @ORM\Column(name="custom_indo_overseas_pph_tax_without_id", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $customIndoOverseasPphTaxWithoutId;

    /**
     * @var string
     *
     * @ORM\Column(name="custom_indo_overseas_pph_tax_with_id", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $customIndoOverseasPphTaxWithId;

    /**
     * @var string
     *
     * @ORM\Column(name="pgf_reddot_mdr_transaction_fee", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $pgfReddotMdrTransactionFee;

    /**
     * @var string
     *
     * @ORM\Column(name="pgf_reddot_gst_on_mdr", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $pgfReddotGstOnMdr;

    /**
     * @var string
     *
     * @ORM\Column(name="doctor_custom_indo_overseas_bm_import_duty", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $doctorCustomIndoOverseasBmImportDuty;

    /**
     * @var string
     *
     * @ORM\Column(name="doctor_custom_indo_overseas_ppn_vat", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $doctorCustomIndoOverseasPpnVat;

    /**
     * @var string
     *
     * @ORM\Column(name="doctor_custom_indo_overseas_pph_tax_without_id", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $doctorCustomIndoOverseasPphTaxWithoutId;

    /**
     * @var string
     *
     * @ORM\Column(name="doctor_custom_indo_overseas_pph_tax_with_id", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $doctorCustomIndoOverseasPphTaxWithId;

    /**
     * @var string
     *
     * @ORM\Column(name="doctor_pgf_reddot_mdr_transaction_fee", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $doctorPgfReddotMdrTransactionFee;

    /**
     * @var string
     *
     * @ORM\Column(name="doctor_pgf_reddot_gst_on_mdr", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $doctorPgfReddotGstOnMdr;

    /**
     * @var string
     *
     * @ORM\Column(name="pharmacy_pgf_reddot_mdr_transaction_fee", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $pharmacyPgfReddotMdrTransactionFee;

    /**
     * @var string
     *
     * @ORM\Column(name="pharmacy_pgf_reddot_gst_on_cc", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $pharmacyPgfReddotGstOnCc;

    /**
     * @var string
     *
     * @ORM\Column(name="pharmacy_custom_indo_overseas_bm_import_duty", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $pharmacyCustomIndoOverseasBmImportDuty;

    /**
     * @var string
     *
     * @ORM\Column(name="pharmacy_custom_indo_overseas_ppn_vat", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $pharmacyCustomIndoOverseasPpnVat;

    /**
     * @var string
     *
     * @ORM\Column(name="pharmacy_custom_indo_overseas_pph_tax_without_id", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $pharmacyCustomIndoOverseasPphTaxWithoutId;

    /**
     * @var string
     *
     * @ORM\Column(name="pharmacy_custom_indo_overseas_pph_tax_with_id", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $pharmacyCustomIndoOverseasPphTaxWithId;

    /**
     * @var string
     *
     * @ORM\Column(name="logistics_custom_indo_overseas_bm_import_duty", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $logisticsCustomIndoOverseasBmImportDuty;

    /**
     * @var string
     *
     * @ORM\Column(name="logistics_custom_indo_overseas_ppn_vat", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $logisticsCustomIndoOverseasPpnVat;

    /**
     * @var string
     *
     * @ORM\Column(name="logistics_custom_indo_overseas_pph_tax_without_id", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $logisticsCustomIndoOverseasPphTaxWithoutId;

    /**
     * @var string
     *
     * @ORM\Column(name="logistics_custom_indo_overseas_pph_tax_with_id", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $logisticsCustomIndoOverseasPphTaxWithId;

    /**
     * @var string
     *
     * @ORM\Column(name="logistics_pgf_reddot_mdr_transaction_fee", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $logisticsPgfReddotMdrTransactionFee;

    /**
     * @var string
     *
     * @ORM\Column(name="logistics_pgf_reddot_gst_on_cc", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $logisticsPgfReddotGstOnCc;

    /**
     * @var string
     *
     * @ORM\Column(name="logistics_destruction_cost", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $logisticsDestructionCost;

    /**
     * @var string
     *
     * @ORM\Column(name="doctor_adminstrative_fee", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $doctorAdminstrativeFee;

    /**
     * @var string
     *
     * @ORM\Column(name="doctor_destruction_fee", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $doctorDestructionFee;

    /**
     * @var string
     *
     * @ORM\Column(name="doctor_gst_on_invoice", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $doctorGstOnInvoice;

    /**
     * @var string
     *
     * @ORM\Column(name="pharmacy_adminstrative_fee", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $pharmacyAdminstrativeFee;

    /**
     * @var string
     *
     * @ORM\Column(name="pharmacy_destruction_fee", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $pharmacyDestructionFee;

    /**
     * @var string
     *
     * @ORM\Column(name="pharmacy_gst_on_invoice", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $pharmacyGstOnInvoice;

    /**
     * @var string
     *
     * @ORM\Column(name="logistics_adminstrative_fee", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $logisticsAdminstrativeFee;

    /**
     * @var string
     *
     * @ORM\Column(name="logistics_destruction_fee", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $logisticsDestructionFee;

    /**
     * @var string
     *
     * @ORM\Column(name="doctor_fees_gmedes_admin_service_fee_per_rescription", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $doctorFeesGmedesAdminServiceFeePerRescription;

    /**
     * @var string
     *
     * @ORM\Column(name="doctors_fees_prescription_support_general_eplatform", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $doctorsFeesPrescriptionSupportGeneralEplatform;

    /**
     * @var string
     *
     * @ORM\Column(name="doctors_fees_prescription_support_special_eplatform", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $doctorsFeesPrescriptionSupportSpecialEplatform;

    /**
     * @var string
     *
     * @ORM\Column(name="doctor_gmedes_admin_service_fee_per_prescription", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $doctorGmedesAdminServiceFeePerPrescription;

    /**
     * @var string
     *
     * @ORM\Column(name="agent_type", type="string", length=50, nullable=true)
     */
    private $agentType;

    /**
     * @var string
     *
     * @ORM\Column(name="agent_fee_marketing_service_local_overseas", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $agentFeeMarketingServiceLocalOverseas;

    /**
     * @var string
     *
     * @ORM\Column(name="agent_fee_prescription_admin_fee_local_overseas", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $agentFeePrescriptionAdminFeeLocalOverseas;

    /**
     * @var string
     *
     * @ORM\Column(name="pharmacy_total_amount_payable_before_gst", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $pharmacyTotalAmountPayableBeforeGst;

    /**
     * @var string
     *
     * @ORM\Column(name="logistics_amount_payable_before_gst", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $logisticsAmountPayableBeforeGst;

    /**
     * @var string
     *
     * @ORM\Column(name="ap_doctor_general_medicine_admin_fee", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $apDoctorGeneralMedicineAdminFee;

    /**
     * @var string
     *
     * @ORM\Column(name="ar_doctor_special_medicine_admin_fee", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $arDoctorSpecialMedicineAdminFee;



    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn = 'CURRENT_TIMESTAMP';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_on", type="datetime", nullable=true)
     */
    private $updatedOn;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set location
     *
     * @param boolean $location
     *
     * @return TransactionListing
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return boolean
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set datePatientPay
     *
     * @param \DateTime $datePatientPay
     *
     * @return TransactionListing
     */
    public function setDatePatientPay($datePatientPay)
    {
        $this->datePatientPay = $datePatientPay;

        return $this;
    }

    /**
     * Get datePatientPay
     *
     * @return \DateTime
     */
    public function getDatePatientPay()
    {
        return $this->datePatientPay;
    }

    /**
     * Set orderId
     *
     * @param string $orderId
     *
     * @return TransactionListing
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * Get orderId
     *
     * @return string
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Set statusFromCustomerService
     *
     * @param integer $statusFromCustomerService
     *
     * @return TransactionListing
     */
    public function setStatusFromCustomerService($statusFromCustomerService)
    {
        $this->statusFromCustomerService = $statusFromCustomerService;

        return $this;
    }

    /**
     * Get statusFromCustomerService
     *
     * @return integer
     */
    public function getStatusFromCustomerService()
    {
        return $this->statusFromCustomerService;
    }

    /**
     * Set referenceNo
     *
     * @param string $referenceNo
     *
     * @return TransactionListing
     */
    public function setReferenceNo($referenceNo)
    {
        $this->referenceNo = $referenceNo;

        return $this;
    }

    /**
     * Get referenceNo
     *
     * @return string
     */
    public function getReferenceNo()
    {
        return $this->referenceNo;
    }

    /**
     * Set doctorName
     *
     * @param string $doctorName
     *
     * @return TransactionListing
     */
    public function setDoctorName($doctorName)
    {
        $this->doctorName = $doctorName;

        return $this;
    }

    /**
     * Get doctorName
     *
     * @return string
     */
    public function getDoctorName()
    {
        return $this->doctorName;
    }

    /**
     * Set isClinicGstRegistered
     *
     * @param boolean $isClinicGstRegistered
     *
     * @return TransactionListing
     */
    public function setIsClinicGstRegistered($isClinicGstRegistered)
    {
        $this->isClinicGstRegistered = $isClinicGstRegistered;

        return $this;
    }

    /**
     * Get isClinicGstRegistered
     *
     * @return boolean
     */
    public function getIsClinicGstRegistered()
    {
        return $this->isClinicGstRegistered;
    }

    /**
     * Set patientName
     *
     * @param string $patientName
     *
     * @return TransactionListing
     */
    public function setPatientName($patientName)
    {
        $this->patientName = $patientName;

        return $this;
    }

    /**
     * Get patientName
     *
     * @return string
     */
    public function getPatientName()
    {
        return $this->patientName;
    }

    /**
     * Set saleGeneralMedicine
     *
     * @param string $saleGeneralMedicine
     *
     * @return TransactionListing
     */
    public function setSaleGeneralMedicine($saleGeneralMedicine)
    {
        $this->saleGeneralMedicine = $saleGeneralMedicine;

        return $this;
    }

    /**
     * Get saleGeneralMedicine
     *
     * @return string
     */
    public function getSaleGeneralMedicine()
    {
        return $this->saleGeneralMedicine;
    }

    /**
     * Set saleSpecialMedicine
     *
     * @param string $saleSpecialMedicine
     *
     * @return TransactionListing
     */
    public function setSaleSpecialMedicine($saleSpecialMedicine)
    {
        $this->saleSpecialMedicine = $saleSpecialMedicine;

        return $this;
    }

    /**
     * Get saleSpecialMedicine
     *
     * @return string
     */
    public function getSaleSpecialMedicine()
    {
        return $this->saleSpecialMedicine;
    }

    /**
     * Set doctorFeesConsult
     *
     * @param string $doctorFeesConsult
     *
     * @return TransactionListing
     */
    public function setDoctorFeesConsult($doctorFeesConsult)
    {
        $this->doctorFeesConsult = $doctorFeesConsult;

        return $this;
    }

    /**
     * Get doctorFeesConsult
     *
     * @return string
     */
    public function getDoctorFeesConsult()
    {
        return $this->doctorFeesConsult;
    }

    /**
     * Set doctorFeesReviewRx
     *
     * @param string $doctorFeesReviewRx
     *
     * @return TransactionListing
     */
    public function setDoctorFeesReviewRx($doctorFeesReviewRx)
    {
        $this->doctorFeesReviewRx = $doctorFeesReviewRx;

        return $this;
    }

    /**
     * Get doctorFeesReviewRx
     *
     * @return string
     */
    public function getDoctorFeesReviewRx()
    {
        return $this->doctorFeesReviewRx;
    }

    /**
     * Set shippingAndHandling
     *
     * @param string $shippingAndHandling
     *
     * @return TransactionListing
     */
    public function setShippingAndHandling($shippingAndHandling)
    {
        $this->shippingAndHandling = $shippingAndHandling;

        return $this;
    }

    /**
     * Get shippingAndHandling
     *
     * @return string
     */
    public function getShippingAndHandling()
    {
        return $this->shippingAndHandling;
    }

    /**
     * Set importTaxIndoOverseas
     *
     * @param string $importTaxIndoOverseas
     *
     * @return TransactionListing
     */
    public function setImportTaxIndoOverseas($importTaxIndoOverseas)
    {
        $this->importTaxIndoOverseas = $importTaxIndoOverseas;

        return $this;
    }

    /**
     * Get importTaxIndoOverseas
     *
     * @return string
     */
    public function getImportTaxIndoOverseas()
    {
        return $this->importTaxIndoOverseas;
    }

    /**
     * Set importTaxSgOverseas
     *
     * @param string $importTaxSgOverseas
     *
     * @return TransactionListing
     */
    public function setImportTaxSgOverseas($importTaxSgOverseas)
    {
        $this->importTaxSgOverseas = $importTaxSgOverseas;

        return $this;
    }

    /**
     * Get importTaxSgOverseas
     *
     * @return string
     */
    public function getImportTaxSgOverseas()
    {
        return $this->importTaxSgOverseas;
    }

    /**
     * Set customClearanceAdminFeeIndoOverseas
     *
     * @param string $customClearanceAdminFeeIndoOverseas
     *
     * @return TransactionListing
     */
    public function setCustomClearanceAdminFeeIndoOverseas($customClearanceAdminFeeIndoOverseas)
    {
        $this->customClearanceAdminFeeIndoOverseas = $customClearanceAdminFeeIndoOverseas;

        return $this;
    }

    /**
     * Get customClearanceAdminFeeIndoOverseas
     *
     * @return string
     */
    public function getCustomClearanceAdminFeeIndoOverseas()
    {
        return $this->customClearanceAdminFeeIndoOverseas;
    }

    /**
     * Set customClearanceAdminFeeSgOverseas
     *
     * @param string $customClearanceAdminFeeSgOverseas
     *
     * @return TransactionListing
     */
    public function setCustomClearanceAdminFeeSgOverseas($customClearanceAdminFeeSgOverseas)
    {
        $this->customClearanceAdminFeeSgOverseas = $customClearanceAdminFeeSgOverseas;

        return $this;
    }

    /**
     * Get customClearanceAdminFeeSgOverseas
     *
     * @return string
     */
    public function getCustomClearanceAdminFeeSgOverseas()
    {
        return $this->customClearanceAdminFeeSgOverseas;
    }

    /**
     * Set customIgPermitFeeSg
     *
     * @param string $customIgPermitFeeSg
     *
     * @return TransactionListing
     */
    public function setCustomIgPermitFeeSg($customIgPermitFeeSg)
    {
        $this->customIgPermitFeeSg = $customIgPermitFeeSg;

        return $this;
    }

    /**
     * Get customIgPermitFeeSg
     *
     * @return string
     */
    public function getCustomIgPermitFeeSg()
    {
        return $this->customIgPermitFeeSg;
    }

    /**
     * Set patientGst
     *
     * @param string $patientGst
     *
     * @return TransactionListing
     */
    public function setPatientGst($patientGst)
    {
        $this->patientGst = $patientGst;

        return $this;
    }

    /**
     * Get patientGst
     *
     * @return string
     */
    public function getPatientGst()
    {
        return $this->patientGst;
    }

    /**
     * Set totalPrescriptionAdvice
     *
     * @param string $totalPrescriptionAdvice
     *
     * @return TransactionListing
     */
    public function setTotalPrescriptionAdvice($totalPrescriptionAdvice)
    {
        $this->totalPrescriptionAdvice = $totalPrescriptionAdvice;

        return $this;
    }

    /**
     * Get totalPrescriptionAdvice
     *
     * @return string
     */
    public function getTotalPrescriptionAdvice()
    {
        return $this->totalPrescriptionAdvice;
    }

    /**
     * Set paymentGateway
     *
     * @param string $paymentGateway
     *
     * @return TransactionListing
     */
    public function setPaymentGateway($paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;

        return $this;
    }

    /**
     * Get paymentGateway
     *
     * @return string
     */
    public function getPaymentGateway()
    {
        return $this->paymentGateway;
    }

    /**
     * Set settlementDateByPaymentGateway
     *
     * @param \DateTime $settlementDateByPaymentGateway
     *
     * @return TransactionListing
     */
    public function setSettlementDateByPaymentGateway($settlementDateByPaymentGateway)
    {
        $this->settlementDateByPaymentGateway = $settlementDateByPaymentGateway;

        return $this;
    }

    /**
     * Get settlementDateByPaymentGateway
     *
     * @return \DateTime
     */
    public function getSettlementDateByPaymentGateway()
    {
        return $this->settlementDateByPaymentGateway;
    }

    /**
     * Set amountToBePaidByPaymentGateway
     *
     * @param string $amountToBePaidByPaymentGateway
     *
     * @return TransactionListing
     */
    public function setAmountToBePaidByPaymentGateway($amountToBePaidByPaymentGateway)
    {
        $this->amountToBePaidByPaymentGateway = $amountToBePaidByPaymentGateway;

        return $this;
    }

    /**
     * Get amountToBePaidByPaymentGateway
     *
     * @return string
     */
    public function getAmountToBePaidByPaymentGateway()
    {
        return $this->amountToBePaidByPaymentGateway;
    }

    /**
     * Set amountPaidByPaymentGateway
     *
     * @param string $amountPaidByPaymentGateway
     *
     * @return TransactionListing
     */
    public function setAmountPaidByPaymentGateway($amountPaidByPaymentGateway)
    {
        $this->amountPaidByPaymentGateway = $amountPaidByPaymentGateway;

        return $this;
    }

    /**
     * Get amountPaidByPaymentGateway
     *
     * @return string
     */
    public function getAmountPaidByPaymentGateway()
    {
        return $this->amountPaidByPaymentGateway;
    }

    /**
     * Set paymentGatewayFeesMdrCc
     *
     * @param string $paymentGatewayFeesMdrCc
     *
     * @return TransactionListing
     */
    public function setPaymentGatewayFeesMdrCc($paymentGatewayFeesMdrCc)
    {
        $this->paymentGatewayFeesMdrCc = $paymentGatewayFeesMdrCc;

        return $this;
    }

    /**
     * Get paymentGatewayFeesMdrCc
     *
     * @return string
     */
    public function getPaymentGatewayFeesMdrCc()
    {
        return $this->paymentGatewayFeesMdrCc;
    }

    /**
     * Set paymentGatewayFeesMdrMyclear
     *
     * @param string $paymentGatewayFeesMdrMyclear
     *
     * @return TransactionListing
     */
    public function setPaymentGatewayFeesMdrMyclear($paymentGatewayFeesMdrMyclear)
    {
        $this->paymentGatewayFeesMdrMyclear = $paymentGatewayFeesMdrMyclear;

        return $this;
    }

    /**
     * Get paymentGatewayFeesMdrMyclear
     *
     * @return string
     */
    public function getPaymentGatewayFeesMdrMyclear()
    {
        return $this->paymentGatewayFeesMdrMyclear;
    }

    /**
     * Set paymentGatewayFeesGst
     *
     * @param string $paymentGatewayFeesGst
     *
     * @return TransactionListing
     */
    public function setPaymentGatewayFeesGst($paymentGatewayFeesGst)
    {
        $this->paymentGatewayFeesGst = $paymentGatewayFeesGst;

        return $this;
    }

    /**
     * Get paymentGatewayFeesGst
     *
     * @return string
     */
    public function getPaymentGatewayFeesGst()
    {
        return $this->paymentGatewayFeesGst;
    }

    /**
     * Set paymentGatewayInvoicePgw
     *
     * @param string $paymentGatewayInvoicePgw
     *
     * @return TransactionListing
     */
    public function setPaymentGatewayInvoicePgw($paymentGatewayInvoicePgw)
    {
        $this->paymentGatewayInvoicePgw = $paymentGatewayInvoicePgw;

        return $this;
    }

    /**
     * Get paymentGatewayInvoicePgw
     *
     * @return string
     */
    public function getPaymentGatewayInvoicePgw()
    {
        return $this->paymentGatewayInvoicePgw;
    }

    /**
     * Set dateOfInvoice
     *
     * @param \DateTime $dateOfInvoice
     *
     * @return TransactionListing
     */
    public function setDateOfInvoice($dateOfInvoice)
    {
        $this->dateOfInvoice = $dateOfInvoice;

        return $this;
    }

    /**
     * Get dateOfInvoice
     *
     * @return \DateTime
     */
    public function getDateOfInvoice()
    {
        return $this->dateOfInvoice;
    }

    /**
     * Set paymentGatewayPendingToPayGmedes
     *
     * @param string $paymentGatewayPendingToPayGmedes
     *
     * @return TransactionListing
     */
    public function setPaymentGatewayPendingToPayGmedes($paymentGatewayPendingToPayGmedes)
    {
        $this->paymentGatewayPendingToPayGmedes = $paymentGatewayPendingToPayGmedes;

        return $this;
    }

    /**
     * Get paymentGatewayPendingToPayGmedes
     *
     * @return string
     */
    public function getPaymentGatewayPendingToPayGmedes()
    {
        return $this->paymentGatewayPendingToPayGmedes;
    }

    /**
     * Set paymentGatewayCompany
     *
     * @param string $paymentGatewayCompany
     *
     * @return TransactionListing
     */
    public function setPaymentGatewayCompany($paymentGatewayCompany)
    {
        $this->paymentGatewayCompany = $paymentGatewayCompany;

        return $this;
    }

    /**
     * Get paymentGatewayCompany
     *
     * @return string
     */
    public function getPaymentGatewayCompany()
    {
        return $this->paymentGatewayCompany;
    }

    /**
     * Set ipaySettlementDateByPaymentGateway
     *
     * @param \DateTime $ipaySettlementDateByPaymentGateway
     *
     * @return TransactionListing
     */
    public function setIpaySettlementDateByPaymentGateway($ipaySettlementDateByPaymentGateway)
    {
        $this->ipaySettlementDateByPaymentGateway = $ipaySettlementDateByPaymentGateway;

        return $this;
    }

    /**
     * Get ipaySettlementDateByPaymentGateway
     *
     * @return \DateTime
     */
    public function getIpaySettlementDateByPaymentGateway()
    {
        return $this->ipaySettlementDateByPaymentGateway;
    }

    /**
     * Set ipayAmountToBePaidByPaymentGateway
     *
     * @param string $ipayAmountToBePaidByPaymentGateway
     *
     * @return TransactionListing
     */
    public function setIpayAmountToBePaidByPaymentGateway($ipayAmountToBePaidByPaymentGateway)
    {
        $this->ipayAmountToBePaidByPaymentGateway = $ipayAmountToBePaidByPaymentGateway;

        return $this;
    }

    /**
     * Get ipayAmountToBePaidByPaymentGateway
     *
     * @return string
     */
    public function getIpayAmountToBePaidByPaymentGateway()
    {
        return $this->ipayAmountToBePaidByPaymentGateway;
    }

    /**
     * Set ipayAmountPaidByPaymentGateway
     *
     * @param string $ipayAmountPaidByPaymentGateway
     *
     * @return TransactionListing
     */
    public function setIpayAmountPaidByPaymentGateway($ipayAmountPaidByPaymentGateway)
    {
        $this->ipayAmountPaidByPaymentGateway = $ipayAmountPaidByPaymentGateway;

        return $this;
    }

    /**
     * Get ipayAmountPaidByPaymentGateway
     *
     * @return string
     */
    public function getIpayAmountPaidByPaymentGateway()
    {
        return $this->ipayAmountPaidByPaymentGateway;
    }

    /**
     * Set ipayPaymentGatewayFeesMdrCc
     *
     * @param string $ipayPaymentGatewayFeesMdrCc
     *
     * @return TransactionListing
     */
    public function setIpayPaymentGatewayFeesMdrCc($ipayPaymentGatewayFeesMdrCc)
    {
        $this->ipayPaymentGatewayFeesMdrCc = $ipayPaymentGatewayFeesMdrCc;

        return $this;
    }

    /**
     * Get ipayPaymentGatewayFeesMdrCc
     *
     * @return string
     */
    public function getIpayPaymentGatewayFeesMdrCc()
    {
        return $this->ipayPaymentGatewayFeesMdrCc;
    }

    /**
     * Set ipayPaymentGatewayFeesMdrMyclear
     *
     * @param string $ipayPaymentGatewayFeesMdrMyclear
     *
     * @return TransactionListing
     */
    public function setIpayPaymentGatewayFeesMdrMyclear($ipayPaymentGatewayFeesMdrMyclear)
    {
        $this->ipayPaymentGatewayFeesMdrMyclear = $ipayPaymentGatewayFeesMdrMyclear;

        return $this;
    }

    /**
     * Get ipayPaymentGatewayFeesMdrMyclear
     *
     * @return string
     */
    public function getIpayPaymentGatewayFeesMdrMyclear()
    {
        return $this->ipayPaymentGatewayFeesMdrMyclear;
    }

    /**
     * Set ipayPaymentGatewayFeesGst
     *
     * @param string $ipayPaymentGatewayFeesGst
     *
     * @return TransactionListing
     */
    public function setIpayPaymentGatewayFeesGst($ipayPaymentGatewayFeesGst)
    {
        $this->ipayPaymentGatewayFeesGst = $ipayPaymentGatewayFeesGst;

        return $this;
    }

    /**
     * Get ipayPaymentGatewayFeesGst
     *
     * @return string
     */
    public function getIpayPaymentGatewayFeesGst()
    {
        return $this->ipayPaymentGatewayFeesGst;
    }

    /**
     * Set ipayPaymentGatewayInvoicePgw
     *
     * @param string $ipayPaymentGatewayInvoicePgw
     *
     * @return TransactionListing
     */
    public function setIpayPaymentGatewayInvoicePgw($ipayPaymentGatewayInvoicePgw)
    {
        $this->ipayPaymentGatewayInvoicePgw = $ipayPaymentGatewayInvoicePgw;

        return $this;
    }

    /**
     * Get ipayPaymentGatewayInvoicePgw
     *
     * @return string
     */
    public function getIpayPaymentGatewayInvoicePgw()
    {
        return $this->ipayPaymentGatewayInvoicePgw;
    }

    /**
     * Set ipayDateOfInvoice
     *
     * @param \DateTime $ipayDateOfInvoice
     *
     * @return TransactionListing
     */
    public function setIpayDateOfInvoice($ipayDateOfInvoice)
    {
        $this->ipayDateOfInvoice = $ipayDateOfInvoice;

        return $this;
    }

    /**
     * Get ipayDateOfInvoice
     *
     * @return \DateTime
     */
    public function getIpayDateOfInvoice()
    {
        return $this->ipayDateOfInvoice;
    }

    /**
     * Set ipayPaymentGatewayPendingToPayGmedes
     *
     * @param string $ipayPaymentGatewayPendingToPayGmedes
     *
     * @return TransactionListing
     */
    public function setIpayPaymentGatewayPendingToPayGmedes($ipayPaymentGatewayPendingToPayGmedes)
    {
        $this->ipayPaymentGatewayPendingToPayGmedes = $ipayPaymentGatewayPendingToPayGmedes;

        return $this;
    }

    /**
     * Get ipayPaymentGatewayPendingToPayGmedes
     *
     * @return string
     */
    public function getIpayPaymentGatewayPendingToPayGmedes()
    {
        return $this->ipayPaymentGatewayPendingToPayGmedes;
    }

    /**
     * Set generalMedicineCostPrice
     *
     * @param string $generalMedicineCostPrice
     *
     * @return TransactionListing
     */
    public function setGeneralMedicineCostPrice($generalMedicineCostPrice)
    {
        $this->generalMedicineCostPrice = $generalMedicineCostPrice;

        return $this;
    }

    /**
     * Get generalMedicineCostPrice
     *
     * @return string
     */
    public function getGeneralMedicineCostPrice()
    {
        return $this->generalMedicineCostPrice;
    }

    /**
     * Set specialMedicineCostPrice
     *
     * @param string $specialMedicineCostPrice
     *
     * @return TransactionListing
     */
    public function setSpecialMedicineCostPrice($specialMedicineCostPrice)
    {
        $this->specialMedicineCostPrice = $specialMedicineCostPrice;

        return $this;
    }

    /**
     * Get specialMedicineCostPrice
     *
     * @return string
     */
    public function getSpecialMedicineCostPrice()
    {
        return $this->specialMedicineCostPrice;
    }

    /**
     * Set totalProductCostPrice
     *
     * @param string $totalProductCostPrice
     *
     * @return TransactionListing
     */
    public function setTotalProductCostPrice($totalProductCostPrice)
    {
        $this->totalProductCostPrice = $totalProductCostPrice;

        return $this;
    }

    /**
     * Get totalProductCostPrice
     *
     * @return string
     */
    public function getTotalProductCostPrice()
    {
        return $this->totalProductCostPrice;
    }

    /**
     * Set gstAmountForMedicine
     *
     * @param string $gstAmountForMedicine
     *
     * @return TransactionListing
     */
    public function setGstAmountForMedicine($gstAmountForMedicine)
    {
        $this->gstAmountForMedicine = $gstAmountForMedicine;

        return $this;
    }

    /**
     * Get gstAmountForMedicine
     *
     * @return string
     */
    public function getGstAmountForMedicine()
    {
        return $this->gstAmountForMedicine;
    }

    /**
     * Set totalMarginGeneralMedicine
     *
     * @param string $totalMarginGeneralMedicine
     *
     * @return TransactionListing
     */
    public function setTotalMarginGeneralMedicine($totalMarginGeneralMedicine)
    {
        $this->totalMarginGeneralMedicine = $totalMarginGeneralMedicine;

        return $this;
    }

    /**
     * Get totalMarginGeneralMedicine
     *
     * @return string
     */
    public function getTotalMarginGeneralMedicine()
    {
        return $this->totalMarginGeneralMedicine;
    }

    /**
     * Set totalMarginSpecialMedicine
     *
     * @param string $totalMarginSpecialMedicine
     *
     * @return TransactionListing
     */
    public function setTotalMarginSpecialMedicine($totalMarginSpecialMedicine)
    {
        $this->totalMarginSpecialMedicine = $totalMarginSpecialMedicine;

        return $this;
    }

    /**
     * Get totalMarginSpecialMedicine
     *
     * @return string
     */
    public function getTotalMarginSpecialMedicine()
    {
        return $this->totalMarginSpecialMedicine;
    }

    /**
     * Set totalMarginMedicine
     *
     * @param string $totalMarginMedicine
     *
     * @return TransactionListing
     */
    public function setTotalMarginMedicine($totalMarginMedicine)
    {
        $this->totalMarginMedicine = $totalMarginMedicine;

        return $this;
    }

    /**
     * Get totalMarginMedicine
     *
     * @return string
     */
    public function getTotalMarginMedicine()
    {
        return $this->totalMarginMedicine;
    }

    /**
     * Set doctorGeneralMedicine
     *
     * @param string $doctorGeneralMedicine
     *
     * @return TransactionListing
     */
    public function setDoctorGeneralMedicine($doctorGeneralMedicine)
    {
        $this->doctorGeneralMedicine = $doctorGeneralMedicine;

        return $this;
    }

    /**
     * Get doctorGeneralMedicine
     *
     * @return string
     */
    public function getDoctorGeneralMedicine()
    {
        return $this->doctorGeneralMedicine;
    }

    /**
     * Set doctorSpecialMedicine
     *
     * @param string $doctorSpecialMedicine
     *
     * @return TransactionListing
     */
    public function setDoctorSpecialMedicine($doctorSpecialMedicine)
    {
        $this->doctorSpecialMedicine = $doctorSpecialMedicine;

        return $this;
    }

    /**
     * Get doctorSpecialMedicine
     *
     * @return string
     */
    public function getDoctorSpecialMedicine()
    {
        return $this->doctorSpecialMedicine;
    }

    /**
     * Set doctorsFeesMarginOnMedicineGeneralGmedes
     *
     * @param string $doctorsFeesMarginOnMedicineGeneralGmedes
     *
     * @return TransactionListing
     */
    public function setDoctorsFeesMarginOnMedicineGeneralGmedes($doctorsFeesMarginOnMedicineGeneralGmedes)
    {
        $this->doctorsFeesMarginOnMedicineGeneralGmedes = $doctorsFeesMarginOnMedicineGeneralGmedes;

        return $this;
    }

    /**
     * Get doctorsFeesMarginOnMedicineGeneralGmedes
     *
     * @return string
     */
    public function getDoctorsFeesMarginOnMedicineGeneralGmedes()
    {
        return $this->doctorsFeesMarginOnMedicineGeneralGmedes;
    }

    /**
     * Set doctorsFeesMarginOnMedicineSpecialGmedes
     *
     * @param string $doctorsFeesMarginOnMedicineSpecialGmedes
     *
     * @return TransactionListing
     */
    public function setDoctorsFeesMarginOnMedicineSpecialGmedes($doctorsFeesMarginOnMedicineSpecialGmedes)
    {
        $this->doctorsFeesMarginOnMedicineSpecialGmedes = $doctorsFeesMarginOnMedicineSpecialGmedes;

        return $this;
    }

    /**
     * Get doctorsFeesMarginOnMedicineSpecialGmedes
     *
     * @return string
     */
    public function getDoctorsFeesMarginOnMedicineSpecialGmedes()
    {
        return $this->doctorsFeesMarginOnMedicineSpecialGmedes;
    }

    /**
     * Set doctorConsult
     *
     * @param string $doctorConsult
     *
     * @return TransactionListing
     */
    public function setDoctorConsult($doctorConsult)
    {
        $this->doctorConsult = $doctorConsult;

        return $this;
    }

    /**
     * Get doctorConsult
     *
     * @return string
     */
    public function getDoctorConsult()
    {
        return $this->doctorConsult;
    }

    /**
     * Set doctorReviewRx
     *
     * @param string $doctorReviewRx
     *
     * @return TransactionListing
     */
    public function setDoctorReviewRx($doctorReviewRx)
    {
        $this->doctorReviewRx = $doctorReviewRx;

        return $this;
    }

    /**
     * Get doctorReviewRx
     *
     * @return string
     */
    public function getDoctorReviewRx()
    {
        return $this->doctorReviewRx;
    }

    /**
     * Set doctorShippingAndHandling
     *
     * @param string $doctorShippingAndHandling
     *
     * @return TransactionListing
     */
    public function setDoctorShippingAndHandling($doctorShippingAndHandling)
    {
        $this->doctorShippingAndHandling = $doctorShippingAndHandling;

        return $this;
    }

    /**
     * Get doctorShippingAndHandling
     *
     * @return string
     */
    public function getDoctorShippingAndHandling()
    {
        return $this->doctorShippingAndHandling;
    }

    /**
     * Set doctorImportTaxIndoOverseas
     *
     * @param string $doctorImportTaxIndoOverseas
     *
     * @return TransactionListing
     */
    public function setDoctorImportTaxIndoOverseas($doctorImportTaxIndoOverseas)
    {
        $this->doctorImportTaxIndoOverseas = $doctorImportTaxIndoOverseas;

        return $this;
    }

    /**
     * Get doctorImportTaxIndoOverseas
     *
     * @return string
     */
    public function getDoctorImportTaxIndoOverseas()
    {
        return $this->doctorImportTaxIndoOverseas;
    }

    /**
     * Set doctorImportTaxSgOverseas
     *
     * @param string $doctorImportTaxSgOverseas
     *
     * @return TransactionListing
     */
    public function setDoctorImportTaxSgOverseas($doctorImportTaxSgOverseas)
    {
        $this->doctorImportTaxSgOverseas = $doctorImportTaxSgOverseas;

        return $this;
    }

    /**
     * Get doctorImportTaxSgOverseas
     *
     * @return string
     */
    public function getDoctorImportTaxSgOverseas()
    {
        return $this->doctorImportTaxSgOverseas;
    }

    /**
     * Set doctorFeesCustomClearanceAdminFeeIndoOverseas
     *
     * @param string $doctorFeesCustomClearanceAdminFeeIndoOverseas
     *
     * @return TransactionListing
     */
    public function setDoctorFeesCustomClearanceAdminFeeIndoOverseas($doctorFeesCustomClearanceAdminFeeIndoOverseas)
    {
        $this->doctorFeesCustomClearanceAdminFeeIndoOverseas = $doctorFeesCustomClearanceAdminFeeIndoOverseas;

        return $this;
    }

    /**
     * Get doctorFeesCustomClearanceAdminFeeIndoOverseas
     *
     * @return string
     */
    public function getDoctorFeesCustomClearanceAdminFeeIndoOverseas()
    {
        return $this->doctorFeesCustomClearanceAdminFeeIndoOverseas;
    }

    /**
     * Set doctorFeesCustomClearanceAdminFeeSgOverseas
     *
     * @param string $doctorFeesCustomClearanceAdminFeeSgOverseas
     *
     * @return TransactionListing
     */
    public function setDoctorFeesCustomClearanceAdminFeeSgOverseas($doctorFeesCustomClearanceAdminFeeSgOverseas)
    {
        $this->doctorFeesCustomClearanceAdminFeeSgOverseas = $doctorFeesCustomClearanceAdminFeeSgOverseas;

        return $this;
    }

    /**
     * Get doctorFeesCustomClearanceAdminFeeSgOverseas
     *
     * @return string
     */
    public function getDoctorFeesCustomClearanceAdminFeeSgOverseas()
    {
        return $this->doctorFeesCustomClearanceAdminFeeSgOverseas;
    }

    /**
     * Set doctorCustomIgPermitFeeSg
     *
     * @param string $doctorCustomIgPermitFeeSg
     *
     * @return TransactionListing
     */
    public function setDoctorCustomIgPermitFeeSg($doctorCustomIgPermitFeeSg)
    {
        $this->doctorCustomIgPermitFeeSg = $doctorCustomIgPermitFeeSg;

        return $this;
    }

    /**
     * Get doctorCustomIgPermitFeeSg
     *
     * @return string
     */
    public function getDoctorCustomIgPermitFeeSg()
    {
        return $this->doctorCustomIgPermitFeeSg;
    }

    /**
     * Set doctorPaymentGatewayFeesMdrCc
     *
     * @param string $doctorPaymentGatewayFeesMdrCc
     *
     * @return TransactionListing
     */
    public function setDoctorPaymentGatewayFeesMdrCc($doctorPaymentGatewayFeesMdrCc)
    {
        $this->doctorPaymentGatewayFeesMdrCc = $doctorPaymentGatewayFeesMdrCc;

        return $this;
    }

    /**
     * Get doctorPaymentGatewayFeesMdrCc
     *
     * @return string
     */
    public function getDoctorPaymentGatewayFeesMdrCc()
    {
        return $this->doctorPaymentGatewayFeesMdrCc;
    }

    /**
     * Set doctorPaymentGatewayFeesMdrMyclear
     *
     * @param string $doctorPaymentGatewayFeesMdrMyclear
     *
     * @return TransactionListing
     */
    public function setDoctorPaymentGatewayFeesMdrMyclear($doctorPaymentGatewayFeesMdrMyclear)
    {
        $this->doctorPaymentGatewayFeesMdrMyclear = $doctorPaymentGatewayFeesMdrMyclear;

        return $this;
    }

    /**
     * Get doctorPaymentGatewayFeesMdrMyclear
     *
     * @return string
     */
    public function getDoctorPaymentGatewayFeesMdrMyclear()
    {
        return $this->doctorPaymentGatewayFeesMdrMyclear;
    }

    /**
     * Set doctorPaymentGatewayFeesGst
     *
     * @param string $doctorPaymentGatewayFeesGst
     *
     * @return TransactionListing
     */
    public function setDoctorPaymentGatewayFeesGst($doctorPaymentGatewayFeesGst)
    {
        $this->doctorPaymentGatewayFeesGst = $doctorPaymentGatewayFeesGst;

        return $this;
    }

    /**
     * Get doctorPaymentGatewayFeesGst
     *
     * @return string
     */
    public function getDoctorPaymentGatewayFeesGst()
    {
        return $this->doctorPaymentGatewayFeesGst;
    }

    /**
     * Set ipayDoctorPaymentGatewayFeesMdrCc
     *
     * @param string $ipayDoctorPaymentGatewayFeesMdrCc
     *
     * @return TransactionListing
     */
    public function setIpayDoctorPaymentGatewayFeesMdrCc($ipayDoctorPaymentGatewayFeesMdrCc)
    {
        $this->ipayDoctorPaymentGatewayFeesMdrCc = $ipayDoctorPaymentGatewayFeesMdrCc;

        return $this;
    }

    /**
     * Get ipayDoctorPaymentGatewayFeesMdrCc
     *
     * @return string
     */
    public function getIpayDoctorPaymentGatewayFeesMdrCc()
    {
        return $this->ipayDoctorPaymentGatewayFeesMdrCc;
    }

    /**
     * Set ipayDoctorPaymentGatewayFeesMdrMyclear
     *
     * @param string $ipayDoctorPaymentGatewayFeesMdrMyclear
     *
     * @return TransactionListing
     */
    public function setIpayDoctorPaymentGatewayFeesMdrMyclear($ipayDoctorPaymentGatewayFeesMdrMyclear)
    {
        $this->ipayDoctorPaymentGatewayFeesMdrMyclear = $ipayDoctorPaymentGatewayFeesMdrMyclear;

        return $this;
    }

    /**
     * Get ipayDoctorPaymentGatewayFeesMdrMyclear
     *
     * @return string
     */
    public function getIpayDoctorPaymentGatewayFeesMdrMyclear()
    {
        return $this->ipayDoctorPaymentGatewayFeesMdrMyclear;
    }

    /**
     * Set ipayDoctorPaymentGatewayFeesGst
     *
     * @param string $ipayDoctorPaymentGatewayFeesGst
     *
     * @return TransactionListing
     */
    public function setIpayDoctorPaymentGatewayFeesGst($ipayDoctorPaymentGatewayFeesGst)
    {
        $this->ipayDoctorPaymentGatewayFeesGst = $ipayDoctorPaymentGatewayFeesGst;

        return $this;
    }

    /**
     * Get ipayDoctorPaymentGatewayFeesGst
     *
     * @return string
     */
    public function getIpayDoctorPaymentGatewayFeesGst()
    {
        return $this->ipayDoctorPaymentGatewayFeesGst;
    }

    /**
     * Set doctorMedicineCost
     *
     * @param string $doctorMedicineCost
     *
     * @return TransactionListing
     */
    public function setDoctorMedicineCost($doctorMedicineCost)
    {
        $this->doctorMedicineCost = $doctorMedicineCost;

        return $this;
    }

    /**
     * Get doctorMedicineCost
     *
     * @return string
     */
    public function getDoctorMedicineCost()
    {
        return $this->doctorMedicineCost;
    }

    /**
     * Set doctorGstOnMedicine
     *
     * @param string $doctorGstOnMedicine
     *
     * @return TransactionListing
     */
    public function setDoctorGstOnMedicine($doctorGstOnMedicine)
    {
        $this->doctorGstOnMedicine = $doctorGstOnMedicine;

        return $this;
    }

    /**
     * Get doctorGstOnMedicine
     *
     * @return string
     */
    public function getDoctorGstOnMedicine()
    {
        return $this->doctorGstOnMedicine;
    }

    /**
     * Set doctorNetAmountToBePaid
     *
     * @param string $doctorNetAmountToBePaid
     *
     * @return TransactionListing
     */
    public function setDoctorNetAmountToBePaid($doctorNetAmountToBePaid)
    {
        $this->doctorNetAmountToBePaid = $doctorNetAmountToBePaid;

        return $this;
    }

    /**
     * Get doctorNetAmountToBePaid
     *
     * @return string
     */
    public function getDoctorNetAmountToBePaid()
    {
        return $this->doctorNetAmountToBePaid;
    }

    /**
     * Set doctorDateOfStatement
     *
     * @param \DateTime $doctorDateOfStatement
     *
     * @return TransactionListing
     */
    public function setDoctorDateOfStatement($doctorDateOfStatement)
    {
        $this->doctorDateOfStatement = $doctorDateOfStatement;

        return $this;
    }

    /**
     * Get doctorDateOfStatement
     *
     * @return \DateTime
     */
    public function getDoctorDateOfStatement()
    {
        return $this->doctorDateOfStatement;
    }

    /**
     * Set doctocDateOfInvoiceGmedesToDoctor
     *
     * @param \DateTime $doctocDateOfInvoiceGmedesToDoctor
     *
     * @return TransactionListing
     */
    public function setDoctocDateOfInvoiceGmedesToDoctor($doctocDateOfInvoiceGmedesToDoctor)
    {
        $this->doctocDateOfInvoiceGmedesToDoctor = $doctocDateOfInvoiceGmedesToDoctor;

        return $this;
    }

    /**
     * Get doctocDateOfInvoiceGmedesToDoctor
     *
     * @return \DateTime
     */
    public function getDoctocDateOfInvoiceGmedesToDoctor()
    {
        return $this->doctocDateOfInvoiceGmedesToDoctor;
    }

    /**
     * Set doctorInvoiceNumberFromGmedes
     *
     * @param string $doctorInvoiceNumberFromGmedes
     *
     * @return TransactionListing
     */
    public function setDoctorInvoiceNumberFromGmedes($doctorInvoiceNumberFromGmedes)
    {
        $this->doctorInvoiceNumberFromGmedes = $doctorInvoiceNumberFromGmedes;

        return $this;
    }

    /**
     * Get doctorInvoiceNumberFromGmedes
     *
     * @return string
     */
    public function getDoctorInvoiceNumberFromGmedes()
    {
        return $this->doctorInvoiceNumberFromGmedes;
    }

    /**
     * Set doctorDateOfPayment
     *
     * @param \DateTime $doctorDateOfPayment
     *
     * @return TransactionListing
     */
    public function setDoctorDateOfPayment($doctorDateOfPayment)
    {
        $this->doctorDateOfPayment = $doctorDateOfPayment;

        return $this;
    }

    /**
     * Get doctorDateOfPayment
     *
     * @return \DateTime
     */
    public function getDoctorDateOfPayment()
    {
        return $this->doctorDateOfPayment;
    }

    /**
     * Set doctorTotalAmountPaid
     *
     * @param string $doctorTotalAmountPaid
     *
     * @return TransactionListing
     */
    public function setDoctorTotalAmountPaid($doctorTotalAmountPaid)
    {
        $this->doctorTotalAmountPaid = $doctorTotalAmountPaid;

        return $this;
    }

    /**
     * Get doctorTotalAmountPaid
     *
     * @return string
     */
    public function getDoctorTotalAmountPaid()
    {
        return $this->doctorTotalAmountPaid;
    }

    /**
     * Set doctorPendingToPay
     *
     * @param string $doctorPendingToPay
     *
     * @return TransactionListing
     */
    public function setDoctorPendingToPay($doctorPendingToPay)
    {
        $this->doctorPendingToPay = $doctorPendingToPay;

        return $this;
    }

    /**
     * Get doctorPendingToPay
     *
     * @return string
     */
    public function getDoctorPendingToPay()
    {
        return $this->doctorPendingToPay;
    }

    /**
     * Set agentName
     *
     * @param string $agentName
     *
     * @return TransactionListing
     */
    public function setAgentName($agentName)
    {
        $this->agentName = $agentName;

        return $this;
    }

    /**
     * Get agentName
     *
     * @return string
     */
    public function getAgentName()
    {
        return $this->agentName;
    }

    /**
     * Set agentMarginShareGeneralMedicine
     *
     * @param string $agentMarginShareGeneralMedicine
     *
     * @return TransactionListing
     */
    public function setAgentMarginShareGeneralMedicine($agentMarginShareGeneralMedicine)
    {
        $this->agentMarginShareGeneralMedicine = $agentMarginShareGeneralMedicine;

        return $this;
    }

    /**
     * Get agentMarginShareGeneralMedicine
     *
     * @return string
     */
    public function getAgentMarginShareGeneralMedicine()
    {
        return $this->agentMarginShareGeneralMedicine;
    }

    /**
     * Set agentMarginShareSpecialMedicine
     *
     * @param string $agentMarginShareSpecialMedicine
     *
     * @return TransactionListing
     */
    public function setAgentMarginShareSpecialMedicine($agentMarginShareSpecialMedicine)
    {
        $this->agentMarginShareSpecialMedicine = $agentMarginShareSpecialMedicine;

        return $this;
    }

    /**
     * Get agentMarginShareSpecialMedicine
     *
     * @return string
     */
    public function getAgentMarginShareSpecialMedicine()
    {
        return $this->agentMarginShareSpecialMedicine;
    }

    /**
     * Set agentRevenueShareDoctorFeesConsult
     *
     * @param string $agentRevenueShareDoctorFeesConsult
     *
     * @return TransactionListing
     */
    public function setAgentRevenueShareDoctorFeesConsult($agentRevenueShareDoctorFeesConsult)
    {
        $this->agentRevenueShareDoctorFeesConsult = $agentRevenueShareDoctorFeesConsult;

        return $this;
    }

    /**
     * Get agentRevenueShareDoctorFeesConsult
     *
     * @return string
     */
    public function getAgentRevenueShareDoctorFeesConsult()
    {
        return $this->agentRevenueShareDoctorFeesConsult;
    }

    /**
     * Set agentRevenueShareDoctorFeesReviewRx
     *
     * @param string $agentRevenueShareDoctorFeesReviewRx
     *
     * @return TransactionListing
     */
    public function setAgentRevenueShareDoctorFeesReviewRx($agentRevenueShareDoctorFeesReviewRx)
    {
        $this->agentRevenueShareDoctorFeesReviewRx = $agentRevenueShareDoctorFeesReviewRx;

        return $this;
    }

    /**
     * Get agentRevenueShareDoctorFeesReviewRx
     *
     * @return string
     */
    public function getAgentRevenueShareDoctorFeesReviewRx()
    {
        return $this->agentRevenueShareDoctorFeesReviewRx;
    }

    /**
     * Set agentFeesTotalBeforeGst
     *
     * @param string $agentFeesTotalBeforeGst
     *
     * @return TransactionListing
     */
    public function setAgentFeesTotalBeforeGst($agentFeesTotalBeforeGst)
    {
        $this->agentFeesTotalBeforeGst = $agentFeesTotalBeforeGst;

        return $this;
    }

    /**
     * Get agentFeesTotalBeforeGst
     *
     * @return string
     */
    public function getAgentFeesTotalBeforeGst()
    {
        return $this->agentFeesTotalBeforeGst;
    }

    /**
     * Set agentGstAmountOnAgentFees
     *
     * @param string $agentGstAmountOnAgentFees
     *
     * @return TransactionListing
     */
    public function setAgentGstAmountOnAgentFees($agentGstAmountOnAgentFees)
    {
        $this->agentGstAmountOnAgentFees = $agentGstAmountOnAgentFees;

        return $this;
    }

    /**
     * Get agentGstAmountOnAgentFees
     *
     * @return string
     */
    public function getAgentGstAmountOnAgentFees()
    {
        return $this->agentGstAmountOnAgentFees;
    }

    /**
     * Set agentFeesAfterGst
     *
     * @param string $agentFeesAfterGst
     *
     * @return TransactionListing
     */
    public function setAgentFeesAfterGst($agentFeesAfterGst)
    {
        $this->agentFeesAfterGst = $agentFeesAfterGst;

        return $this;
    }

    /**
     * Get agentFeesAfterGst
     *
     * @return string
     */
    public function getAgentFeesAfterGst()
    {
        return $this->agentFeesAfterGst;
    }

    /**
     * Set agentDateOfAgentStatement
     *
     * @param \DateTime $agentDateOfAgentStatement
     *
     * @return TransactionListing
     */
    public function setAgentDateOfAgentStatement($agentDateOfAgentStatement)
    {
        $this->agentDateOfAgentStatement = $agentDateOfAgentStatement;

        return $this;
    }

    /**
     * Get agentDateOfAgentStatement
     *
     * @return \DateTime
     */
    public function getAgentDateOfAgentStatement()
    {
        return $this->agentDateOfAgentStatement;
    }

    /**
     * Set agentInvoiceNumber
     *
     * @param string $agentInvoiceNumber
     *
     * @return TransactionListing
     */
    public function setAgentInvoiceNumber($agentInvoiceNumber)
    {
        $this->agentInvoiceNumber = $agentInvoiceNumber;

        return $this;
    }

    /**
     * Get agentInvoiceNumber
     *
     * @return string
     */
    public function getAgentInvoiceNumber()
    {
        return $this->agentInvoiceNumber;
    }

    /**
     * Set agentInvoiceDate
     *
     * @param \DateTime $agentInvoiceDate
     *
     * @return TransactionListing
     */
    public function setAgentInvoiceDate($agentInvoiceDate)
    {
        $this->agentInvoiceDate = $agentInvoiceDate;

        return $this;
    }

    /**
     * Get agentInvoiceDate
     *
     * @return \DateTime
     */
    public function getAgentInvoiceDate()
    {
        return $this->agentInvoiceDate;
    }

    /**
     * Set agentDateOfPayment
     *
     * @param \DateTime $agentDateOfPayment
     *
     * @return TransactionListing
     */
    public function setAgentDateOfPayment($agentDateOfPayment)
    {
        $this->agentDateOfPayment = $agentDateOfPayment;

        return $this;
    }

    /**
     * Get agentDateOfPayment
     *
     * @return \DateTime
     */
    public function getAgentDateOfPayment()
    {
        return $this->agentDateOfPayment;
    }

    /**
     * Set agentTotalAmountPaid
     *
     * @param string $agentTotalAmountPaid
     *
     * @return TransactionListing
     */
    public function setAgentTotalAmountPaid($agentTotalAmountPaid)
    {
        $this->agentTotalAmountPaid = $agentTotalAmountPaid;

        return $this;
    }

    /**
     * Get agentTotalAmountPaid
     *
     * @return string
     */
    public function getAgentTotalAmountPaid()
    {
        return $this->agentTotalAmountPaid;
    }

    /**
     * Set agentPendingToPay
     *
     * @param string $agentPendingToPay
     *
     * @return TransactionListing
     */
    public function setAgentPendingToPay($agentPendingToPay)
    {
        $this->agentPendingToPay = $agentPendingToPay;

        return $this;
    }

    /**
     * Get agentPendingToPay
     *
     * @return string
     */
    public function getAgentPendingToPay()
    {
        return $this->agentPendingToPay;
    }

    /**
     * Set gmedesMarginShareGeneralMedicine
     *
     * @param string $gmedesMarginShareGeneralMedicine
     *
     * @return TransactionListing
     */
    public function setGmedesMarginShareGeneralMedicine($gmedesMarginShareGeneralMedicine)
    {
        $this->gmedesMarginShareGeneralMedicine = $gmedesMarginShareGeneralMedicine;

        return $this;
    }

    /**
     * Get gmedesMarginShareGeneralMedicine
     *
     * @return string
     */
    public function getGmedesMarginShareGeneralMedicine()
    {
        return $this->gmedesMarginShareGeneralMedicine;
    }

    /**
     * Set gmedesMarginShareSpecialMedicine
     *
     * @param string $gmedesMarginShareSpecialMedicine
     *
     * @return TransactionListing
     */
    public function setGmedesMarginShareSpecialMedicine($gmedesMarginShareSpecialMedicine)
    {
        $this->gmedesMarginShareSpecialMedicine = $gmedesMarginShareSpecialMedicine;

        return $this;
    }

    /**
     * Get gmedesMarginShareSpecialMedicine
     *
     * @return string
     */
    public function getGmedesMarginShareSpecialMedicine()
    {
        return $this->gmedesMarginShareSpecialMedicine;
    }

    /**
     * Set gmedesRevenueShareDoctorFeesConsult
     *
     * @param string $gmedesRevenueShareDoctorFeesConsult
     *
     * @return TransactionListing
     */
    public function setGmedesRevenueShareDoctorFeesConsult($gmedesRevenueShareDoctorFeesConsult)
    {
        $this->gmedesRevenueShareDoctorFeesConsult = $gmedesRevenueShareDoctorFeesConsult;

        return $this;
    }

    /**
     * Get gmedesRevenueShareDoctorFeesConsult
     *
     * @return string
     */
    public function getGmedesRevenueShareDoctorFeesConsult()
    {
        return $this->gmedesRevenueShareDoctorFeesConsult;
    }

    /**
     * Set gmedesRevenueShareDoctorFeesReviewRx
     *
     * @param string $gmedesRevenueShareDoctorFeesReviewRx
     *
     * @return TransactionListing
     */
    public function setGmedesRevenueShareDoctorFeesReviewRx($gmedesRevenueShareDoctorFeesReviewRx)
    {
        $this->gmedesRevenueShareDoctorFeesReviewRx = $gmedesRevenueShareDoctorFeesReviewRx;

        return $this;
    }

    /**
     * Get gmedesRevenueShareDoctorFeesReviewRx
     *
     * @return string
     */
    public function getGmedesRevenueShareDoctorFeesReviewRx()
    {
        return $this->gmedesRevenueShareDoctorFeesReviewRx;
    }

    /**
     * Set gmedesRevenueShareOverseas
     *
     * @param string $gmedesRevenueShareOverseas
     *
     * @return TransactionListing
     */
    public function setGmedesRevenueShareOverseas($gmedesRevenueShareOverseas)
    {
        $this->gmedesRevenueShareOverseas = $gmedesRevenueShareOverseas;

        return $this;
    }

    /**
     * Get gmedesRevenueShareOverseas
     *
     * @return string
     */
    public function getGmedesRevenueShareOverseas()
    {
        return $this->gmedesRevenueShareOverseas;
    }

    /**
     * Set gmedesTotalEarnings
     *
     * @param string $gmedesTotalEarnings
     *
     * @return TransactionListing
     */
    public function setGmedesTotalEarnings($gmedesTotalEarnings)
    {
        $this->gmedesTotalEarnings = $gmedesTotalEarnings;

        return $this;
    }

    /**
     * Get gmedesTotalEarnings
     *
     * @return string
     */
    public function getGmedesTotalEarnings()
    {
        return $this->gmedesTotalEarnings;
    }

    /**
     * Set pharmacyCostOfSalesGeneralMedicine
     *
     * @param string $pharmacyCostOfSalesGeneralMedicine
     *
     * @return TransactionListing
     */
    public function setPharmacyCostOfSalesGeneralMedicine($pharmacyCostOfSalesGeneralMedicine)
    {
        $this->pharmacyCostOfSalesGeneralMedicine = $pharmacyCostOfSalesGeneralMedicine;

        return $this;
    }

    /**
     * Get pharmacyCostOfSalesGeneralMedicine
     *
     * @return string
     */
    public function getPharmacyCostOfSalesGeneralMedicine()
    {
        return $this->pharmacyCostOfSalesGeneralMedicine;
    }

    /**
     * Set pharmacyCostOfSalesSpecialMedicine
     *
     * @param string $pharmacyCostOfSalesSpecialMedicine
     *
     * @return TransactionListing
     */
    public function setPharmacyCostOfSalesSpecialMedicine($pharmacyCostOfSalesSpecialMedicine)
    {
        $this->pharmacyCostOfSalesSpecialMedicine = $pharmacyCostOfSalesSpecialMedicine;

        return $this;
    }

    /**
     * Get pharmacyCostOfSalesSpecialMedicine
     *
     * @return string
     */
    public function getPharmacyCostOfSalesSpecialMedicine()
    {
        return $this->pharmacyCostOfSalesSpecialMedicine;
    }

    /**
     * Set pharmacyGstAmount
     *
     * @param string $pharmacyGstAmount
     *
     * @return TransactionListing
     */
    public function setPharmacyGstAmount($pharmacyGstAmount)
    {
        $this->pharmacyGstAmount = $pharmacyGstAmount;

        return $this;
    }

    /**
     * Get pharmacyGstAmount
     *
     * @return string
     */
    public function getPharmacyGstAmount()
    {
        return $this->pharmacyGstAmount;
    }

    /**
     * Set pharmacyPaymentGatewayFeesMdrCc
     *
     * @param string $pharmacyPaymentGatewayFeesMdrCc
     *
     * @return TransactionListing
     */
    public function setPharmacyPaymentGatewayFeesMdrCc($pharmacyPaymentGatewayFeesMdrCc)
    {
        $this->pharmacyPaymentGatewayFeesMdrCc = $pharmacyPaymentGatewayFeesMdrCc;

        return $this;
    }

    /**
     * Get pharmacyPaymentGatewayFeesMdrCc
     *
     * @return string
     */
    public function getPharmacyPaymentGatewayFeesMdrCc()
    {
        return $this->pharmacyPaymentGatewayFeesMdrCc;
    }

    /**
     * Set pharmacyPaymentGatewayFeesMdrMyclear
     *
     * @param string $pharmacyPaymentGatewayFeesMdrMyclear
     *
     * @return TransactionListing
     */
    public function setPharmacyPaymentGatewayFeesMdrMyclear($pharmacyPaymentGatewayFeesMdrMyclear)
    {
        $this->pharmacyPaymentGatewayFeesMdrMyclear = $pharmacyPaymentGatewayFeesMdrMyclear;

        return $this;
    }

    /**
     * Get pharmacyPaymentGatewayFeesMdrMyclear
     *
     * @return string
     */
    public function getPharmacyPaymentGatewayFeesMdrMyclear()
    {
        return $this->pharmacyPaymentGatewayFeesMdrMyclear;
    }

    /**
     * Set pharmacyPaymentGatewayFeesGst
     *
     * @param string $pharmacyPaymentGatewayFeesGst
     *
     * @return TransactionListing
     */
    public function setPharmacyPaymentGatewayFeesGst($pharmacyPaymentGatewayFeesGst)
    {
        $this->pharmacyPaymentGatewayFeesGst = $pharmacyPaymentGatewayFeesGst;

        return $this;
    }

    /**
     * Get pharmacyPaymentGatewayFeesGst
     *
     * @return string
     */
    public function getPharmacyPaymentGatewayFeesGst()
    {
        return $this->pharmacyPaymentGatewayFeesGst;
    }

    /**
     * Set ipayPharmacyPaymentGatewayFeesMdrCc
     *
     * @param string $ipayPharmacyPaymentGatewayFeesMdrCc
     *
     * @return TransactionListing
     */
    public function setIpayPharmacyPaymentGatewayFeesMdrCc($ipayPharmacyPaymentGatewayFeesMdrCc)
    {
        $this->ipayPharmacyPaymentGatewayFeesMdrCc = $ipayPharmacyPaymentGatewayFeesMdrCc;

        return $this;
    }

    /**
     * Get ipayPharmacyPaymentGatewayFeesMdrCc
     *
     * @return string
     */
    public function getIpayPharmacyPaymentGatewayFeesMdrCc()
    {
        return $this->ipayPharmacyPaymentGatewayFeesMdrCc;
    }

    /**
     * Set ipayPharmacyPaymentGatewayFeesMdrMyclear
     *
     * @param string $ipayPharmacyPaymentGatewayFeesMdrMyclear
     *
     * @return TransactionListing
     */
    public function setIpayPharmacyPaymentGatewayFeesMdrMyclear($ipayPharmacyPaymentGatewayFeesMdrMyclear)
    {
        $this->ipayPharmacyPaymentGatewayFeesMdrMyclear = $ipayPharmacyPaymentGatewayFeesMdrMyclear;

        return $this;
    }

    /**
     * Get ipayPharmacyPaymentGatewayFeesMdrMyclear
     *
     * @return string
     */
    public function getIpayPharmacyPaymentGatewayFeesMdrMyclear()
    {
        return $this->ipayPharmacyPaymentGatewayFeesMdrMyclear;
    }

    /**
     * Set ipayPharmacyPaymentGatewayFeesGst
     *
     * @param string $ipayPharmacyPaymentGatewayFeesGst
     *
     * @return TransactionListing
     */
    public function setIpayPharmacyPaymentGatewayFeesGst($ipayPharmacyPaymentGatewayFeesGst)
    {
        $this->ipayPharmacyPaymentGatewayFeesGst = $ipayPharmacyPaymentGatewayFeesGst;

        return $this;
    }

    /**
     * Get ipayPharmacyPaymentGatewayFeesGst
     *
     * @return string
     */
    public function getIpayPharmacyPaymentGatewayFeesGst()
    {
        return $this->ipayPharmacyPaymentGatewayFeesGst;
    }

    /**
     * Set pharmacyShippingAndHandling
     *
     * @param string $pharmacyShippingAndHandling
     *
     * @return TransactionListing
     */
    public function setPharmacyShippingAndHandling($pharmacyShippingAndHandling)
    {
        $this->pharmacyShippingAndHandling = $pharmacyShippingAndHandling;

        return $this;
    }

    /**
     * Get pharmacyShippingAndHandling
     *
     * @return string
     */
    public function getPharmacyShippingAndHandling()
    {
        return $this->pharmacyShippingAndHandling;
    }

    /**
     * Set pharmacyGstOnShipping
     *
     * @param string $pharmacyGstOnShipping
     *
     * @return TransactionListing
     */
    public function setPharmacyGstOnShipping($pharmacyGstOnShipping)
    {
        $this->pharmacyGstOnShipping = $pharmacyGstOnShipping;

        return $this;
    }

    /**
     * Get pharmacyGstOnShipping
     *
     * @return string
     */
    public function getPharmacyGstOnShipping()
    {
        return $this->pharmacyGstOnShipping;
    }

    /**
     * Set pharmacyImportTaxIndoOverseas
     *
     * @param string $pharmacyImportTaxIndoOverseas
     *
     * @return TransactionListing
     */
    public function setPharmacyImportTaxIndoOverseas($pharmacyImportTaxIndoOverseas)
    {
        $this->pharmacyImportTaxIndoOverseas = $pharmacyImportTaxIndoOverseas;

        return $this;
    }

    /**
     * Get pharmacyImportTaxIndoOverseas
     *
     * @return string
     */
    public function getPharmacyImportTaxIndoOverseas()
    {
        return $this->pharmacyImportTaxIndoOverseas;
    }

    /**
     * Set pharmacyImportTaxSgOverseas
     *
     * @param string $pharmacyImportTaxSgOverseas
     *
     * @return TransactionListing
     */
    public function setPharmacyImportTaxSgOverseas($pharmacyImportTaxSgOverseas)
    {
        $this->pharmacyImportTaxSgOverseas = $pharmacyImportTaxSgOverseas;

        return $this;
    }

    /**
     * Get pharmacyImportTaxSgOverseas
     *
     * @return string
     */
    public function getPharmacyImportTaxSgOverseas()
    {
        return $this->pharmacyImportTaxSgOverseas;
    }

    /**
     * Set pharmacyFeesCustomClearanceAdminFeeIndoOverseas
     *
     * @param string $pharmacyFeesCustomClearanceAdminFeeIndoOverseas
     *
     * @return TransactionListing
     */
    public function setPharmacyFeesCustomClearanceAdminFeeIndoOverseas($pharmacyFeesCustomClearanceAdminFeeIndoOverseas)
    {
        $this->pharmacyFeesCustomClearanceAdminFeeIndoOverseas = $pharmacyFeesCustomClearanceAdminFeeIndoOverseas;

        return $this;
    }

    /**
     * Get pharmacyFeesCustomClearanceAdminFeeIndoOverseas
     *
     * @return string
     */
    public function getPharmacyFeesCustomClearanceAdminFeeIndoOverseas()
    {
        return $this->pharmacyFeesCustomClearanceAdminFeeIndoOverseas;
    }

    /**
     * Set pharmacyFeesCustomClearanceAdminFeeSgOverseas
     *
     * @param string $pharmacyFeesCustomClearanceAdminFeeSgOverseas
     *
     * @return TransactionListing
     */
    public function setPharmacyFeesCustomClearanceAdminFeeSgOverseas($pharmacyFeesCustomClearanceAdminFeeSgOverseas)
    {
        $this->pharmacyFeesCustomClearanceAdminFeeSgOverseas = $pharmacyFeesCustomClearanceAdminFeeSgOverseas;

        return $this;
    }

    /**
     * Get pharmacyFeesCustomClearanceAdminFeeSgOverseas
     *
     * @return string
     */
    public function getPharmacyFeesCustomClearanceAdminFeeSgOverseas()
    {
        return $this->pharmacyFeesCustomClearanceAdminFeeSgOverseas;
    }

    /**
     * Set pharmacyCustomIgPermitFeeSg
     *
     * @param string $pharmacyCustomIgPermitFeeSg
     *
     * @return TransactionListing
     */
    public function setPharmacyCustomIgPermitFeeSg($pharmacyCustomIgPermitFeeSg)
    {
        $this->pharmacyCustomIgPermitFeeSg = $pharmacyCustomIgPermitFeeSg;

        return $this;
    }

    /**
     * Get pharmacyCustomIgPermitFeeSg
     *
     * @return string
     */
    public function getPharmacyCustomIgPermitFeeSg()
    {
        return $this->pharmacyCustomIgPermitFeeSg;
    }

    /**
     * Set pharmacyTotalAmountPayable
     *
     * @param string $pharmacyTotalAmountPayable
     *
     * @return TransactionListing
     */
    public function setPharmacyTotalAmountPayable($pharmacyTotalAmountPayable)
    {
        $this->pharmacyTotalAmountPayable = $pharmacyTotalAmountPayable;

        return $this;
    }

    /**
     * Get pharmacyTotalAmountPayable
     *
     * @return string
     */
    public function getPharmacyTotalAmountPayable()
    {
        return $this->pharmacyTotalAmountPayable;
    }

    /**
     * Set pharmacyDailyPoDate
     *
     * @param \DateTime $pharmacyDailyPoDate
     *
     * @return TransactionListing
     */
    public function setPharmacyDailyPoDate($pharmacyDailyPoDate)
    {
        $this->pharmacyDailyPoDate = $pharmacyDailyPoDate;

        return $this;
    }

    /**
     * Get pharmacyDailyPoDate
     *
     * @return \DateTime
     */
    public function getPharmacyDailyPoDate()
    {
        return $this->pharmacyDailyPoDate;
    }

    /**
     * Set pharmacyWeeklyPoDate
     *
     * @param \DateTime $pharmacyWeeklyPoDate
     *
     * @return TransactionListing
     */
    public function setPharmacyWeeklyPoDate($pharmacyWeeklyPoDate)
    {
        $this->pharmacyWeeklyPoDate = $pharmacyWeeklyPoDate;

        return $this;
    }

    /**
     * Get pharmacyWeeklyPoDate
     *
     * @return \DateTime
     */
    public function getPharmacyWeeklyPoDate()
    {
        return $this->pharmacyWeeklyPoDate;
    }

    /**
     * Set pharmacyWeeklyTaxInvoiceNumber
     *
     * @param string $pharmacyWeeklyTaxInvoiceNumber
     *
     * @return TransactionListing
     */
    public function setPharmacyWeeklyTaxInvoiceNumber($pharmacyWeeklyTaxInvoiceNumber)
    {
        $this->pharmacyWeeklyTaxInvoiceNumber = $pharmacyWeeklyTaxInvoiceNumber;

        return $this;
    }

    /**
     * Get pharmacyWeeklyTaxInvoiceNumber
     *
     * @return string
     */
    public function getPharmacyWeeklyTaxInvoiceNumber()
    {
        return $this->pharmacyWeeklyTaxInvoiceNumber;
    }

    /**
     * Set pharmacyWeeklyTaxInvoiceDate
     *
     * @param \DateTime $pharmacyWeeklyTaxInvoiceDate
     *
     * @return TransactionListing
     */
    public function setPharmacyWeeklyTaxInvoiceDate($pharmacyWeeklyTaxInvoiceDate)
    {
        $this->pharmacyWeeklyTaxInvoiceDate = $pharmacyWeeklyTaxInvoiceDate;

        return $this;
    }

    /**
     * Get pharmacyWeeklyTaxInvoiceDate
     *
     * @return \DateTime
     */
    public function getPharmacyWeeklyTaxInvoiceDate()
    {
        return $this->pharmacyWeeklyTaxInvoiceDate;
    }

    /**
     * Set pharmacyDateOfPayment
     *
     * @param \DateTime $pharmacyDateOfPayment
     *
     * @return TransactionListing
     */
    public function setPharmacyDateOfPayment($pharmacyDateOfPayment)
    {
        $this->pharmacyDateOfPayment = $pharmacyDateOfPayment;

        return $this;
    }

    /**
     * Get pharmacyDateOfPayment
     *
     * @return \DateTime
     */
    public function getPharmacyDateOfPayment()
    {
        return $this->pharmacyDateOfPayment;
    }

    /**
     * Set pharmacyTotalAmountPaid
     *
     * @param string $pharmacyTotalAmountPaid
     *
     * @return TransactionListing
     */
    public function setPharmacyTotalAmountPaid($pharmacyTotalAmountPaid)
    {
        $this->pharmacyTotalAmountPaid = $pharmacyTotalAmountPaid;

        return $this;
    }

    /**
     * Get pharmacyTotalAmountPaid
     *
     * @return string
     */
    public function getPharmacyTotalAmountPaid()
    {
        return $this->pharmacyTotalAmountPaid;
    }

    /**
     * Set pharmacyPendingToPayPharmacy
     *
     * @param string $pharmacyPendingToPayPharmacy
     *
     * @return TransactionListing
     */
    public function setPharmacyPendingToPayPharmacy($pharmacyPendingToPayPharmacy)
    {
        $this->pharmacyPendingToPayPharmacy = $pharmacyPendingToPayPharmacy;

        return $this;
    }

    /**
     * Get pharmacyPendingToPayPharmacy
     *
     * @return string
     */
    public function getPharmacyPendingToPayPharmacy()
    {
        return $this->pharmacyPendingToPayPharmacy;
    }

    /**
     * Set logisticsShippingAndHandling
     *
     * @param string $logisticsShippingAndHandling
     *
     * @return TransactionListing
     */
    public function setLogisticsShippingAndHandling($logisticsShippingAndHandling)
    {
        $this->logisticsShippingAndHandling = $logisticsShippingAndHandling;

        return $this;
    }

    /**
     * Get logisticsShippingAndHandling
     *
     * @return string
     */
    public function getLogisticsShippingAndHandling()
    {
        return $this->logisticsShippingAndHandling;
    }

    /**
     * Set logisticsImportTaxIndoOverseas
     *
     * @param string $logisticsImportTaxIndoOverseas
     *
     * @return TransactionListing
     */
    public function setLogisticsImportTaxIndoOverseas($logisticsImportTaxIndoOverseas)
    {
        $this->logisticsImportTaxIndoOverseas = $logisticsImportTaxIndoOverseas;

        return $this;
    }

    /**
     * Get logisticsImportTaxIndoOverseas
     *
     * @return string
     */
    public function getLogisticsImportTaxIndoOverseas()
    {
        return $this->logisticsImportTaxIndoOverseas;
    }

    /**
     * Set logisticsImportTaxSgOverseas
     *
     * @param string $logisticsImportTaxSgOverseas
     *
     * @return TransactionListing
     */
    public function setLogisticsImportTaxSgOverseas($logisticsImportTaxSgOverseas)
    {
        $this->logisticsImportTaxSgOverseas = $logisticsImportTaxSgOverseas;

        return $this;
    }

    /**
     * Get logisticsImportTaxSgOverseas
     *
     * @return string
     */
    public function getLogisticsImportTaxSgOverseas()
    {
        return $this->logisticsImportTaxSgOverseas;
    }

    /**
     * Set logisticsCustomIgPermitFeeSg
     *
     * @param string $logisticsCustomIgPermitFeeSg
     *
     * @return TransactionListing
     */
    public function setLogisticsCustomIgPermitFeeSg($logisticsCustomIgPermitFeeSg)
    {
        $this->logisticsCustomIgPermitFeeSg = $logisticsCustomIgPermitFeeSg;

        return $this;
    }

    /**
     * Get logisticsCustomIgPermitFeeSg
     *
     * @return string
     */
    public function getLogisticsCustomIgPermitFeeSg()
    {
        return $this->logisticsCustomIgPermitFeeSg;
    }

    /**
     * Set logisticsPaymentGatewayFeesMdrCc
     *
     * @param string $logisticsPaymentGatewayFeesMdrCc
     *
     * @return TransactionListing
     */
    public function setLogisticsPaymentGatewayFeesMdrCc($logisticsPaymentGatewayFeesMdrCc)
    {
        $this->logisticsPaymentGatewayFeesMdrCc = $logisticsPaymentGatewayFeesMdrCc;

        return $this;
    }

    /**
     * Get logisticsPaymentGatewayFeesMdrCc
     *
     * @return string
     */
    public function getLogisticsPaymentGatewayFeesMdrCc()
    {
        return $this->logisticsPaymentGatewayFeesMdrCc;
    }

    /**
     * Set logisticsPaymentGatewayFeesMdrMyclear
     *
     * @param string $logisticsPaymentGatewayFeesMdrMyclear
     *
     * @return TransactionListing
     */
    public function setLogisticsPaymentGatewayFeesMdrMyclear($logisticsPaymentGatewayFeesMdrMyclear)
    {
        $this->logisticsPaymentGatewayFeesMdrMyclear = $logisticsPaymentGatewayFeesMdrMyclear;

        return $this;
    }

    /**
     * Get logisticsPaymentGatewayFeesMdrMyclear
     *
     * @return string
     */
    public function getLogisticsPaymentGatewayFeesMdrMyclear()
    {
        return $this->logisticsPaymentGatewayFeesMdrMyclear;
    }

    /**
     * Set logisticsPaymentGatewayFeesGst
     *
     * @param string $logisticsPaymentGatewayFeesGst
     *
     * @return TransactionListing
     */
    public function setLogisticsPaymentGatewayFeesGst($logisticsPaymentGatewayFeesGst)
    {
        $this->logisticsPaymentGatewayFeesGst = $logisticsPaymentGatewayFeesGst;

        return $this;
    }

    /**
     * Get logisticsPaymentGatewayFeesGst
     *
     * @return string
     */
    public function getLogisticsPaymentGatewayFeesGst()
    {
        return $this->logisticsPaymentGatewayFeesGst;
    }

    /**
     * Set ipayLogisticsPaymentGatewayFeesMdrCc
     *
     * @param string $ipayLogisticsPaymentGatewayFeesMdrCc
     *
     * @return TransactionListing
     */
    public function setIpayLogisticsPaymentGatewayFeesMdrCc($ipayLogisticsPaymentGatewayFeesMdrCc)
    {
        $this->ipayLogisticsPaymentGatewayFeesMdrCc = $ipayLogisticsPaymentGatewayFeesMdrCc;

        return $this;
    }

    /**
     * Get ipayLogisticsPaymentGatewayFeesMdrCc
     *
     * @return string
     */
    public function getIpayLogisticsPaymentGatewayFeesMdrCc()
    {
        return $this->ipayLogisticsPaymentGatewayFeesMdrCc;
    }

    /**
     * Set ipayLogisticsPaymentGatewayFeesMdrMyclear
     *
     * @param string $ipayLogisticsPaymentGatewayFeesMdrMyclear
     *
     * @return TransactionListing
     */
    public function setIpayLogisticsPaymentGatewayFeesMdrMyclear($ipayLogisticsPaymentGatewayFeesMdrMyclear)
    {
        $this->ipayLogisticsPaymentGatewayFeesMdrMyclear = $ipayLogisticsPaymentGatewayFeesMdrMyclear;

        return $this;
    }

    /**
     * Get ipayLogisticsPaymentGatewayFeesMdrMyclear
     *
     * @return string
     */
    public function getIpayLogisticsPaymentGatewayFeesMdrMyclear()
    {
        return $this->ipayLogisticsPaymentGatewayFeesMdrMyclear;
    }

    /**
     * Set ipayLogisticsPaymentGatewayFeesGst
     *
     * @param string $ipayLogisticsPaymentGatewayFeesGst
     *
     * @return TransactionListing
     */
    public function setIpayLogisticsPaymentGatewayFeesGst($ipayLogisticsPaymentGatewayFeesGst)
    {
        $this->ipayLogisticsPaymentGatewayFeesGst = $ipayLogisticsPaymentGatewayFeesGst;

        return $this;
    }

    /**
     * Get ipayLogisticsPaymentGatewayFeesGst
     *
     * @return string
     */
    public function getIpayLogisticsPaymentGatewayFeesGst()
    {
        return $this->ipayLogisticsPaymentGatewayFeesGst;
    }

    /**
     * Set logisticsMedicineCosts
     *
     * @param string $logisticsMedicineCosts
     *
     * @return TransactionListing
     */
    public function setLogisticsMedicineCosts($logisticsMedicineCosts)
    {
        $this->logisticsMedicineCosts = $logisticsMedicineCosts;

        return $this;
    }

    /**
     * Get logisticsMedicineCosts
     *
     * @return string
     */
    public function getLogisticsMedicineCosts()
    {
        return $this->logisticsMedicineCosts;
    }

    /**
     * Set logisticsGstOnMedicine
     *
     * @param string $logisticsGstOnMedicine
     *
     * @return TransactionListing
     */
    public function setLogisticsGstOnMedicine($logisticsGstOnMedicine)
    {
        $this->logisticsGstOnMedicine = $logisticsGstOnMedicine;

        return $this;
    }

    /**
     * Get logisticsGstOnMedicine
     *
     * @return string
     */
    public function getLogisticsGstOnMedicine()
    {
        return $this->logisticsGstOnMedicine;
    }

    /**
     * Set logisticsGst
     *
     * @param string $logisticsGst
     *
     * @return TransactionListing
     */
    public function setLogisticsGst($logisticsGst)
    {
        $this->logisticsGst = $logisticsGst;

        return $this;
    }

    /**
     * Get logisticsGst
     *
     * @return string
     */
    public function getLogisticsGst()
    {
        return $this->logisticsGst;
    }

    /**
     * Set logisticsAmountPayable
     *
     * @param string $logisticsAmountPayable
     *
     * @return TransactionListing
     */
    public function setLogisticsAmountPayable($logisticsAmountPayable)
    {
        $this->logisticsAmountPayable = $logisticsAmountPayable;

        return $this;
    }

    /**
     * Get logisticsAmountPayable
     *
     * @return string
     */
    public function getLogisticsAmountPayable()
    {
        return $this->logisticsAmountPayable;
    }

    /**
     * Set logisticsDailyPurchaseOrderDate
     *
     * @param \DateTime $logisticsDailyPurchaseOrderDate
     *
     * @return TransactionListing
     */
    public function setLogisticsDailyPurchaseOrderDate($logisticsDailyPurchaseOrderDate)
    {
        $this->logisticsDailyPurchaseOrderDate = $logisticsDailyPurchaseOrderDate;

        return $this;
    }

    /**
     * Get logisticsDailyPurchaseOrderDate
     *
     * @return \DateTime
     */
    public function getLogisticsDailyPurchaseOrderDate()
    {
        return $this->logisticsDailyPurchaseOrderDate;
    }

    /**
     * Set logisticsWeeklyPurchaseOrderDate
     *
     * @param \DateTime $logisticsWeeklyPurchaseOrderDate
     *
     * @return TransactionListing
     */
    public function setLogisticsWeeklyPurchaseOrderDate($logisticsWeeklyPurchaseOrderDate)
    {
        $this->logisticsWeeklyPurchaseOrderDate = $logisticsWeeklyPurchaseOrderDate;

        return $this;
    }

    /**
     * Get logisticsWeeklyPurchaseOrderDate
     *
     * @return \DateTime
     */
    public function getLogisticsWeeklyPurchaseOrderDate()
    {
        return $this->logisticsWeeklyPurchaseOrderDate;
    }

    /**
     * Set logisticsInvoiceNumber
     *
     * @param string $logisticsInvoiceNumber
     *
     * @return TransactionListing
     */
    public function setLogisticsInvoiceNumber($logisticsInvoiceNumber)
    {
        $this->logisticsInvoiceNumber = $logisticsInvoiceNumber;

        return $this;
    }

    /**
     * Get logisticsInvoiceNumber
     *
     * @return string
     */
    public function getLogisticsInvoiceNumber()
    {
        return $this->logisticsInvoiceNumber;
    }

    /**
     * Set logisticsInvoiceDate
     *
     * @param \DateTime $logisticsInvoiceDate
     *
     * @return TransactionListing
     */
    public function setLogisticsInvoiceDate($logisticsInvoiceDate)
    {
        $this->logisticsInvoiceDate = $logisticsInvoiceDate;

        return $this;
    }

    /**
     * Get logisticsInvoiceDate
     *
     * @return \DateTime
     */
    public function getLogisticsInvoiceDate()
    {
        return $this->logisticsInvoiceDate;
    }

    /**
     * Set logisticsDateOfPayment
     *
     * @param \DateTime $logisticsDateOfPayment
     *
     * @return TransactionListing
     */
    public function setLogisticsDateOfPayment($logisticsDateOfPayment)
    {
        $this->logisticsDateOfPayment = $logisticsDateOfPayment;

        return $this;
    }

    /**
     * Get logisticsDateOfPayment
     *
     * @return \DateTime
     */
    public function getLogisticsDateOfPayment()
    {
        return $this->logisticsDateOfPayment;
    }

    /**
     * Set logisticsTotalAmountPaid
     *
     * @param string $logisticsTotalAmountPaid
     *
     * @return TransactionListing
     */
    public function setLogisticsTotalAmountPaid($logisticsTotalAmountPaid)
    {
        $this->logisticsTotalAmountPaid = $logisticsTotalAmountPaid;

        return $this;
    }

    /**
     * Get logisticsTotalAmountPaid
     *
     * @return string
     */
    public function getLogisticsTotalAmountPaid()
    {
        return $this->logisticsTotalAmountPaid;
    }

    /**
     * Set logisticsPendingToPayLogistics
     *
     * @param string $logisticsPendingToPayLogistics
     *
     * @return TransactionListing
     */
    public function setLogisticsPendingToPayLogistics($logisticsPendingToPayLogistics)
    {
        $this->logisticsPendingToPayLogistics = $logisticsPendingToPayLogistics;

        return $this;
    }

    /**
     * Get logisticsPendingToPayLogistics
     *
     * @return string
     */
    public function getLogisticsPendingToPayLogistics()
    {
        return $this->logisticsPendingToPayLogistics;
    }

    /**
     * Set customIndoOverseasBmImportDuty
     *
     * @param string $customIndoOverseasBmImportDuty
     *
     * @return TransactionListing
     */
    public function setCustomIndoOverseasBmImportDuty($customIndoOverseasBmImportDuty)
    {
        $this->customIndoOverseasBmImportDuty = $customIndoOverseasBmImportDuty;

        return $this;
    }

    /**
     * Get customIndoOverseasBmImportDuty
     *
     * @return string
     */
    public function getCustomIndoOverseasBmImportDuty()
    {
        return $this->customIndoOverseasBmImportDuty;
    }

    /**
     * Set customIndoOverseasPpnVat
     *
     * @param string $customIndoOverseasPpnVat
     *
     * @return TransactionListing
     */
    public function setCustomIndoOverseasPpnVat($customIndoOverseasPpnVat)
    {
        $this->customIndoOverseasPpnVat = $customIndoOverseasPpnVat;

        return $this;
    }

    /**
     * Get customIndoOverseasPpnVat
     *
     * @return string
     */
    public function getCustomIndoOverseasPpnVat()
    {
        return $this->customIndoOverseasPpnVat;
    }

    /**
     * Set customIndoOverseasPphTaxWithoutId
     *
     * @param string $customIndoOverseasPphTaxWithoutId
     *
     * @return TransactionListing
     */
    public function setCustomIndoOverseasPphTaxWithoutId($customIndoOverseasPphTaxWithoutId)
    {
        $this->customIndoOverseasPphTaxWithoutId = $customIndoOverseasPphTaxWithoutId;

        return $this;
    }

    /**
     * Get customIndoOverseasPphTaxWithoutId
     *
     * @return string
     */
    public function getCustomIndoOverseasPphTaxWithoutId()
    {
        return $this->customIndoOverseasPphTaxWithoutId;
    }

    /**
     * Set customIndoOverseasPphTaxWithId
     *
     * @param string $customIndoOverseasPphTaxWithId
     *
     * @return TransactionListing
     */
    public function setCustomIndoOverseasPphTaxWithId($customIndoOverseasPphTaxWithId)
    {
        $this->customIndoOverseasPphTaxWithId = $customIndoOverseasPphTaxWithId;

        return $this;
    }

    /**
     * Get customIndoOverseasPphTaxWithId
     *
     * @return string
     */
    public function getCustomIndoOverseasPphTaxWithId()
    {
        return $this->customIndoOverseasPphTaxWithId;
    }

    /**
     * Set pgfReddotMdrTransactionFee
     *
     * @param string $pgfReddotMdrTransactionFee
     *
     * @return TransactionListing
     */
    public function setPgfReddotMdrTransactionFee($pgfReddotMdrTransactionFee)
    {
        $this->pgfReddotMdrTransactionFee = $pgfReddotMdrTransactionFee;

        return $this;
    }

    /**
     * Get pgfReddotMdrTransactionFee
     *
     * @return string
     */
    public function getPgfReddotMdrTransactionFee()
    {
        return $this->pgfReddotMdrTransactionFee;
    }

    /**
     * Set pgfReddotGstOnMdr
     *
     * @param string $pgfReddotGstOnMdr
     *
     * @return TransactionListing
     */
    public function setPgfReddotGstOnMdr($pgfReddotGstOnMdr)
    {
        $this->pgfReddotGstOnMdr = $pgfReddotGstOnMdr;

        return $this;
    }

    /**
     * Get pgfReddotGstOnMdr
     *
     * @return string
     */
    public function getPgfReddotGstOnMdr()
    {
        return $this->pgfReddotGstOnMdr;
    }

    /**
     * Set doctorCustomIndoOverseasBmImportDuty
     *
     * @param string $doctorCustomIndoOverseasBmImportDuty
     *
     * @return TransactionListing
     */
    public function setDoctorCustomIndoOverseasBmImportDuty($doctorCustomIndoOverseasBmImportDuty)
    {
        $this->doctorCustomIndoOverseasBmImportDuty = $doctorCustomIndoOverseasBmImportDuty;

        return $this;
    }

    /**
     * Get doctorCustomIndoOverseasBmImportDuty
     *
     * @return string
     */
    public function getDoctorCustomIndoOverseasBmImportDuty()
    {
        return $this->doctorCustomIndoOverseasBmImportDuty;
    }

    /**
     * Set doctorCustomIndoOverseasPpnVat
     *
     * @param string $doctorCustomIndoOverseasPpnVat
     *
     * @return TransactionListing
     */
    public function setDoctorCustomIndoOverseasPpnVat($doctorCustomIndoOverseasPpnVat)
    {
        $this->doctorCustomIndoOverseasPpnVat = $doctorCustomIndoOverseasPpnVat;

        return $this;
    }

    /**
     * Get doctorCustomIndoOverseasPpnVat
     *
     * @return string
     */
    public function getDoctorCustomIndoOverseasPpnVat()
    {
        return $this->doctorCustomIndoOverseasPpnVat;
    }

    /**
     * Set doctorCustomIndoOverseasPphTaxWithoutId
     *
     * @param string $doctorCustomIndoOverseasPphTaxWithoutId
     *
     * @return TransactionListing
     */
    public function setDoctorCustomIndoOverseasPphTaxWithoutId($doctorCustomIndoOverseasPphTaxWithoutId)
    {
        $this->doctorCustomIndoOverseasPphTaxWithoutId = $doctorCustomIndoOverseasPphTaxWithoutId;

        return $this;
    }

    /**
     * Get doctorCustomIndoOverseasPphTaxWithoutId
     *
     * @return string
     */
    public function getDoctorCustomIndoOverseasPphTaxWithoutId()
    {
        return $this->doctorCustomIndoOverseasPphTaxWithoutId;
    }

    /**
     * Set doctorCustomIndoOverseasPphTaxWithId
     *
     * @param string $doctorCustomIndoOverseasPphTaxWithId
     *
     * @return TransactionListing
     */
    public function setDoctorCustomIndoOverseasPphTaxWithId($doctorCustomIndoOverseasPphTaxWithId)
    {
        $this->doctorCustomIndoOverseasPphTaxWithId = $doctorCustomIndoOverseasPphTaxWithId;

        return $this;
    }

    /**
     * Get doctorCustomIndoOverseasPphTaxWithId
     *
     * @return string
     */
    public function getDoctorCustomIndoOverseasPphTaxWithId()
    {
        return $this->doctorCustomIndoOverseasPphTaxWithId;
    }

    /**
     * Set doctorPgfReddotMdrTransactionFee
     *
     * @param string $doctorPgfReddotMdrTransactionFee
     *
     * @return TransactionListing
     */
    public function setDoctorPgfReddotMdrTransactionFee($doctorPgfReddotMdrTransactionFee)
    {
        $this->doctorPgfReddotMdrTransactionFee = $doctorPgfReddotMdrTransactionFee;

        return $this;
    }

    /**
     * Get doctorPgfReddotMdrTransactionFee
     *
     * @return string
     */
    public function getDoctorPgfReddotMdrTransactionFee()
    {
        return $this->doctorPgfReddotMdrTransactionFee;
    }

    /**
     * Set doctorPgfReddotGstOnMdr
     *
     * @param string $doctorPgfReddotGstOnMdr
     *
     * @return TransactionListing
     */
    public function setDoctorPgfReddotGstOnMdr($doctorPgfReddotGstOnMdr)
    {
        $this->doctorPgfReddotGstOnMdr = $doctorPgfReddotGstOnMdr;

        return $this;
    }

    /**
     * Get doctorPgfReddotGstOnMdr
     *
     * @return string
     */
    public function getDoctorPgfReddotGstOnMdr()
    {
        return $this->doctorPgfReddotGstOnMdr;
    }

    /**
     * Set pharmacyPgfReddotMdrTransactionFee
     *
     * @param string $pharmacyPgfReddotMdrTransactionFee
     *
     * @return TransactionListing
     */
    public function setPharmacyPgfReddotMdrTransactionFee($pharmacyPgfReddotMdrTransactionFee)
    {
        $this->pharmacyPgfReddotMdrTransactionFee = $pharmacyPgfReddotMdrTransactionFee;

        return $this;
    }

    /**
     * Get pharmacyPgfReddotMdrTransactionFee
     *
     * @return string
     */
    public function getPharmacyPgfReddotMdrTransactionFee()
    {
        return $this->pharmacyPgfReddotMdrTransactionFee;
    }

    /**
     * Set pharmacyPgfReddotGstOnCc
     *
     * @param string $pharmacyPgfReddotGstOnCc
     *
     * @return TransactionListing
     */
    public function setPharmacyPgfReddotGstOnCc($pharmacyPgfReddotGstOnCc)
    {
        $this->pharmacyPgfReddotGstOnCc = $pharmacyPgfReddotGstOnCc;

        return $this;
    }

    /**
     * Get pharmacyPgfReddotGstOnCc
     *
     * @return string
     */
    public function getPharmacyPgfReddotGstOnCc()
    {
        return $this->pharmacyPgfReddotGstOnCc;
    }

    /**
     * Set pharmacyCustomIndoOverseasBmImportDuty
     *
     * @param string $pharmacyCustomIndoOverseasBmImportDuty
     *
     * @return TransactionListing
     */
    public function setPharmacyCustomIndoOverseasBmImportDuty($pharmacyCustomIndoOverseasBmImportDuty)
    {
        $this->pharmacyCustomIndoOverseasBmImportDuty = $pharmacyCustomIndoOverseasBmImportDuty;

        return $this;
    }

    /**
     * Get pharmacyCustomIndoOverseasBmImportDuty
     *
     * @return string
     */
    public function getPharmacyCustomIndoOverseasBmImportDuty()
    {
        return $this->pharmacyCustomIndoOverseasBmImportDuty;
    }

    /**
     * Set pharmacyCustomIndoOverseasPpnVat
     *
     * @param string $pharmacyCustomIndoOverseasPpnVat
     *
     * @return TransactionListing
     */
    public function setPharmacyCustomIndoOverseasPpnVat($pharmacyCustomIndoOverseasPpnVat)
    {
        $this->pharmacyCustomIndoOverseasPpnVat = $pharmacyCustomIndoOverseasPpnVat;

        return $this;
    }

    /**
     * Get pharmacyCustomIndoOverseasPpnVat
     *
     * @return string
     */
    public function getPharmacyCustomIndoOverseasPpnVat()
    {
        return $this->pharmacyCustomIndoOverseasPpnVat;
    }

    /**
     * Set pharmacyCustomIndoOverseasPphTaxWithoutId
     *
     * @param string $pharmacyCustomIndoOverseasPphTaxWithoutId
     *
     * @return TransactionListing
     */
    public function setPharmacyCustomIndoOverseasPphTaxWithoutId($pharmacyCustomIndoOverseasPphTaxWithoutId)
    {
        $this->pharmacyCustomIndoOverseasPphTaxWithoutId = $pharmacyCustomIndoOverseasPphTaxWithoutId;

        return $this;
    }

    /**
     * Get pharmacyCustomIndoOverseasPphTaxWithoutId
     *
     * @return string
     */
    public function getPharmacyCustomIndoOverseasPphTaxWithoutId()
    {
        return $this->pharmacyCustomIndoOverseasPphTaxWithoutId;
    }

    /**
     * Set pharmacyCustomIndoOverseasPphTaxWithId
     *
     * @param string $pharmacyCustomIndoOverseasPphTaxWithId
     *
     * @return TransactionListing
     */
    public function setPharmacyCustomIndoOverseasPphTaxWithId($pharmacyCustomIndoOverseasPphTaxWithId)
    {
        $this->pharmacyCustomIndoOverseasPphTaxWithId = $pharmacyCustomIndoOverseasPphTaxWithId;

        return $this;
    }

    /**
     * Get pharmacyCustomIndoOverseasPphTaxWithId
     *
     * @return string
     */
    public function getPharmacyCustomIndoOverseasPphTaxWithId()
    {
        return $this->pharmacyCustomIndoOverseasPphTaxWithId;
    }

    /**
     * Set logisticsCustomIndoOverseasBmImportDuty
     *
     * @param string $logisticsCustomIndoOverseasBmImportDuty
     *
     * @return TransactionListing
     */
    public function setLogisticsCustomIndoOverseasBmImportDuty($logisticsCustomIndoOverseasBmImportDuty)
    {
        $this->logisticsCustomIndoOverseasBmImportDuty = $logisticsCustomIndoOverseasBmImportDuty;

        return $this;
    }

    /**
     * Get logisticsCustomIndoOverseasBmImportDuty
     *
     * @return string
     */
    public function getLogisticsCustomIndoOverseasBmImportDuty()
    {
        return $this->logisticsCustomIndoOverseasBmImportDuty;
    }

    /**
     * Set logisticsCustomIndoOverseasPpnVat
     *
     * @param string $logisticsCustomIndoOverseasPpnVat
     *
     * @return TransactionListing
     */
    public function setLogisticsCustomIndoOverseasPpnVat($logisticsCustomIndoOverseasPpnVat)
    {
        $this->logisticsCustomIndoOverseasPpnVat = $logisticsCustomIndoOverseasPpnVat;

        return $this;
    }

    /**
     * Get logisticsCustomIndoOverseasPpnVat
     *
     * @return string
     */
    public function getLogisticsCustomIndoOverseasPpnVat()
    {
        return $this->logisticsCustomIndoOverseasPpnVat;
    }

    /**
     * Set logisticsCustomIndoOverseasPphTaxWithoutId
     *
     * @param string $logisticsCustomIndoOverseasPphTaxWithoutId
     *
     * @return TransactionListing
     */
    public function setLogisticsCustomIndoOverseasPphTaxWithoutId($logisticsCustomIndoOverseasPphTaxWithoutId)
    {
        $this->logisticsCustomIndoOverseasPphTaxWithoutId = $logisticsCustomIndoOverseasPphTaxWithoutId;

        return $this;
    }

    /**
     * Get logisticsCustomIndoOverseasPphTaxWithoutId
     *
     * @return string
     */
    public function getLogisticsCustomIndoOverseasPphTaxWithoutId()
    {
        return $this->logisticsCustomIndoOverseasPphTaxWithoutId;
    }

    /**
     * Set logisticsCustomIndoOverseasPphTaxWithId
     *
     * @param string $logisticsCustomIndoOverseasPphTaxWithId
     *
     * @return TransactionListing
     */
    public function setLogisticsCustomIndoOverseasPphTaxWithId($logisticsCustomIndoOverseasPphTaxWithId)
    {
        $this->logisticsCustomIndoOverseasPphTaxWithId = $logisticsCustomIndoOverseasPphTaxWithId;

        return $this;
    }

    /**
     * Get logisticsCustomIndoOverseasPphTaxWithId
     *
     * @return string
     */
    public function getLogisticsCustomIndoOverseasPphTaxWithId()
    {
        return $this->logisticsCustomIndoOverseasPphTaxWithId;
    }

    /**
     * Set logisticsPgfReddotMdrTransactionFee
     *
     * @param string $logisticsPgfReddotMdrTransactionFee
     *
     * @return TransactionListing
     */
    public function setLogisticsPgfReddotMdrTransactionFee($logisticsPgfReddotMdrTransactionFee)
    {
        $this->logisticsPgfReddotMdrTransactionFee = $logisticsPgfReddotMdrTransactionFee;

        return $this;
    }

    /**
     * Get logisticsPgfReddotMdrTransactionFee
     *
     * @return string
     */
    public function getLogisticsPgfReddotMdrTransactionFee()
    {
        return $this->logisticsPgfReddotMdrTransactionFee;
    }

    /**
     * Set logisticsPgfReddotGstOnCc
     *
     * @param string $logisticsPgfReddotGstOnCc
     *
     * @return TransactionListing
     */
    public function setLogisticsPgfReddotGstOnCc($logisticsPgfReddotGstOnCc)
    {
        $this->logisticsPgfReddotGstOnCc = $logisticsPgfReddotGstOnCc;

        return $this;
    }

    /**
     * Get logisticsPgfReddotGstOnCc
     *
     * @return string
     */
    public function getLogisticsPgfReddotGstOnCc()
    {
        return $this->logisticsPgfReddotGstOnCc;
    }

    /**
     * Set logisticsDestructionCost
     *
     * @param string $logisticsDestructionCost
     *
     * @return TransactionListing
     */
    public function setLogisticsDestructionCost($logisticsDestructionCost)
    {
        $this->logisticsDestructionCost = $logisticsDestructionCost;

        return $this;
    }

    /**
     * Get logisticsDestructionCost
     *
     * @return string
     */
    public function getLogisticsDestructionCost()
    {
        return $this->logisticsDestructionCost;
    }

    /**
     * Set doctorAdminstrativeFee
     *
     * @param string $doctorAdminstrativeFee
     *
     * @return TransactionListing
     */
    public function setDoctorAdminstrativeFee($doctorAdminstrativeFee)
    {
        $this->doctorAdminstrativeFee = $doctorAdminstrativeFee;

        return $this;
    }

    /**
     * Get doctorAdminstrativeFee
     *
     * @return string
     */
    public function getDoctorAdminstrativeFee()
    {
        return $this->doctorAdminstrativeFee;
    }

    /**
     * Set doctorDestructionFee
     *
     * @param string $doctorDestructionFee
     *
     * @return TransactionListing
     */
    public function setDoctorDestructionFee($doctorDestructionFee)
    {
        $this->doctorDestructionFee = $doctorDestructionFee;

        return $this;
    }

    /**
     * Get doctorDestructionFee
     *
     * @return string
     */
    public function getDoctorDestructionFee()
    {
        return $this->doctorDestructionFee;
    }

    /**
     * Set doctorGstOnInvoice
     *
     * @param string $doctorGstOnInvoice
     *
     * @return TransactionListing
     */
    public function setDoctorGstOnInvoice($doctorGstOnInvoice)
    {
        $this->doctorGstOnInvoice = $doctorGstOnInvoice;

        return $this;
    }

    /**
     * Get doctorGstOnInvoice
     *
     * @return string
     */
    public function getDoctorGstOnInvoice()
    {
        return $this->doctorGstOnInvoice;
    }

    /**
     * Set pharmacyAdminstrativeFee
     *
     * @param string $pharmacyAdminstrativeFee
     *
     * @return TransactionListing
     */
    public function setPharmacyAdminstrativeFee($pharmacyAdminstrativeFee)
    {
        $this->pharmacyAdminstrativeFee = $pharmacyAdminstrativeFee;

        return $this;
    }

    /**
     * Get pharmacyAdminstrativeFee
     *
     * @return string
     */
    public function getPharmacyAdminstrativeFee()
    {
        return $this->pharmacyAdminstrativeFee;
    }

    /**
     * Set pharmacyDestructionFee
     *
     * @param string $pharmacyDestructionFee
     *
     * @return TransactionListing
     */
    public function setPharmacyDestructionFee($pharmacyDestructionFee)
    {
        $this->pharmacyDestructionFee = $pharmacyDestructionFee;

        return $this;
    }

    /**
     * Get pharmacyDestructionFee
     *
     * @return string
     */
    public function getPharmacyDestructionFee()
    {
        return $this->pharmacyDestructionFee;
    }

    /**
     * Set pharmacyGstOnInvoice
     *
     * @param string $pharmacyGstOnInvoice
     *
     * @return TransactionListing
     */
    public function setPharmacyGstOnInvoice($pharmacyGstOnInvoice)
    {
        $this->pharmacyGstOnInvoice = $pharmacyGstOnInvoice;

        return $this;
    }

    /**
     * Get pharmacyGstOnInvoice
     *
     * @return string
     */
    public function getPharmacyGstOnInvoice()
    {
        return $this->pharmacyGstOnInvoice;
    }

    /**
     * Set logisticsAdminstrativeFee
     *
     * @param string $logisticsAdminstrativeFee
     *
     * @return TransactionListing
     */
    public function setLogisticsAdminstrativeFee($logisticsAdminstrativeFee)
    {
        $this->logisticsAdminstrativeFee = $logisticsAdminstrativeFee;

        return $this;
    }

    /**
     * Get logisticsAdminstrativeFee
     *
     * @return string
     */
    public function getLogisticsAdminstrativeFee()
    {
        return $this->logisticsAdminstrativeFee;
    }

    /**
     * Set logisticsDestructionFee
     *
     * @param string $logisticsDestructionFee
     *
     * @return TransactionListing
     */
    public function setLogisticsDestructionFee($logisticsDestructionFee)
    {
        $this->logisticsDestructionFee = $logisticsDestructionFee;

        return $this;
    }

    /**
     * Get logisticsDestructionFee
     *
     * @return string
     */
    public function getLogisticsDestructionFee()
    {
        return $this->logisticsDestructionFee;
    }

    /**
     * Set doctorFeesGmedesAdminServiceFeePerRescription
     *
     * @param string $doctorFeesGmedesAdminServiceFeePerRescription
     *
     * @return TransactionListing
     */
    public function setDoctorFeesGmedesAdminServiceFeePerRescription($doctorFeesGmedesAdminServiceFeePerRescription)
    {
        $this->doctorFeesGmedesAdminServiceFeePerRescription = $doctorFeesGmedesAdminServiceFeePerRescription;

        return $this;
    }

    /**
     * Get doctorFeesGmedesAdminServiceFeePerRescription
     *
     * @return string
     */
    public function getDoctorFeesGmedesAdminServiceFeePerRescription()
    {
        return $this->doctorFeesGmedesAdminServiceFeePerRescription;
    }

    /**
     * Set doctorsFeesPrescriptionSupportGeneralEplatform
     *
     * @param string $doctorsFeesPrescriptionSupportGeneralEplatform
     *
     * @return TransactionListing
     */
    public function setDoctorsFeesPrescriptionSupportGeneralEplatform($doctorsFeesPrescriptionSupportGeneralEplatform)
    {
        $this->doctorsFeesPrescriptionSupportGeneralEplatform = $doctorsFeesPrescriptionSupportGeneralEplatform;

        return $this;
    }

    /**
     * Get doctorsFeesPrescriptionSupportGeneralEplatform
     *
     * @return string
     */
    public function getDoctorsFeesPrescriptionSupportGeneralEplatform()
    {
        return $this->doctorsFeesPrescriptionSupportGeneralEplatform;
    }

    /**
     * Set doctorsFeesPrescriptionSupportSpecialEplatform
     *
     * @param string $doctorsFeesPrescriptionSupportSpecialEplatform
     *
     * @return TransactionListing
     */
    public function setDoctorsFeesPrescriptionSupportSpecialEplatform($doctorsFeesPrescriptionSupportSpecialEplatform)
    {
        $this->doctorsFeesPrescriptionSupportSpecialEplatform = $doctorsFeesPrescriptionSupportSpecialEplatform;

        return $this;
    }

    /**
     * Get doctorsFeesPrescriptionSupportSpecialEplatform
     *
     * @return string
     */
    public function getDoctorsFeesPrescriptionSupportSpecialEplatform()
    {
        return $this->doctorsFeesPrescriptionSupportSpecialEplatform;
    }

    /**
     * Set doctorGmedesAdminServiceFeePerPrescription
     *
     * @param string $doctorGmedesAdminServiceFeePerPrescription
     *
     * @return TransactionListing
     */
    public function setDoctorGmedesAdminServiceFeePerPrescription($doctorGmedesAdminServiceFeePerPrescription)
    {
        $this->doctorGmedesAdminServiceFeePerPrescription = $doctorGmedesAdminServiceFeePerPrescription;

        return $this;
    }

    /**
     * Get doctorGmedesAdminServiceFeePerPrescription
     *
     * @return string
     */
    public function getDoctorGmedesAdminServiceFeePerPrescription()
    {
        return $this->doctorGmedesAdminServiceFeePerPrescription;
    }

    /**
     * Set agentType
     *
     * @param string $agentType
     *
     * @return TransactionListing
     */
    public function setAgentType($agentType)
    {
        $this->agentType = $agentType;

        return $this;
    }

    /**
     * Get agentType
     *
     * @return string
     */
    public function getAgentType()
    {
        return $this->agentType;
    }

    /**
     * Set agentFeeMarketingServiceLocalOverseas
     *
     * @param string $agentFeeMarketingServiceLocalOverseas
     *
     * @return TransactionListing
     */
    public function setAgentFeeMarketingServiceLocalOverseas($agentFeeMarketingServiceLocalOverseas)
    {
        $this->agentFeeMarketingServiceLocalOverseas = $agentFeeMarketingServiceLocalOverseas;

        return $this;
    }

    /**
     * Get agentFeeMarketingServiceLocalOverseas
     *
     * @return string
     */
    public function getAgentFeeMarketingServiceLocalOverseas()
    {
        return $this->agentFeeMarketingServiceLocalOverseas;
    }

    /**
     * Set agentFeePrescriptionAdminFeeLocalOverseas
     *
     * @param string $agentFeePrescriptionAdminFeeLocalOverseas
     *
     * @return TransactionListing
     */
    public function setAgentFeePrescriptionAdminFeeLocalOverseas($agentFeePrescriptionAdminFeeLocalOverseas)
    {
        $this->agentFeePrescriptionAdminFeeLocalOverseas = $agentFeePrescriptionAdminFeeLocalOverseas;

        return $this;
    }

    /**
     * Get agentFeePrescriptionAdminFeeLocalOverseas
     *
     * @return string
     */
    public function getAgentFeePrescriptionAdminFeeLocalOverseas()
    {
        return $this->agentFeePrescriptionAdminFeeLocalOverseas;
    }

    /**
     * Set pharmacyTotalAmountPayableBeforeGst
     *
     * @param string $pharmacyTotalAmountPayableBeforeGst
     *
     * @return TransactionListing
     */
    public function setPharmacyTotalAmountPayableBeforeGst($pharmacyTotalAmountPayableBeforeGst)
    {
        $this->pharmacyTotalAmountPayableBeforeGst = $pharmacyTotalAmountPayableBeforeGst;

        return $this;
    }

    /**
     * Get pharmacyTotalAmountPayableBeforeGst
     *
     * @return string
     */
    public function getPharmacyTotalAmountPayableBeforeGst()
    {
        return $this->pharmacyTotalAmountPayableBeforeGst;
    }

    /**
     * Set logisticsAmountPayableBeforeGst
     *
     * @param string $logisticsAmountPayableBeforeGst
     *
     * @return TransactionListing
     */
    public function setLogisticsAmountPayableBeforeGst($logisticsAmountPayableBeforeGst)
    {
        $this->logisticsAmountPayableBeforeGst = $logisticsAmountPayableBeforeGst;

        return $this;
    }

    /**
     * Get logisticsAmountPayableBeforeGst
     *
     * @return string
     */
    public function getLogisticsAmountPayableBeforeGst()
    {
        return $this->logisticsAmountPayableBeforeGst;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return TransactionListing
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
     *
     * @return TransactionListing
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
     * Set rx
     *
     * @param \UtilBundle\Entity\Rx $rx
     *
     * @return TransactionListing
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
     * Gets triggered only on insert

     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdOn = new \DateTime("now");
        $this->updatedOn = new \DateTime("now");
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
     * Set apDoctorGeneralMedicineAdminFee
     *
     * @param string $apDoctorGeneralMedicineAdminFee
     *
     * @return TransactionListing
     */
    public function setApDoctorGeneralMedicineAdminFee($apDoctorGeneralMedicineAdminFee)
    {
        $this->apDoctorGeneralMedicineAdminFee = $apDoctorGeneralMedicineAdminFee;

        return $this;
    }

    /**
     * Get apDoctorGeneralMedicineAdminFee
     *
     * @return string
     */
    public function getApDoctorGeneralMedicineAdminFee()
    {
        return $this->apDoctorGeneralMedicineAdminFee;
    }

    /**
     * Set arDoctorSpecialMedicineAdminFee
     *
     * @param string $arDoctorSpecialMedicineAdminFee
     *
     * @return TransactionListing
     */
    public function setArDoctorSpecialMedicineAdminFee($arDoctorSpecialMedicineAdminFee)
    {
        $this->arDoctorSpecialMedicineAdminFee = $arDoctorSpecialMedicineAdminFee;

        return $this;
    }

    /**
     * Get arDoctorSpecialMedicineAdminFee
     *
     * @return string
     */
    public function getArDoctorSpecialMedicineAdminFee()
    {
        return $this->arDoctorSpecialMedicineAdminFee;
    }
}
