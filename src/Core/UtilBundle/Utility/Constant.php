<?php

namespace UtilBundle\Utility;

class Constant {
    // for strike 802
    const TAX_IMPORT_DUTY           = 'bmImportDuty';
    const TAX_VAT                   = 'ppnVat';
    const TAX_INCOME                = 'pphWithTaxId';
    const TAX_INCOME_WITHOUT_TAX_ID = 'pphWithoutTaxId';
    const FREIGHT_COST              = 'freightCost';
    const INSURANCE_VARIABLE        = 'insuranceVariable';

    const BAG_DELIVERED = 17;
    const BAG_OTHER = 16;
    const BAG_STATUSES = array('DELIVERED' => 17, 'READY FOR CUSTOMER COLLECTION' => 17);

    const XERO_MAPPING_LOCAL    = 1;
    const XERO_MAPPING_OVERSEAS = 9;
    const XERO_MAPPING_ALL      = 5;
    const PAYMENT_STATUS = array(0 => 'Pending', 1 => 'Paid', 2 => 'For Investigation', 3 => 'Processing');
    const LOGIN_LINK = 'http://gmedes.com/';
    const LABEL_STOCK_DRUG =  "AVAILABLE - BY SPECIAL INDENT (7 DAYS)";
    const LIST_RX_REVIEW_FEE_LOCAL = [
                        '30.00'    => 30,
                        '35.00'    => 35,
                        '40.00'    => 40,
                        '45.00'    => 45,
                        '50.00'    => 50,
                        '55.00'    => 55,
                        '60.00'    => 60,
                        '65.00'    => 65,
                        '70.00'    => 70,
                        '75.00'    => 75,
                        '80.00'    => 80,
                        '85.00'    => 85,
                        '90.00'    => 90,
                        '95.00'    => 95,
                        '100.00'   => 100,
                        '105.00'   => 105,
                        '110.00'   => 110,
                        '115.00'   => 115,
                        '120.00'   => 120,
                        'Other' => 'Other'
                    ];
    const LIST_RX_REVIEW_FEE_INTERNATIONAL = [
                        '30.00'    => 30,
                        '35.00'    => 35,
                        '40.00'    => 40,
                        '45.00'    => 45,
                        '50.00'    => 50,
                        '55.00'    => 55,
                        '60.00'    => 60,
                        '65.00'    => 65,
                        '70.00'    => 70,
                        '75.00'    => 75,
                        '80.00'    => 80,
                        '85.00'    => 85,
                        '90.00'    => 90,
                        '95.00'    => 95,
                        '100.00'   => 100,
                        '105.00'   => 105,
                        '110.00'   => 110,
                        '115.00'   => 115,
                        '120.00'   => 120,
                        'Other' => 'Other'
                    ];


    const LIST_RX_FEE_LIVE_CONSULT_LOCAL = [
                        '30.00'    => 30,
                        '35.00'    => 35,
                        '40.00'    => 40,
                        '45.00'    => 45,
                        '50.00'    => 50,
                        '55.00'    => 55,
                        '60.00'    => 60,
                        '65.00'    => 65,
                        '70.00'    => 70,
                        '75.00'    => 75,
                        '80.00'    => 80,
                        '85.00'    => 85,
                        '90.00'    => 90,
                        '95.00'    => 95,
                        '100.00'   => 100,
                        '105.00'   => 105,
                        '110.00'   => 110,
                        '115.00'   => 115,
                        '120.00'   => 120,
                        'Other' => 'Other'
                    ];
    const LIST_RX_FEE_LIVE_CONSULT_INTERNATIONAL = [
                        '30.00'    => 30,
                        '35.00'    => 35,
                        '40.00'    => 40,
                        '45.00'    => 45,
                        '50.00'    => 50,
                        '55.00'    => 55,
                        '60.00'    => 60,
                        '65.00'    => 65,
                        '70.00'    => 70,
                        '75.00'    => 75,
                        '80.00'    => 80,
                        '85.00'    => 85,
                        '90.00'    => 90,
                        '95.00'    => 95,
                        '100.00'   => 100,
                        '105.00'   => 105,
                        '110.00'   => 110,
                        '115.00'   => 115,
                        '120.00'   => 120,
                        'Other' => 'Other'
                    ];

    const ISSUE_MSG_FOR_RECALLED_RX              = 'Doctor amended RX order. Patient accepted and paid';
    const GST_RATE_MODULE_NAME                   = 'gst_rate';
    const PAYMENT_SCHEDULE_MODULE_NAME           = 'payment_schedule';
    const REMINDER_SETTING_MODULE_NAME           = 'rx_reminder_setting';
    const DELIVERY_PARTNER_MODULE_NAME           = 'delivery_partner';
    const AGENT_MODULE_NAME                      = 'agents';
    const GROSS_MARGIN_SHARE_MODULE_NAME         = 'gross_margin_share';
    const GLOBAL_MARGIN_SHARE_FEE_MODULE_NAME    = 'global_margin_share_fee';
    const CUSTOM_CLEARANCE_ADMIN_FEE_MODULE_NAME = 'custom_clearance_admin_fee';
    const PAYMENT_GATEWAY_FEE_MODULE_NAME        = 'payment_gatewate_fees';
    const DOCTOR_TO_PATIENT_LOCAL                = 'doctor_to_patient_local';
    const DOCTOR_TO_PATIENT_OVERSEA              = 'doctor_to_patient_oversea';
    const GMEDS_TO_DOCTOR                        = 'gmeds_to_doctor';
    const SF_PATIENT_LOCAL                       = 'sf_patient_local';
    const SF_PATIENT_OVERSEA                     = 'sf_patient_oversea';

    const DOCTOR_MONTHLY_STATEMENT_MODULE_NAME   = 'doctor_monthly_statement';
    const DOCTOR_MONTHLY_STATEMENT_DETAIL_MODULE_NAME   = 'doctor_monthly_statement_detail';
    const DOCTOR_EXCEPTION_STATEMENT_MODULE_NAME   = 'doctor_exception_statement';

    const AGENT_MONTHLY_STATEMENT_MODULE_NAME    = 'agent_monthly_statement';
    const AGENT_MONTHLY_STATEMENT_DETAIL_MODULE_NAME = 'agent_monthly_statement_detail';
    const AGENT_EXCEPTION_STATEMENT_MODULE_NAME    = 'agent_exception_statement';

    const COURIER_WEEKLY_STATEMENT_MODULE_NAME    = 'courier_weekly_statement';
    const COURIER_WEEKLY_STATEMENT_DETAIL_MODULE_NAME = 'courier_weekly_statement_detail';
    const COURIER_EXCEPTION_STATEMENT_MODULE_NAME     = 'courier_exception_statement';
    const COURIER_DAILY_STATEMENT_MODULE_NAME    = 'courier_daily_statement';

    const PHARMACY_WEEKLY_STATEMENT_MODULE_NAME    = 'pharmacy_weekly_statement';
    const PHARMACY_WEEKLY_STATEMENT_DETAIL_MODULE_NAME = 'pharmacy_weekly_statement_detail';
    const PHARMACY_EXCEPTION_STATEMENT_MODULE_NAME     = 'pharmacy_exception_statement';
    const INDONESIA_IMPORT_TAX_MODULE_NAME = 'indonesia_import_tax';

    const PER_PAGE_DEFAULT = 10;
    const PAGE_DEFAULT = 0;
    const TOTAL_PAGE_DEFAULT = 1;
    const NUM_PAGE_LINKS_TO_DISPLAY = 10;
    const PER_PAGE_LIST = array(5, 10, 15, 20);

    const USER_TYPE_DOCTOR   = 1; //doctor
    const USER_TYPE_AGENT    = 2; //agent
    const USER_TYPE_PHARMACY = 3; //pharmacy
    const USER_TYPE_DELIVERY = 4; //delivery

    const DEFAULT_ROLE_MPA = array(
        "patient_index",
        "patient_new",
        "list_rx",
        "list_draft_rx",
        "list_pending_rx",
        "list_confirmed_rx",
        "list_recalled_rx",
        "list_failed_rx",
        "list_reported_rx",
        "create_rx",
        "doctor_custom_selling_prices"
    );

    const TYPE_DOCTOR_NAME           = 'Doctor'; //doctor
    const TYPE_AGENT_NAME            = 'Agent'; //agent
    const TYPE_SUB_AGENT_NAME        = 'Sub Agent'; //agent
    const TYPE_ADMIN_NAME            = 'Admin'; //pharmacy
    const TYPE_CUSTOMER_CARE_IT_NAME = 'Finance_IT'; //customer care
    const TYPE_MPA = 'MPA';

    const FEE_DOCTOR    = 1; //doctor
    const FEE_AGENT     = 2; //agent
    const FEE_PLATFORM  = 3; //pharmacy

	const ADMIN_ROLE = 1;
	const AGENT_ROLE = 2;
	const SUB_AGENT_ROLE = 3;
	const DOCTOR_ROLE = 4;
    const CC_ROLE = 6;

    const MSG_RX_DRAFT          = 'Draft Prescription.'; //draft
    const MSG_RX_REFILL         = 'Reminder for medication refilling.'; //refill
    const MSG_RX_REFILL_YES     = 'Reminder for medication refilling - Patient wants a refill.'; //refill yes
    const MSG_RX_REFILL_MAYBE   = 'Reminder for medication refilling - Patient requests to be contacted prior to issuing a refill.'; //refill maybe
    const MSG_TYPE_MAYBE        = 'reminder_maybe'; //refill type maybe
    const MSG_TYPE_YES          = 'reminder_yes'; //refill type yes
    const MSG_TYPE_NOTIFICATION = 'notification'; //refill type yes
    const MSG_TYPE_REPLACEMENT_ORDER = 'replacement_order'; //refill type yes
    const MSG_TYPE_DOCTOR_ISSUE = 'doctor_issue';
    const RX_STATUS_DRAFT = 1;
    const RX_STATUS_NEW = 1;
    const RX_STATUS_FOR_DOCTOR_REVIEW = 2;
    const RX_STATUS_AWAITING_PAYMENT = 3;
    const RX_STATUS_PENDING = 3;
    const RX_STATUS_CONFIRMED = 4;
    const RX_STATUS_FOR_AMENDMENT = 5;
    const RX_STATUS_REVIEWING = 6;
    const RX_STATUS_APPROVED = 9;
    const RX_STATUS_DISPENSED = 13;
    const RX_STATUS_READY_FOR_COLLECTION = 14;
    const RX_STATUS_PAYMENT_FAILED = 30;
    const RX_STATUS_COLLECTED = 15;
    const RX_STATUS_DELIVERING = 16;
    const RX_STATUS_DELIVERED = 17;
    const RX_STATUS_DELIVERY_FAILED = 34;
    const RX_STATUS_CANCELLED = 31;
    const RX_STATUS_REFUNDED = 32;
    const RX_STATUS_DAMAGED = 33;
    const RX_STATUS_RECALLED = 40;
    const RX_STATUS_ON_HOLD = 7;
    const RX_STATUS_DELETED = 20;
    const RX_STATUS_FAILED = 21;
    const RX_STATUS_REJECTED = 8;
    const RX_STATUS_DEAD = 41;
    const RX_STATUS_TRANSFERRED = 42;
    const RX_STATUS_PROCESSING_REFUND = 43;
    const RX_STATUS_SUBMITED_REFUND_CC = 49;
    const RX_STATUS_SUBMITED_REFUND_CC1Rejected = 48;
    const RX_STATUS_SUBMITED_REFUND_CC1Approved = 47;
    const RX_STATUS_SUBMITED_REFUND_CC2Rejected = 46;
    const RX_STATUS_SUBMITED_REFUND_CC2Approved = 45;
    const RX_STATUS_SUBMITED_REFUND_CC2 = 44;
    const RX_STATUS_SUBMITED_REFUND_CC_FAIL = 50;

    const RX_LINE_TYPE_ONE = 1; // medicine
    const RX_LINE_TYPE_TWO = 2; // doctor service

    const RX_LOCKING_INTERVAL = 11;

    const PAYMENT_CREDIT = 'credit';
    const PAYMENT_DEBIT = 'debit';
    const RX_LINE_TYPE_SERVICE = 2;
    const RX_LINE_TYPE_DRUG = 1;
    const DRUG_AUDIT_PENDING = 0;
    const DRUG_AUDIT_APPROVED = 1;
    const DRUG_AUDIT_APPLIED = 2;
    const DRUG_AUDIT_OVERWROTE = 3;
    const DRUG_AUDIT_REJECTED = 9;
    // Imported TAX/VAT
    const SINGAPORE_CODE = "SG";
    const INDONESIA_CODE = "ID";
    const MALAYSIA_CODE = "MY";
    const INDONESIA_DISPLAY = "INDO";
    const SINGAPORE_IMPORTED_TAX = 7; // 7%
    const SINGAPORE_IG_PERMIT_FEE_BUFFER = 105;
    const INDONESIA_IMPORTED_TAX = 17.5; // 17.5%
    const SINGAPORE_THREHOLD_MONEY = 400; // 400 SGD

    const INVOICE_NUMBER_CODE = 'INV';
    const CREDIT_NOTE_CODE = 'CN';
    const PROFORMA_NUMBER_CODE = 'PINV';

    // GST Code
    const GST_SRS = "SRS"; //Standard Rated Supplies - Apply GST to the Medicine
    const GST_ZRS = "ZRS"; //Zero Rated Supplies - Apply GST at a rate of 0% to the Medicine
    const GST_EXS = "EXS"; //EXS - Exempt - No GST
    const GST_OOS = "OOS"; //OOS - Out of Scope - No GST
    const GST_SRSGM = "SRSGM"; // SRS GMEDES
    const GST_ZRSGM = "ZRSGM"; // SRS GMEDES
    const CCAF_SG = "ccaf_sg"; // Custom Clearance Admin Fee Singapore
    const CCAF_ID = "ccaf_id"; // Custom Clearance Admin Fee Indonesia
    const SGCIG_PF = "sgcig_pf"; // SG Custom IG Permit Fee
    const PGB_GST = "pgb_gst"; // Payment Gateway Bank GST
    const SF_GMEDES = "sf_gmedes"; // Shipping Fee (GMEDES)

    // Platform setting gst code fee
    const GM_SF = "gm_sf";
    const GM_PGB_MDR = "gm_pgb_mdr";
    const GM_PGB_VARIABLE = "gm_pgb_variable";
    const GM_PGB_FIXED = "gm_pgb_fixed";
    const GM_PFRS = "gm_pfrs";
    const GM_MGMS = "gm_mgms";
    const GM_LCRS = "gm_lcrs";
    const GM_SGCIG_PF = "gm_sgcig_pf";
    const GM_CCAF_ID = "gm_ccaf_id";
    const GM_CCAF_SG = "gm_ccaf_sg";
    const GM_CT_SG = "gm_ct_sg";
    const GM_CT_ID = "gm_ct_id";

    // Payment constant
    const MPS_MODE = "NIL";
    const PAY_TYPE = "N";
    const LANG = "E";
    const PAY_METHOD_VISA_MASTER = "CC";
    const PAY_METHOD_REVPAY_FPX = "REVPAY-FPX";
    const PAYMENT_TYPE_PAID = 'captured';
    const PAYMENT_TYPE_REFUND = 'refund';
    const REFUND_STATUS = ['success'=> '00', 'rejected' => '11','pending'=>'22'];


    const IG_PERMIT_FEE_SGD = 15; // 15 SGD
    const PG_BANK_MDR = "pg_bank_mdr";
    const PG_BANK_GST = "pg_bank_gst";
    const PG_VARIABLE = "pg_variable";
    const PG_FIXED = "pg_fixed";

    // Currency
    const CURRENCY_SINGAPORE = 'SGD';
    const CURRENCY_MALAYSIA = 'MYR';
    const CURRENCY_INDONESIA = 'IDR';
    const CURRENCY_USD = 'USD';
    const CURRENCY_USED = 'SGD';
    const LIMIT = '100';
    const SINGAPORE_NAME = 'Singapore';
    
    //gateway and fee
    const ID_SINGAPORE = '195';
    const ID_SINGAPORE_STATE = '2857';
    const ID_SINGAPORE_CITY = '372406';
    const ID_MALAYSIA = '133';
    const ID_INDONESIA = '101';
    const GATEWAY_CODE_MDR = 'pg_bank_mdr';
    const GATEWAY_CODE_GST  = 'pg_bank_gst';
    const GATEWAY_CODE_VAR = 'pg_variable';
    const GATEWAY_CODE_FIX = 'pg_fixed';
    const GATEWAY_CODE_FIX_GST = 'pg_fixed_gst';
    const GATEWAY_REFUND_CHARGE = 'pg_refund_charge';
    const GATEWAY_METHOD_MV = 1;
    const GATEWAY_METHOD_MC = 2;

    // Group ID
    const GROUP_ID_SG = 1;
    const GROUP_ID_INDONESIA = 2;
    const GROUP_ID_SG_HIGHVALUE = 3;
    const GROUP_ID_INTERNATIONAL = 4;
    // Issue type
    const ISSUE_TYPE_DOCTOR = 4;
    const ISSUE_TYPE_DEFAULT = 1;
    //Courier Name
    const COURIER_WESTMEAT = 'Westmead Pharmacy Pte Ltd';
    const COLLECTION_FEE = 'Collection and Destruction Fee';

    // Margin share type
    const MST_MEDICINE     = 1;
    const MST_SERVICE      = 2;
    const MST_CUSTOM_CAF   = 3;
    const MST_LIVE_CONSULT = 4;
    // Area type
    const AREA_TYPE_LOCAL   = 1; //local
    const AREA_TYPE_OVERSEA = 2; //oversea
    // Default number
    const ZERO_NUMBER = 0;
    const ONE_NUMBER = 1;
    const ONE_HUNDRE_NUMBER = 100;

    const GENERAL_DATE_FORMAT = 'd M y';
    const PHONE_TYPE = 1;

    const LIST_BATCH_STATUS = [
        '0' => 'Error',
        '1' => 'Transferred'

    ];

    // Patient PDF
    const PDF_FILE = ".pdf";
    const PATIENT_FOLDER = "patient";

    const REMINDER_CODE_C1_P1 = 'RCC1P1';
    const REMINDER_CODE_C1_P2 = 'RCC1P2';
    const REMINDER_CODE_C1_P3 = 'RCC1P3';
    const REMINDER_CODE_C1_FP = 'RCC1FP';
    const REMINDER_CODE_C1_FD = 'RCC1FD';
    const REMINDER_CODE_C2_FOS = 'RCC2FOS';
    const REMINDER_CODE_C2_GPS = 'RCC2GPS';
    const REMINDER_CODE_C2_FP = 'RCC2FP';
    const REMINDER_CODE_C2_FD = 'RCC2FD';
    const REMINDER_CODE_C2_FA = 'RCC2FA';
    const REMINDER_CODE_EM_P1 = 'RCEMP1';
    const REMINDER_CODE_EM_D1 = 'RCEMD1';
    const REMINDER_CODE_EM_P2 = 'RCEMP2';
    const REMINDER_CODE_EM_D2 = 'RCEMD2';
    const CODE_NEW_RX_ORDER = 'NRXORDER';
    const CODE_FUTURE_RX_ORDER = 'FRXORDER';

    const STATUS_CONFIRM = '3';
    const STATUS_ACCEPT_TANDC = '4';
    const STATUS_UPDATE_PROFILE = '5';

    const ID_ADMIN = '1';
    const ID_AGENT = '2';
    const ID_SUB_AGENT = '3';
    const ID_DOCTOR = '4';
    const ID_MPA = '9';
    // refund
    const REFUND_TYPE_COURIER = '1';
    const REFUND_TYPE_DOCTOR = '2';
    const REFUND_TYPE_PATIENT = '3';
    const REFUND_TYPE_PHARMACY = '4';
    // module customer care
    const MODULE_CC ="customer_care";
    const MODULE_RO ="redispense_order";
    const AGENT_FEE ="agent_fee";
    // Reminder
    const REMINDER_30_DAYS = 30;
    const REMINDER_60_DAYS = 60;
    const CYCLE_ONE = 'one';
    const CYCLE_TWO = 'two';
    const REFILL_STATUS_YES = 'yes';
    const REFILL_STATUS_MAYBE = 'maybe';
    const REFILL_STATUS_NO = 'no';
    const MESSAGE_CONTENT_SUBJECT = 'Reminder for medication refilling';
    const MESSAGE_CONTENT_BODY_YES = 'Reminder for medication refilling - Patient wants a refill';
    const MESSAGE_CONTENT_BODY_MAYBE = 'Reminder for medication refilling - Patient requests to be contacted prior to issuing a refill.';
    const MESSAGE_CONTENT_BODY_NO = 'Patient opted out refilling your medication';
    const ADMIN_SYSTEM = 1;

    // Delivery Time
    const ONE_DAY_TIME_STAMP = 86400;
    const SATURDAY_NUMERIC = 6;
    const SUNDAY_NUMERIC = 0;

    const MESSAGE_CONTENT_TYPE_YES = 'reminder_yes';
    const MESSAGE_CONTENT_TYPE_NO = 'reminder_no';
    const MESSAGE_CONTENT_TYPE_MAYBE = 'reminder_maybe';
    const MESSAGE_CONTENT_TYPE_REFILLED = 'reminder_refilled';
    const MESSAGE_CONTENT_TYPE_DOCTOR_REVIEW = 'doctor_review';
    const MESSAGE_CONTENT_TYPE_AMENDMENTS = 'amendments';
    //resolve status

    const RESOVLVE_STATUS_DRAF = 2;
    const RESOVLVE_STATUS_ACTIVE = 1;

    const ISSUE_STATUS_DRAF = 2;
    const ISSUE_STATUS_ACTIVE = 1;
    //payment gate
    const PAYMENT_GATE_MOLPAY = 'MOLPAY';
    const PAYMENT_GATE_IPAY   = 'IPAY88';
    const PAYMENT_GATE_REDDOT = 'REDDOT';
    const RX_STATUS_LIST = ['1' => 'Draft',
            '3' => 'Pending',
            '4' => 'Confirmed',
            '6' => 'Reviewing',
            '8' => 'Rejected',
            '9' => 'Approved',
            '13' => 'Dispensed',
            '14' => 'Ready for Collection',
            '30' => 'Payment Failed',
            '15' => 'Collected',
            '16' => 'Delivering',
            '17' => 'Delivered',
            '34' => 'Delivery Failed',
            '31' => 'Cancelled',
            '32' => 'Refunded',
            '33' => 'Damaged',
            '40' => 'Recalled',
            '41' => 'Dead',
            '42' => 'Transferred',
            '43' => 'Processing Refund',
            '48' => 'Request Refund 1 Rejected',
            '47' => 'Request Refund 1 Approved',
            '46' => 'Request Refund 2 Rejected',
            '45' => 'Request Refund 2 Apporved',
            '44' => 'Refund Request Submitted',
            '49' => 'Request Refund to Endpoint Submitted',
            '50' => 'Refund Request Failed'
    ];
    const VALID_REFUND_STATUS = [4,6,8,9,13,14,15,16,17,34,42,46,48];
    const REFUND_STATUS_PENDING = 'pending';
    const REFUND_STATUS_SUCCESS = 'Success';
    //Doctor GST Setting
    const SETTING_GST_MEDICINE = 1;
    const SETTING_GST_REVIEW = 2;
    const SETTING_GST_CONSULT = 3;

    const STATUS_GST_DISABLE = 0;
    const STATUS_GST_ENABLE = 1;

    const GST_SETTING_TYPE = [
        self::SETTING_GST_MEDICINE,
        self::SETTING_GST_REVIEW,
        self::SETTING_GST_CONSULT,
    ];

    const PATIENT_TYPE = ['overseas', 'local'];
    const XRO_APP_TYPE = 'Private';
    const OAUTH_CALLBACK = 'oob';
    const XERO_AGENT ="Gmed test dev";

    const SI_EXCEL_FREQUENCY = 7;

    const PRIMARY_AGENT_FEE_CODE = [1,2,3,4,5];
    const SECONDARY_AGENT_FEE_CODE = [6,7,8,9,10];

    // Define all access pages for each role
    public static function getRoutersOfRole($role) {
        $routers = array();
        switch ($role) {
            case "Admin":
                $routers = array("admin", "homepage");
                break;
            case "ROLE_AGENT":
                $routers = array();
                break;
            case "ROLE_DOCTOR":
                $routers = array();
                break;
            case "ROLE_CUSTOMER":
                $routers = array();
                break;
            case "ROLE_PHAMACY":
                $routers = array();
                break;
            case "ROLE_FINANCE":
                $routers = array("finance");
                break;
            case "ROLE_DELIVERY":
                $routers = array();
                break;
        }
        return $routers;
    }

    public static $menuList = array(
        'payment'     => array('payment')
    );

    public static $dayOfWeek = array(
        'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'
    );

    /**
     * @author Tien Nguyen
     */
    public static function getRXStatus($status)
    {
        switch ($status) {
            case self::RX_STATUS_DRAFT:
                $result = 'Draft';
                break;
            case self::RX_STATUS_NEW:
                $result = 'New';
                break;
            case self::RX_STATUS_AWAITING_PAYMENT:
                $result = 'Pending';
                break;
            case self::RX_STATUS_DELIVERED:
                $result = 'Delivered';
                break;
            case self::RX_STATUS_CANCELLED:
                $result = 'Cancelled';
                break;
            case self::RX_STATUS_REJECTED:
                $result = 'Rejected';
                break;
            case self::RX_STATUS_ON_HOLD:
                $result = 'On Hold';
                break;
            case self::RX_STATUS_CONFIRMED:
                $result = 'Confirmed';
                break;
            case self::RX_STATUS_REVIEWING:
                $result = 'Reviewing';
                break;
            case self::RX_STATUS_APPROVED:
                $result = 'Approved';
                break;
            case self::RX_STATUS_DISPENSED:
                $result = 'Dispensed';
                break;
            case self::RX_STATUS_COLLECTED:
                $result = 'Collected';
                break;
            case self::RX_STATUS_PAYMENT_FAILED:
                $result = 'Payment Failed';
                break;
            case self::RX_STATUS_READY_FOR_COLLECTION:
                $result = 'Ready for Collection';
                break;
            case self::RX_STATUS_DELIVERING:
                $result = 'Delivering';
                break;
            case self::RX_STATUS_DELIVERY_FAILED:
                $result = 'Delivery Failed';
                break;
            case self::RX_STATUS_REFUNDED:
                $result = 'Refunded';
                break;
            case self::RX_STATUS_DAMAGED:
                $result = 'Damaged';
                break;
            case self::RX_STATUS_RECALLED:
                $result = 'Recalled';
                break;
            case self::RX_STATUS_DEAD:
                $result = 'Dead';
                break;
            case self::RX_STATUS_TRANSFERRED:
                $result = 'Transferred';
                break;
            case self::RX_STATUS_PROCESSING_REFUND:
                $result = 'Processing Refund';
                break;
            case self::RX_STATUS_SUBMITED_REFUND_CC2:
                $result = 'Refund Request Submitted';
                break;
            case self::RX_STATUS_SUBMITED_REFUND_CC2Approved:
                $result = 'Request Refund 2 Apporved';
                break;
            case self::RX_STATUS_SUBMITED_REFUND_CC2Rejected:
                $result = 'Request Refund 2 Rejected';
                break;
            case self::RX_STATUS_SUBMITED_REFUND_CC1Approved:
                $result = 'Request Refund 1 Approved';
                break;
            case self::RX_STATUS_SUBMITED_REFUND_CC1Rejected:
                $result = 'Request Refund 1 Rejected';
                break;
            case self::RX_STATUS_SUBMITED_REFUND_CC:
                $result = 'Request Refund to Endpoint Submitted';
                break;
            case self::RX_STATUS_SUBMITED_REFUND_CC_FAIL:
                $result = 'Refund Request Failed';
                break;
            case self::RX_STATUS_FAILED:
                $result = 'Failed';
                break;
            case self::RX_STATUS_FOR_DOCTOR_REVIEW:
                $result = "For Doctor's Review";
                break;
            case self::RX_STATUS_FOR_AMENDMENT:
                $result = 'For Amendments';
                break;
            default:
                $result = '';
        }

        return $result;
    }

    public static $userTypes = array(
        self::USER_TYPE_DOCTOR   => 'Doctor',
        self::USER_TYPE_AGENT    => 'Agent',
        self::USER_TYPE_PHARMACY => 'Pharmacy',
        self::USER_TYPE_DELIVERY => 'Delivery'
    );


    const REDIRECT_ROLE = array(
        'Doctor'        => 'doctor_dashboard',
        'Agent'         => 'agent_dashboard',
        'Sub Agent'     => 'agent_dashboard',
        'Admin'         => 'admin_dashboard',
        'Customer Care' => 'customer_care_order_list',
        'Finance'       => 'finance_doctor_monthly_view',
        'Finance_IT' => 'synchronization_list',
        'Pharmacist' => 'customer_care_list_redispensing',
        'MPA' => 'mpa_index'
    );

    const HELP_GMEDS_ROUTES = array(
        'doctor_faq',
        'patient_faq'
    );

    const PUBLIC_ROUTES = [
        'login',
        'login_check',
        'confirm_google_auth_code',
        'logout',
        'access_denied',
        'prescription_index',
        'shipping_index',
        'review_index',
        'thanks_index',
        'success_index',
        'failed_index',
        'reddot_payment_validate',
        'reddot_payment_result',
        'prescription_email_click',
        'state_index',
        'city_index',
        'area_index',
        'city_empty_state',
        'refill_index',
        'patientAddress_index',
        'forgot_password',
        'change_password',
        'doctor_setting_password',
        'login_doctor_setting_password',
        'homepage',
        'not_found',
        'confirm_otp',
        'ajax_resend_otp',
        'tracking_order',
        'tracking_order_list',
        'single_session',
        'refill_entry',
        'mol_payment_result',
        'customer_care_excute_refund',
        'ipay88_response',
        'ipay88_backend_response',
        'customer_care_update_dispense',
        'xero-test',
        'prescription_update_tax',
        'pdf_fda_declaration_form',
        'update_fda_information',
        'device_warning',
        'thanks_declaration_form',
        'pdf_cif',
        'get_pdf_file',
        'pdf_rx',
        'extral_tools',
        'mpa_setting_password',
        'patient_terms_of_use',
        'doctor_user_guide',
        'patient_faq',
        'doctor_faq',
        'doctor_subscriber_agreement',
    ];
    const PERMISSIONS = [
            'Admin' => [
                'admin_dashboard',
                'ajax_get_chart_data_admin',
                'admin_doctor_list',
                'admin_doctor_update_status_ajax',
                'admin_doctor_autocomplete',
                'admin_doctor_list_ajax',
                'admin_doctor_create',
                'admin_doctor_edit',
                'admin_doctor_edit_ajax',
                'admin_doctor_create_ajax',
                'admin_doctor_create_getdependent',
                'admin_pharmacy_create',
                'admin_pharmacy_edit',
                'admin_pharmacy_list',
                'admin_ajax_pharmacy',
                'admin_delivery_partner',
                'admin_courier_list_ajax',
                'admin_ajax_delivery',
                'admin_delivery_partner_create',
                'admin_delivery_partner_edit',
                'admin_delivery_autocomplete',
                'admin_delivery_update',
                'admin_agent',
                'admin_agent_autocomplete',
                'admin_sub_agent',
				'admin_agent_login',
				'admin_sub_agent_login',
				'admin_agent_create_login',
				'admin_sub_agent_create_login',
				'admin_agent_login_edit',
				'admin_sub_agent_login_edit',
				'admin_doctor_login',
				'admin_doctor_create_login',
				'admin_doctor_login_edit',
				'admin_login_ajax',
                'admin_agent_view_doctor_ajax',
                'admin_agent_edit',
                'admin_agent_list_ajax',
                'admin_agent_create',
                'admin_agent_create_sub',
                'admin_reactivate_agent',
                'admin_reactivate_doctor',
                'admin_sub_agent_edit',
                'admin_doctor_agreement_setting',
                'admin_save_doctor_agreement_setting',
                'admin_get_doctor_agreement_setting',
                'admin_agent_update_status_ajax',
                'admin_save_doctor_agreement_notification',
                'admin_send_doctor_agreement_notification',
                'admin_patient_terms_of_use_setting',
                'admin_get_patient_terms_of_use_setting',
                'admin_save_patient_terms_of_use_setting',
                'admin_doctor_user_guide_setting',
                'admin_get_doctor_user_guide_setting',
                'admin_save_doctor_user_guide_setting',
                'admin_patient_faq_setting',
                'admin_get_patient_faq_setting',
                'admin_save_patient_faq_setting',
                'admin_doctor_faq_setting',
                'admin_get_doctor_faq_setting',
                'admin_save_doctor_faq_setting',
                'admin_validate_email',
                'pharmacy_products',
                'pharmacy_products_list',
                'pharmacy_list_products',
                'drug_list_names',
                'drug_update_prices',
                'drug_list_logs',
                'drug_print_logs',
                'payment_schedule',
                'admin_view_logs',
                'admin_write_logs',
                'admin_print_logs',
                'payment_gross_margin_share',
                'payment_global_margin_share_fee',
                'admin_gross_margin_share_view_logs',
                'admin_gross_margin_share_print_logs',
                'payment_gms_update_active',
                'payment_gms_new_update_active',
                'payment_gst_rate',
                'payment_product_margin',
                'payment_status',
                'payment_status_detail',
                'payment_status_update',
                'payment_status_suggestion',
                'payment_indonesia_import_tax',
                'ajax_payment_status',
                'admin_rx_refill_setting',
                'admin_report_doctor',
                'admin_report_doctor_ajax',
                'admin_report_transaction_history_csv',
                'admin_report_transaction_history',
                'admin_report_transaction_history_ajax',
                'admin_report_agent',
                'admin_report_agent_ajax',
                'admin_report_agent_breakdown_ajax',
                'admin_report_transaction_history_ajax',
                'admin_profile',
                'ajax_admin_change_password',
                'ajax_admin_change_profile',
                'admin_report_doctor_csv',
                'admin_report_agent_csv',
                'admin_custom_clearance_fee',
                'admin_new_custom_clearance_fee',
                'admin_gateway_fee',
                'admin_payment_margin_gst_view_logs',
                'admin_payment_margin_gst_print_logs',
                'admin_fee_ajax',
                'admin_rx_reminder_setting',
                'admin_rx_reminder_setting_c1',
                'admin_rx_reminder_setting_c2',
                'admin_rx_transaction_history_detail',
                'admin_message',
                'admin_change_password',
                'rx_refill_reminder_logs',
                'rx_refill_reminder_print_logs',
                'rx_reminder_logs',
                'rx_reminder_print_logs',
                'admin_rx_report_auto_suggest_ajax',
                'admin_create',
                'admin_resend_doctor_welcome_email',
                'admin_resend_agent_welcome_email',
                'payment_gst_rate_log',
                'admin_delivery_partner_log',
                'message',
                'message_compose',
                'message_list',
                'message_view',
                'message_change',
                'message_send',
                'message_upload',
                'message_download',
                'message_suggestion',
                'admin_pharmacy_group_drug',
                'admin_pharmacy_group_drug_list',
                'admin_pharmacy_group_drug_add',
                'admin_pharmacy_group_drug_edit',
                'admin_pharmacy_group_drug_delete',
                'admin_pharmacy_group_drug_form',
                'admin_pharmacy_group_drug_update',
                'admin_pharmacy_group_drug_move',
                'admin_pharmacy_group_check',
                'admin_others_fee',
                'admin_others_setting',
                'admin_report_rx_refunds',
                'admin_report_rx_refunds_ajax',
                'admin_report_rx_refunds_csv',
                'admin_resend_welcome_email',
                'pdf_shipping_label',
                'admin_ajax_generate_google_auth',
                'admin_ajax_remove_google_auth',
                'admin_ajax_save_google_auth',
                'admin_mpa_dashboard',
                'ajax_admin_mpa_dashboard',
                'admin_mpa_register',
                'admin_mpa_detail',
                'admin_upload_picture',
                'admin_get_document_log',
                'admin_view_document_log',
                'admin_document_log_list_ajax',
                'admin_validate_email_mpa',
                'admin_agent_fee_ajax',
                'admin_rx_order_notification_setting',
                'admin_rx_order_ajax_save_notification_setting',
                'rx_order_notification_logs',
                'rx_order_notification_print_logs',
                'admin_document_log_list_ajax',
                'admin_validate_email_mpa',
                'admin_future_rx_order_notification_setting',
                'admin_ajax_save_future_rx_order_notification_setting',
            ],
            'Agent' => [
                'agent_dashboard',
                'ajax_get_chart_data',
                'ajax_list_doctors',
                'ajax_agent_change_password',
                'ajax_agent_change_profile',
                'agent_profile',
                'agent_change_password',
                'sales_report',
                'monthly_statement',
                'ajax_breakdown_rx',
                'ajax_list_search',
                'ajax_list_sales',
                'ajax_download_sales_report',
                'reports_ajax_monthly_statement',
                'agent_report_monthly_statement_pdf',
                'doctors',
                'agent_profile_get_state_city',
                'agent_profile_get_city',

                'doctor_report_transaction_history',
                'doctor_report_transaction_history_ajax',
                'doctor_rx_transaction_history_detail',
                'doctor_rx_report_auto_suggest_ajax',
                'doctor_report_monthly_statement',
                'doctor_report_monthly_statement_ajax',
                'doctor_report_monthly_statement_pdf',
                'doctor_report_transaction_history_csv',
                'admin_doctor_create_getdependent',

                'agent_message',
                'agent_message_compose',
                'agent_message_list',
                'agent_message_view',
                'agent_message_change',
                'agent_message_send',
                'agent_message_upload',
                'agent_message_download',
                'agent_message_suggestion',
                'admin_view_logs',
                'admin_print_logs',
                'finance_print_to_pdf'

            ],
            'Sub Agent' => [
                'agent_dashboard',
                'ajax_get_chart_data',
                'ajax_list_doctors',
                'ajax_agent_change_password',
                'ajax_agent_change_profile',
                'agent_profile',
                'agent_change_password',
                'sales_report',
                'monthly_statement',
                'ajax_breakdown_rx',
                'ajax_list_search',
                'ajax_list_sales',
                'ajax_download_sales_report',
                'reports_ajax_monthly_statement',
                'agent_report_monthly_statement_pdf',
                'doctors',
                'agent_profile_get_state_city',
                'agent_profile_get_city',
                'doctor_report_transaction_history',
                'doctor_report_transaction_history_ajax',
                'doctor_rx_transaction_history_detail',
                'doctor_rx_report_auto_suggest_ajax',
                'doctor_report_monthly_statement',
                'doctor_report_monthly_statement_ajax',
                'doctor_report_monthly_statement_pdf',
                'doctor_report_transaction_history_csv',
                'agent_message',
                'agent_message_compose',
                'agent_message_send',
                'agent_message_view',
                'agent_message_change',
                'agent_message_upload',
                'agent_message_download',
                'agent_message_suggestion',
                'agent_message_list',
                'finance_print_to_pdf'
            ],
            'Doctor' => [
                'doctor_dashboard',
                'ajax_get_check_stock',
                'doctor_dashboard_close_notification',
                'ajax_list_rx',
                'ajax_get_list_patient',
                'ajax_get_chart_data_doctor',
                'index_rx',
                'create_rx',
                'ajax_get_rx_drug',
                'ajax_get_drug',
                'ajax_get_top30',
                'ajax_get_favorites',
                'ajax_handle_favorite',
                'ajax_get_step2_content',
                'ajax_save_as_draft',
                'review_rx',
                'confirm_rx',
                'list_rx',
                'list_draft_rx',
                'list_scheduled_rx',
                'list_failed_rx',
                'list_recalled_rx',
                'list_pending_rx',
                'list_confirmed_rx',
                'list_deleted_cancelled_rx',
                'ajax_get_list_rx',
                'delete_rx',
                'edit_rx',
                'copy_rx',
                'view_rx',
                'update_rx',
                'ajax_save_update_rx',
                'ajax_get_activities_log',
                'ajax_recall',
                'recall_rx',
                'ajax_resend',
                'patient_index',
                'patient_ajax_get_list',
                'patient_delete',
                'patient_list_rx_history',
                'patient_ajax_list_rx_history',
                'patient_new',
                'patient_edit',
                'doctor_report_transaction_history',
                'doctor_report_transaction_history_ajax',
                'doctor_rx_transaction_history_detail',
                'doctor_rx_report_auto_suggest_ajax',
                'doctor_report_monthly_statement',
                'doctor_report_monthly_statement_ajax',
                'doctor_report_monthly_statement_pdf',
                'doctor_report_transaction_history_csv',
                'doctor_profile',
                'doctor_profile_view',
                'doctor_change_password',
                'ajax_doctor_change_password',
                'ajax_doctor_edit',
                'doctor_profile_create_getdependent',
                'ajax_doctor_accept_tandc',
                'ajax_edit_recalled_rx',
                'list_reported_rx',
                'doctor_rx_activities_print_logs',
                'doctor_message',
                'doctor_message_compose',
                'doctor_message_list',
                'doctor_message_view',
                'doctor_message_change',
                'doctor_message_send',
                'doctor_message_upload',
                'doctor_message_download',
                'doctor_message_suggestion',
                'doctor_report_download_invoice',
                'admin_view_logs',
                'admin_print_logs',
                'pdf_proforma_rx',
                'patient_ajax_get_info',
                'patient_ajax_get_info_note_list',
                'ajax_set_adverse_activities',                
                'ajax_closed_messages',
                'pdf_cif',
                'pdf_shipping_label',
                'doctor_medicine_list',
                'doctor_ajax_medicine_list',
                'check_edit_rx_session',
                'doctor_rx_request_assistant_amend_rx',
                'admin_doctor_create_getdependent',
                'doctor_custom_selling_prices',
                'doctor_custom_selling_prices_list',
                'doctor_custom_selling_prices_update_price',
                'doctor_custom_selling_prices_list_logs',
                'doctor_custom_selling_prices_logs',
                'doctor_custom_selling_prices_download_excel',
                'doctor_custom_selling_prices_upload_favorite_drugs',
                'finance_print_to_pdf'
            ],
            'Customer Care' => [
                'customer_care_order_list',
                'customer_care_order_list_ajax',
                'customer_care_order_list_detail',
                'customer_care_order_list_suggestion',
                'customer_care_issue_resolutions',
                'admin_rx_transaction_history_detail',
                'customer_care_issue_solution',
                'customer_care_issue_solution_ajax',
                'customer_care_issue_solutio_update',
                'customer_care_issue_solution_report',
                'customer_care_report_issue_solution_ajax',
                'customer_care_issue_solutio_update_ajax',
                'customer_care_issue_update_list',
                'customer_care_incident_report_update_list',
                'customer_care_uploadfile_ajax',
                'customer_care_delete_upload_file_ajax',
                'customer_care_load_refund',
                'customer_care_save_refund',
                'customer_care_refund',
                'customer_care_save_re_dispense',
                'customer_care_incident_issue_update_ajax',
                'customer_care_log_update_ajax',
                'customer_care_restore_attachment_ajax',
                'customer_care_redispense_add_issue_ajax',
                'customer_care_validate_refund',
                'customer_care_excute_refund_test',
                'customer_care_credit_note_test',
                'customer_care_refund_test',
                'customer_careinvoice_dispense',
                'customer_care_save_resole_rx',
                'customer_care_get_address_data',
                'pdf_rx_invoice',
                'customer_care_message',
                'customer_care_message_compose',
                'customer_care_message_list',
                'customer_care_message_view',
                'customer_care_message_change',
                'customer_care_message_send',
                'customer_care_message_upload',
                'customer_care_message_download',
                'customer_care_message_suggestion',
                'customer_replacement_order_by_doctor',
                'collect_and_destruction_of_parcel',
                'change_delivery_address',
                'cc_get_city',
                'credit_note_pdf',
                'admin_view_logs',
                'admin_print_logs',
                'customer_care_excute_sendrequest_test',
                'customer_care_load_dependence_ajax',
                'customer_care_profile',
                'ajax_customer_care_change_profile',
                'customer_care_list_redispensing',
                'customer_care_completed_redispensing',
                'customer_care_redispensing_order',
                'customer_care_redispensing_review',
                'customer_care_update_data_ajax',
                'customer_care_pdf_rx',
                'customer_care_showResolve',
                'customer_care_load_resolve_ajax',
                'customer_care_issue_create_resolve',
                'customer_care_issue_update_resolve',
                'customer_care_update_resolve_ajax'
            ],
            'Finance' => [
                'finance_default',
                'finance_setting_public_holiday',
                'bank_payment',
                'bank_payment_list',
                'bank_payment_upload',
                'bank_payment_detail',
                'bank_payment_detail_list',
                'finance_doctor_monthly_index',
                'finance_doctor_monthly_view',
                'finance_doctor_detail_monthly',
                'finance_doctor_exception_statement_view',
                'finance_doctor_exception_statement_index',
                'finance_doctor_consolidated_statement_index',
                'finance_agent_monthly_valid_si_download',
                'bank_payment_detail',
                'berjaya_weekly_po',
                'berjaya_weekly_po_list',
                'berjaya_weekly_po_detail',
                'berjaya_weekly_po_update',
                'berjaya_weekly_po_suggestion',
                'berjaya_po_exception',
                'berjaya_po_exception_list',
                'berjaya_po_exception_report',
                'berjaya_po_exception_download',
                'berjaya_daily_po',
                'berjaya_daily_po_list',
                'berjaya_daily_po_download',
                'berjaya_weekly_po_download',
                'berjaya_weekly_upload_invoice',
                'berjaya_weekly_upload',
                'berjaya_weekly_logs',
                'berjaya_exclude_daily',
                'berjaya_exclude_daily_update',
                'berjaya_po_resolve_issue',
                'berjaya_notify_supplier',
                'berjaya_download_standing_instruction',
                'finance_doctor_consolidated_statement_exclude',
                'doctor_report_download_invoice',
                'doctor_report_monthly_statement_pdf',
                'finance_doctor_standing_instruction',
                'finance_doctor_ms_logs_index',
                'finance_doctor_credit_note',
                'finance_agent_monthly_view',
                'finance_agent_monthly_index',
                'finance_agent_exception_statement_view',
                'finance_agent_exception_statement_index',
                'finance_agent_detail_monthly',
                'finance_agent_consolidated_statement_index',
                'finance_agent_consolidated_statement_exclude',
                'finance_agent_ms_logs_index',
                'finance_agent_standing_instruction',
                'agent_report_monthly_statement_pdf',
                'finance_agent_upload_invoice',
                'agent_payment_reminder',
                'doctor_payment_reminder',
                'berjaya_payment_reminder',
                'westmead_payment_reminder',
                'finance_update_reminder',
                'finance_setting_public_holiday_index',
                'finance_setting_public_holiday_delete',
                'finance_home_page',
                'finance_setting_payment_date',
                'westmead_weekly_po',
                'westmead_weekly_po_list',
                'admin_view_logs',
                'admin_print_logs',
                'admin_write_logs',
                'westmead_daily_po',
                'westmead_daily_po_list',
                'westmead_weekly_po_upload_invoice',
                'westmead_daily_po_exclude',
                'westmead_weekly_po_log',
                'westmead_weekly_po_exception',
                'westmead_weekly_po_exception_list',
                'platform_schedule',
                'platform_gross_margin_share',
                'platform_gst_rates',
                'platform_cc_admin_fee',
                'platform_payment_gateway_feeds',
                'platform_currency_exchange',
                'platform_medicine_costs',
                'platform_medicine_costs_list',
                'platform_shipping_fees',
                'platform_shipping_fees_list',
                'westmead_weekly_po_exception_list',
                'westmead_download_standing_instruction',
                'westmead_po_resolve_issue',
                'westmead_notify_supplier',
                'finance_agent_resolve_issue',
                'finance_doctor_resolve_issue',
                // 'pg_settlement',
                'pg_settlement_detail',
                'pg_settlement_update',
                'pg_settlement_suggestion',
                'ajax_pg_settlement',
                'pg_summary',
                'ajax_pg_summary',
                'pg_summary_log',
                'pg_summary_update',
                'pg_amount_update',
                'doctor_download_list',
                'doctor_download_exception_list',
                'doctor_download_consolidated_list',
                'agent_download_list',
                'agent_download_exception_list',
                'agent_download_consolidated_list',
                'westmead_weekly_po_download',
                'westmead_daily_po_download',
                'westmead_po_exception_download',
                'finance_get_frequency_setting',
                'download_bank_settlement_history',
                'sync_index',
                'sync_list',
                'sync_info',
                'sync_info_list',
                'sync_info_detail',
                'chart_of_accounts_index',
                'chart_of_accounts_list',
                'chart_of_accounts_delete',
                'chart_of_accounts_add',
                'chart_of_accounts_edit',
                'sync_info_detail',
                'sync_download',
                'finance_setting_accounts_code_mapping',
                'finance_setting_accounts_code_mapping_action',
                'synchronization_list',
                'synchronization_list_ajax',
                'synchronization_batch_info',
                'synchronization_batch_info_ajax',
                'synchronization_batch_info_ajax_single',
                'synchronization_batch_detail',
                'synchronization_batch_detail_ajax',
                'synchronization_view_order',
                'synchronization_view_order_ajax',
                'synchronization_batch_detail_report_download',
                'synchronization_view_order_report_download',
                'synchronization_batch_resend',
                'synchronization_batch_info_report_download',
                'finance_setting_accounts_code_mapping_action',
                'finance_doctor_exception_statement_print',
                'finance_agent_exception_statement_print',
                'westmead_po_exception_print',
                'berjaya_upload_invoice',
                'berjaya_exception_upload_invoice',
                'finance_setting_accounts_code_mapping_ajax',
                'chart_of_accounts_index_ajax',
                'berjaya_exception_upload_invoice',
                'finance_agent_get_emf',
                'finance_profile',
                'ajax_admin_change_profile',
                'synchronization_batch_export',
                'chart_of_accounts_index_upload',
                'synchronization_setting',
                'synchronization_component',
                'synchronization_event',
                'synchronization_region',
                'synchronization_action',
                'synchronization_setting_ajax',
                'finance_print_to_pdf',
                'finance_doctor_standing_instruction_pantai',
                'finance_doctor_standing_instruction_pantai_exception',
                'finance_agent_standing_instruction_pantai',
                'finance_agent_standing_instruction_pantai_exception',
                'finance_berjaya_standing_instruction_pantai',
                'finance_berjaya_standing_instruction_pantai_exception',
                'finance_westmead_standing_instruction_pantai',
                'finance_westmead_standing_instruction_pantai_exception',
                'platform_currency_exchange_log',
                'bank_payment_upload_test',
                'finance_global_margin_share',
                'agent_medicine_flat_fee',
                'agent_medicine_flat_fee_list',
            ],
            'Finance_IT' => [
                'synchronization_list',
                'synchronization_list_ajax',
                'synchronization_batch_info',
                'synchronization_batch_info_ajax',
                'synchronization_batch_export',
                'synchronization_batch_resend',
                'synchronization_batch_info_view_xml',
                'synchronization_batch_info_ajax_single',
                'synchronization_batch_detail',
                'synchronization_batch_detail_ajax',
                'synchronization_view_order',
                'synchronization_view_order_ajax',
                'synchronization_batch_detail_report_download',
                'synchronization_view_order_report_download',
                'finance_setting_accounts_code_mapping',
                'finance_setting_accounts_code_mapping_ajax',
                'chart_of_accounts_index',
                'chart_of_accounts_index_ajax',
                'chart_of_accounts_index_upload',
                'chart_of_accounts_list',
                'chart_of_accounts_delete',
                'chart_of_accounts_add',
                'chart_of_accounts_edit',
                'synchronization_setting',
                'synchronization_setting_ajax',
                'synchronization_component',
                'synchronization_event',
                'synchronization_region',
                'synchronization_action',
                'synchronization_batch_retransfer'

            ],
             'Pharmacist' => [
                'pharmacist_re_dispensing',
                'pharmacist_re_dispensing_ajax',
                'customer_care_profile',
                'ajax_customer_care_change_profile',
                'customer_care_message',
                'customer_care_message_compose',
                'customer_care_message_list',
                'customer_care_message_view',
                'customer_care_message_change',
                'customer_care_message_send',
                'customer_care_message_upload',
                'customer_care_message_download',
                'customer_care_message_suggestion',
                'customer_care_load_dependence_ajax',
                'customer_care_profile',
                'ajax_customer_care_change_profile',
                'customer_care_list_redispensing',
                'customer_care_completed_redispensing',
                'customer_care_redispensing_order',
                'customer_care_redispensing_review',
                'customer_care_update_data_ajax',
                'customer_care_pdf_rx',
                'customer_care_order_list_ajax',
                'customer_care_pdf_rx',
                'admin_view_logs',
                'admin_print_logs'
            ],
            'MPA' => [
                'mpa_index'
            ]
        ];

    //Message status
    const MESSAGE_INBOX = 0;
    const MESSAGE_DRAFT = 1;
    const MESSAGE_SPAM = 2;

    const MESSAGE_GROUP_ADMIN = 'gmedes admin';
    const MESSAGE_GROUP_AGENT = 'all agents';
    const MESSAGE_GROUP_DOCTOR = 'all doctors';
    const MESSAGE_GROUP_CUSTOMER_CARE = 'customer care';
    const MESSAGE_GROUP_ASK_THE_PHARMACIST = 'ask the pharmacist';
    const MESSAGE_GROUP_PHARMACY_SERVICE = 'pharmacy service';

    public static $messageStatus = array(
        'inbox',
        'sent',
        'draft',
        'spam'
    );

    // Collection and Destroy
    const PARTY_LIST = [
        1   => 'Delivery Partner',
        2   => 'Doctor',
        3   => 'Patient',
        4   => 'Pharmacy'
    ];

    //SMS send status
    const SMS_STATUS_NONE = 0;
    const SMS_STATUS_SENT = 1;
    const SMS_STATUS_ERROR = 2;

    //Email send status
    const EMAIL_STATUS_NONE = 0;
    const EMAIL_STATUS_SENT = 1;
    const EMAIL_STATUS_ERROR = 2;

    //Finance Reminder Code
    // Agent payment reminder for uploading invoices
    const AP_INVOICE_REMINDER_CODE = 'APIVR';
    // Agent payment reminder for uploading standing instructions to the bank
    const AP_SI_REMINDER_CODE = 'APSIR';
    // Doctor payment reminder for uploading standing instructions to the bank
    const DP_SI_REMINDER_CODE = 'DPSIR';
    // Berjaya payment reminder for uploading invoices
    const BP_INVOICE_REMINDER_CODE = 'BPIVR';
    // Berjaya payment reminder for uploading standing instructions to the bank
    const BP_SI_REMINDER_CODE = 'BPSIR';
    // Westmead payment reminder for uploading invoices
    const WP_INVOICE_REMINDER_CODE = 'WPIVR';
    // Westmead payment reminder for uploading standing instructions to the bank
    const WP_SI_REMINDER_CODE = 'WPSIR';

    const FREQUENCY_TYPE_DAYS = 'days';
    const FREQUENCY_TYPE_DAY_OF_WEEK = 'dow';

    public static $financeReminderCodes = array(
        self::AP_INVOICE_REMINDER_CODE => 'Agent',
        self::AP_SI_REMINDER_CODE => 'Agent',
        self::DP_SI_REMINDER_CODE => 'Doctor',
        self::BP_INVOICE_REMINDER_CODE => 'Berjaya',
        self::BP_SI_REMINDER_CODE => 'Berjaya',
        self::WP_INVOICE_REMINDER_CODE => 'Westmead',
        self::WP_SI_REMINDER_CODE => 'Westmead'
    );

    public static $financeReminderFrequencies = array(
        'Agent' => array(1, 5, 10, 15, 20),
        'Doctor' => array(1, 5, 10, 15, 20),
        'Berjaya' => array(1, 2, 3, 4, 5, 6, 7),
        'Westmead' => array(1, 2, 3, 4, 5, 6, 7)
    );

    //drug group
    const DRUG_GROUP_DEFAULT = 1;

    //xero define

    const XERO_DOCUMENT_TYPE_BILL = 'bill';
    const XERO_DOCUMENT_TYPE_JOURNAL = 'manualjournal';
    const XERO_DOCUMENT_TYPE_SALE = 'sale';
    const XERO_DOCUMENT_TYPE_PAYMENT = 'payment';
    const XERO_DOCUMENT_TYPE_SETTLEMENT = 'settlement';
    const XERO_DOCUMENT_TYPE_SETTLEMENT_NAME = "Settlement Date by Payment Gateway";

    const XERO_TRACKING_TYPE_DOCTOR_STATEMENT = 1;
    const XERO_TRACKING_TYPE_AGENT_STATEMENT = 2;
    const XERO_TRACKING_TYPE_PHARMACY_WEEKLY = 3;
    const XERO_TRACKING_TYPE_COURIER_WEEKLY = 4;


    // pantai define

    const PANTAI_PURPOSE_CODE_DOCTOR = 1;
    const PANTAI_PURPOSE_CODE_PHARMACY = 2;
    const PANTAI_PURPOSE_CODE_LOGISTIC = 3;

	const PRIVILEGE = array(
		'doctors', 'sales_report', 'monthly_statement',
		'patient_index', 'patient_new',
		'list_rx', 'list_draft_rx', 'list_pending_rx', 'list_confirmed_rx', 'list_recalled_rx',
		'list_failed_rx', 'list_reported_rx', 'index_rx', 'create_rx',
		'doctor_report_transaction_history', 'doctor_report_monthly_statement'
	);

    //Hashing querystring
    const HASHING_PREFIX = 'gm';

    //Doctor favorite drugs limit
    const FAVORITE_DRUGS_LIMIT = 50;

    const SITE_PARKWAY_TYPE = 1;
    const SITE_NON_PARKWAY_TYPE = 2;
    const NON_PARKWAY_COUNTRY_PHONE_CODE = '68';

    const MAX_SMS_LENGTH = 420;
    const  REDISPENSE_STATE_AVAILABLE = 0;
    const  REDISPENSE_STATE_STARTED = 1;
    const  REDISPENSE_STATE_REVIEWED = 3;
    const  REDISPENSE_STATE_PREVIEW_MEDICINE = 2;
    const  REDISPENSE_STATE_APPROVE = 4;
    const  REDISPENSE_STATE_COMPLETE = 5;

    const  RESOLVE_STATUS_NOT_STARTED = 1;
    const  RESOLVE_STATUS_IN_PROGRESS = 2;
    const  RESOLVE_STATUS_COMPLETED = 3;

    const  INVOICE_PARTY_STATUS_NOT_STARTED = 1;
    const  INVOICE_PARTY_STATUS_IN_PROGRESS = 2;
    const  INVOICE_PARTY_STATUS_PHARMACIST_APPROVE = 3;
    const  INVOICE_PARTY_STATUS_MANAGEMENT_APPROVE = 4;
    const  INVOICE_PARTY_STATUS_COMPLETE = 5;
    const  XERO_COUNTRY = "SINGAPORE";
    const  MPA_CODE = "SGP";

    const DOCUMENT_NAME_DOCTOR_AGREEMENT = "Doctor's Subscriber Agreement";
    const DOCUMENT_NAME_PATIENT_TERM_OF_USE = "Patient's Terms of Use";
    const DOCUMENT_NAME_DOCTOR_USER_GUIDE = "Doctor's User Guide";
    const DOCUMENT_NAME_PATIENT_FAQ = "Patient's FAQ";
    const DOCUMENT_NAME_DOCTOR_FAQ = "Doctor's FAQ";
    const GMS_FEE_3RD_AGENT_MEDICINE = "1";
    const GMS_FEE_3RD_AGENT_PRESCRIPTION = "2";
    const GMS_FEE_3RD_AGENT_LIVECONSULT = "3";
    const GMS_TYPE_3RD_AGENT_LOCAL = "1";
    const GMS_TYPE_3RD_AGENT_OVERSEAS = "2";

    const AGENT_MINUMUM_FEE_PRIMARY_lOCAL = "1";
    const AGENT_MINUMUM_FEE_PRIMARY_INDONESIA = "2";
    const AGENT_MINUMUM_FEE_PRIMARY_EAST_MALAY = "3";
    const AGENT_MINUMUM_FEE_PRIMARY_WEST_MALAY = "4";
    const AGENT_MINUMUM_FEE_PRIMARY_INTERNATIONAL = "5";
    const AGENT_MINUMUM_FEE_SECONDARY_lOCAL = "6";
    const AGENT_MINUMUM_FEE_SECONDARY_INDONESIA = "7";
    const AGENT_MINUMUM_FEE_SECONDARY_EAST_MALAY = "8";
    const AGENT_MINUMUM_FEE_SECONDARY_WEST_MALAY = "9";
    const AGENT_MINUMUM_FEE_SECONDARY_INTERNATIONAL = "10";

    const AGENT_MINUMUM_FEE_NAME_lOCAL = "Singapore";
    const AGENT_MINUMUM_FEE_NAME_INDONESIA = "Indonesia";
    const AGENT_MINUMUM_FEE_NAME_EAST_MALAY = "East Malaysia";
    const AGENT_MINUMUM_FEE_NAME_WEST_MALAY = "West Malaysia";
    const AGENT_MINUMUM_FEE_NAME_INTERNATIONAL = "International";

}
