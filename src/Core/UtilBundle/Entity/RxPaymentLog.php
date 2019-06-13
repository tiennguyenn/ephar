<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * RxPaymentLog
 *
 * @ORM\Table(name="rx_payment_log")
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\RxPaymentLogRepository")
 * @ORM\HasLifecycleCallbacks
 */
class RxPaymentLog
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
     * @ORM\Column(name="order_ref", type="string", length=50, nullable=true)
     */
    private $orderRef;

    /**
     * @var string
     *
     * @ORM\Column(name="transaction_id", type="string",length=70, nullable=true)
     */
    private $transactionId;

    /**
     * @var string
     *
     * @ORM\Column(name="curr_code", type="string", length=3, nullable=true)
     */
    private $currCode;

    /**
     * @var string
     *
     * @ORM\Column(name="mps_mode", type="string", length=3, nullable=true)
     */
    private $mpsMode;

    /**
     * @var string
     *
     * @ORM\Column(name="pay_type", type="string", length=1, nullable=true)
     */
    private $payType;

    /**
     * @var string
     *
     * @ORM\Column(name="lang", type="string", length=1, nullable=true)
     */
    private $lang;

    /**
     * @var string
     *
     * @ORM\Column(name="pay_method", type="string", length=15, nullable=true)
     */
    private $payMethod;

    /**
     * @var string
     *
     * @ORM\Column(name="secure_hash", type="string", length=50, nullable=true)
     */
    private $secureHash;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="merchant_id", type="string", length=50, nullable=true)
     */
    private $merchantId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="payment_result", type="boolean", nullable=true)
     */
    private $paymentResult;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=10, nullable=true)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_type", type="string", length=15, nullable=true)
     */
    private $paymentType = 'captured';

    /**
     * @var string
     *
     * @ORM\Column(name="error_code", type="string", length=250, nullable=true)
     */
    private $errorCode;

    /**
     * @var string
     *
     * @ORM\Column(name="error_desc", type="string", length=250, nullable=true)
     */
    private $errorDesc;

    /**
     * @var string
     *
     * @ORM\Column(name="appcode", type="string", length=16, nullable=true)
     */
    private $appcode;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="paydate", type="datetime", nullable=true)
     */
    private $paydate;

    /**
     * @var string
     *
     * @ORM\Column(name="bank_code", type="string", length=8, nullable=true)
     */
    private $bankCode;

    /**
     * @var string
     *
     * @ORM\Column(name="beneficiary_name", type="string", length=100, nullable=true)
     */
    private $beneficiaryName;

    /**
     * @var string
     *
     * @ORM\Column(name="beneficiary_acc_no", type="string", length=100, nullable=true)
     */
    private $beneficiaryAccNo;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_gate", type="string", length=50, nullable=true)
     */
    private $paymentGate;

    /**
     * @var string
     *
     * @ORM\Column(name="api_log", type="text", nullable=true)
     */
    private $apiLog;

    /**
     * @var string
     *
     * @ORM\Column(name="refund_reason", type="string", length=250, nullable=true)
     */
    private $refundReason;

    /**
     * @var string
     *
     * @ORM\Column(name="refund_id", type="string", length=100, nullable=true)
     */
    private $refundId;

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
     * @var \ResolveRefund
     *
     * @ORM\OneToMany(targetEntity="ResolveRefund",mappedBy="rxPaymentLog", cascade={"persist", "remove" })
     */
    private $resolveRefunds;

    /**
     * @var \Rx
     *
     * @ORM\ManyToOne(targetEntity="Rx",inversedBy="rxPaymentLogs", cascade={"persist", "remove"} )
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="rx_id", referencedColumnName="id")
     * })
     */
    private $rx;

    public function __construct()
    {
        $this->resolveRefunds = new ArrayCollection();
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
     * Set orderRef
     *
     * @param string $orderRef
     *
     * @return RxPaymentLog
     */
    public function setOrderRef($orderRef)
    {
        $this->orderRef = $orderRef;

        return $this;
    }

    /**
     * Get orderRef
     *
     * @return string
     */
    public function getOrderRef()
    {
        return $this->orderRef;
    }

    /**
     * Set transactionId
     *
     * @param integer $transactionId
     *
     * @return RxPaymentLog
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;

        return $this;
    }

    /**
     * Get transactionId
     *
     * @return integer
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * Set currCode
     *
     * @param string $currCode
     *
     * @return RxPaymentLog
     */
    public function setCurrCode($currCode)
    {
        $this->currCode = $currCode;

        return $this;
    }

    /**
     * Get currCode
     *
     * @return string
     */
    public function getCurrCode()
    {
        return $this->currCode;
    }

    /**
     * Set mpsMode
     *
     * @param string $mpsMode
     *
     * @return RxPaymentLog
     */
    public function setMpsMode($mpsMode)
    {
        $this->mpsMode = $mpsMode;

        return $this;
    }

    /**
     * Get mpsMode
     *
     * @return string
     */
    public function getMpsMode()
    {
        return $this->mpsMode;
    }

    /**
     * Set payType
     *
     * @param string $payType
     *
     * @return RxPaymentLog
     */
    public function setPayType($payType)
    {
        $this->payType = $payType;

        return $this;
    }

    /**
     * Get payType
     *
     * @return string
     */
    public function getPayType()
    {
        return $this->payType;
    }

    /**
     * Set lang
     *
     * @param string $lang
     *
     * @return RxPaymentLog
     */
    public function setLang($lang)
    {
        $this->lang = $lang;

        return $this;
    }

    /**
     * Get lang
     *
     * @return string
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * Set payMethod
     *
     * @param string $payMethod
     *
     * @return RxPaymentLog
     */
    public function setPayMethod($payMethod)
    {
        $this->payMethod = $payMethod;

        return $this;
    }

    /**
     * Get payMethod
     *
     * @return string
     */
    public function getPayMethod()
    {
        return $this->payMethod;
    }

    /**
     * Set secureHash
     *
     * @param string $secureHash
     *
     * @return RxPaymentLog
     */
    public function setSecureHash($secureHash)
    {
        $this->secureHash = $secureHash;

        return $this;
    }

    /**
     * Get secureHash
     *
     * @return string
     */
    public function getSecureHash()
    {
        return $this->secureHash;
    }

    /**
     * Set amount
     *
     * @param string $amount
     *
     * @return RxPaymentLog
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set merchantId
     *
     * @param string $merchantId
     *
     * @return RxPaymentLog
     */
    public function setMerchantId($merchantId)
    {
        $this->merchantId = $merchantId;

        return $this;
    }

    /**
     * Get merchantId
     *
     * @return string
     */
    public function getMerchantId()
    {
        return $this->merchantId;
    }

    /**
     * Set paymentResult
     *
     * @param boolean $paymentResult
     *
     * @return RxPaymentLog
     */
    public function setPaymentResult($paymentResult)
    {
        $this->paymentResult = $paymentResult;

        return $this;
    }

    /**
     * Get paymentResult
     *
     * @return boolean
     */
    public function getPaymentResult()
    {
        return $this->paymentResult;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return RxPaymentLog
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set paymentType
     *
     * @param string $paymentType
     *
     * @return RxPaymentLog
     */
    public function setPaymentType($paymentType)
    {
        $this->paymentType = $paymentType;

        return $this;
    }

    /**
     * Get paymentType
     *
     * @return string
     */
    public function getPaymentType()
    {
        return $this->paymentType;
    }

    /**
     * Set errorCode
     *
     * @param string $errorCode
     *
     * @return RxPaymentLog
     */
    public function setErrorCode($errorCode)
    {
        $this->errorCode = $errorCode;

        return $this;
    }

    /**
     * Get errorCode
     *
     * @return string
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * Set errorDesc
     *
     * @param string $errorDesc
     *
     * @return RxPaymentLog
     */
    public function setErrorDesc($errorDesc)
    {
        $this->errorDesc = $errorDesc;

        return $this;
    }

    /**
     * Get errorDesc
     *
     * @return string
     */
    public function getErrorDesc()
    {
        return $this->errorDesc;
    }

    /**
     * Set appcode
     *
     * @param string $appcode
     *
     * @return RxPaymentLog
     */
    public function setAppcode($appcode)
    {
        $this->appcode = $appcode;

        return $this;
    }

    /**
     * Get appcode
     *
     * @return string
     */
    public function getAppcode()
    {
        return $this->appcode;
    }

    /**
     * Set paydate
     *
     * @param \DateTime $paydate
     *
     * @return RxPaymentLog
     */
    public function setPaydate($paydate)
    {
        $this->paydate = $paydate;

        return $this;
    }

    /**
     * Get paydate
     *
     * @return \DateTime
     */
    public function getPaydate()
    {
        return $this->paydate;
    }

    /**
     * Set refundReason
     *
     * @param string $refundReason
     *
     * @return RxPaymentLog
     */
    public function setRefundReason($refundReason)
    {
        $this->refundReason = $refundReason;

        return $this;
    }

    /**
     * Get refundReason
     *
     * @return string
     */
    public function getRefundReason()
    {
        return $this->refundReason;
    }

    /**
     * Set refundId
     *
     * @param string $refundId
     *
     * @return RxPaymentLog
     */
    public function setRefundId($refundId)
    {
        $this->refundId = $refundId;

        return $this;
    }

    /**
     * Get refundId
     *
     * @return string
     */
    public function getRefundId()
    {
        return $this->refundId;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return RxPaymentLog
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
     * @return RxPaymentLog
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
     * Add resolveRefund
     *
     * @param \UtilBundle\Entity\ResolveRefund $resolveRefund
     *
     * @return RxPaymentLog
     */
    public function addResolveRefund(\UtilBundle\Entity\ResolveRefund $resolveRefund)
    {
        $this->resolveRefunds[] = $resolveRefund;

        return $this;
    }

    /**
     * Remove resolveRefund
     *
     * @param \UtilBundle\Entity\ResolveRefund $resolveRefund
     */
    public function removeResolveRefund(\UtilBundle\Entity\ResolveRefund $resolveRefund)
    {
        $this->resolveRefunds->removeElement($resolveRefund);
    }

    /**
     * Get resolveRefunds
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getResolveRefunds()
    {
        return $this->resolveRefunds;
    }

    /**
     * Set rx
     *
     * @param \UtilBundle\Entity\Rx $rx
     *
     * @return RxPaymentLog
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
     * Set bankCode
     *
     * @param string $bankCode
     *
     * @return RxPaymentLog
     */
    public function setBankCode($bankCode)
    {
        $this->bankCode = $bankCode;

        return $this;
    }

    /**
     * Get bankCode
     *
     * @return string
     */
    public function getBankCode()
    {
        return $this->bankCode;
    }

    /**
     * Set beneficiaryName
     *
     * @param string $beneficiaryName
     *
     * @return RxPaymentLog
     */
    public function setBeneficiaryName($beneficiaryName)
    {
        $this->beneficiaryName = $beneficiaryName;

        return $this;
    }

    /**
     * Get beneficiaryName
     *
     * @return string
     */
    public function getBeneficiaryName()
    {
        return $this->beneficiaryName;
    }

    /**
     * Set beneficiaryAccNo
     *
     * @param string $beneficiaryAccNo
     *
     * @return RxPaymentLog
     */
    public function setBeneficiaryAccNo($beneficiaryAccNo)
    {
        $this->beneficiaryAccNo = $beneficiaryAccNo;

        return $this;
    }

    /**
     * Get beneficiaryAccNo
     *
     * @return string
     */
    public function getBeneficiaryAccNo()
    {
        return $this->beneficiaryAccNo;
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
     * Set paymentGate
     *
     * @param string $paymentGate
     *
     * @return RxPaymentLog
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
     * Get apiLog
     *
     * @return string
     */
    public function getApiLog()
    {
        return $this->apiLog;
    }

    /**
     * Set apiLog
     *
     * @param string $apiLog
     *
     * @return RxPaymentLog
     */
    public function setApiLog($apiLog)
    {
        $this->apiLog = $apiLog;

        return $this;
    }
}
