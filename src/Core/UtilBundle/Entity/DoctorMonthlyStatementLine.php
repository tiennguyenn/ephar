<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DoctorMonthlyStatementLine
 *
 * @ORM\Table(name="doctor_monthly_statement_line", indexes={@ORM\Index(name="FK_doctor_monthly_statemement_line", columns={"doctor_monthly_statement_id"}), @ORM\Index(name="FK_doctor_monthly_statemement_line_doctor", columns={"doctor_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\DoctorMonthlyStatementLineRepository")
 */
class DoctorMonthlyStatementLine
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
     * @ORM\Column(name="doctor_monthly_fee", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $doctorMonthlyFee;

    /**
     * @var string
     *
     * @ORM\Column(name="out_standing_amount", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $outStandingAmount;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean", nullable=true)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="customer_reference", type="string", length=50, nullable=true)
     */
    private $customerReference;

    /**
     * @var string
     *
     * @ORM\Column(name="bank_transaction", type="string", length=10, nullable=true)
     */
    private $bankTransaction;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_exclude_payment", type="boolean", nullable=true)
     */
    private $isExcludePayment;

    /**
     * @var string
     *
     * @ORM\Column(name="exclude_payment_note", type="text", length=65535, nullable=true)
     */
    private $excludePaymentNote;

    /**
     * @var string
     *
     * @ORM\Column(name="excluded_by", type="string", length=250, nullable=true)
     */
    private $excludedBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="excluded_on", type="datetime", nullable=true)
     */
    private $excludedOn;

    /**
     * @var boolean
     *
     * @ORM\Column(name="issue_status", type="boolean", nullable=true)
     */
    private $issueStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="filename", type="string", length=100, nullable=true)
     */
    private $filename;

    /**
     * @var string
     *
     * @ORM\Column(name="invoice_filename", type="string", length=100, nullable=true)
     */
    private $invoiceFilename;

    /**
     * @var string
     *
     * @ORM\Column(name="total_amount", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $totalAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="invoice_number", type="string", length=50, nullable=true)
     */
    private $invoiceNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="amount_paid", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $amountPaid;

    /**
     * @var string
     *
     * @ORM\Column(name="exception_statement", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $exceptionStatement;

    /**
     * @var string
     *
     * @ORM\Column(name="exception_doctor_monthly_fee", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $exceptionDoctorMonthlyFee;

    /**
     * @var string
     *
     * @ORM\Column(name="order_value", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $orderValue = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="post_date", type="datetime", nullable=true)
     */
    private $postDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var \DoctorMonthlyStatement
     *
     * @ORM\ManyToOne(targetEntity="DoctorMonthlyStatement")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="doctor_monthly_statement_id", referencedColumnName="id")
     * })
     */
    private $doctorMonthlyStatement;

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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set doctorMonthlyFee
     *
     * @param string $doctorMonthlyFee
     * @return DoctorMonthlyStatementLine
     */
    public function setDoctorMonthlyFee($doctorMonthlyFee)
    {
        $this->doctorMonthlyFee = $doctorMonthlyFee;

        return $this;
    }

    /**
     * Get doctorMonthlyFee
     *
     * @return string 
     */
    public function getDoctorMonthlyFee()
    {
        return $this->doctorMonthlyFee;
    }

    /**
     * Set outStandingAmount
     *
     * @param string $outStandingAmount
     * @return DoctorMonthlyStatementLine
     */
    public function setOutStandingAmount($outStandingAmount)
    {
        $this->outStandingAmount = $outStandingAmount;

        return $this;
    }

    /**
     * Get outStandingAmount
     *
     * @return string 
     */
    public function getOutStandingAmount()
    {
        return $this->outStandingAmount;
    }

    /**
     * Set orderValue
     *
     * @param string $orderValue
     * @return DoctorMonthlyStatementLine
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
     * Set status
     *
     * @param boolean $status
     * @return DoctorMonthlyStatementLine
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set customerReference
     *
     * @param string $customerReference
     * @return DoctorMonthlyStatementLine
     */
    public function setCustomerReference($customerReference)
    {
        $this->customerReference = $customerReference;

        return $this;
    }

    /**
     * Get customerReference
     *
     * @return string 
     */
    public function getCustomerReference()
    {
        return $this->customerReference;
    }

    /**
     * Set bankTransaction
     *
     * @param string $bankTransaction
     * @return DoctorMonthlyStatementLine
     */
    public function setBankTransaction($bankTransaction)
    {
        $this->bankTransaction = $bankTransaction;

        return $this;
    }

    /**
     * Get bankTransaction
     *
     * @return string 
     */
    public function getBankTransaction()
    {
        return $this->bankTransaction;
    }

    /**
     * Set isExcludePayment
     *
     * @param boolean $isExcludePayment
     * @return DoctorMonthlyStatementLine
     */
    public function setIsExcludePayment($isExcludePayment)
    {
        $this->isExcludePayment = $isExcludePayment;

        return $this;
    }

    /**
     * Get isExcludePayment
     *
     * @return boolean 
     */
    public function getIsExcludePayment()
    {
        return $this->isExcludePayment;
    }

    /**
     * Set excludePaymentNote
     *
     * @param string $excludePaymentNote
     * @return DoctorMonthlyStatementLine
     */
    public function setExcludePaymentNote($excludePaymentNote)
    {
        $this->excludePaymentNote = $excludePaymentNote;

        return $this;
    }

    /**
     * Get excludePaymentNote
     *
     * @return string 
     */
    public function getExcludePaymentNote()
    {
        return $this->excludePaymentNote;
    }

    /**
     * Set excludedBy
     *
     * @param string $excludedBy
     * @return DoctorMonthlyStatementLine
     */
    public function setExcludedBy($excludedBy)
    {
        $this->excludedBy = $excludedBy;

        return $this;
    }

    /**
     * Get excludedBy
     *
     * @return string 
     */
    public function getExcludedBy()
    {
        return $this->excludedBy;
    }

    /**
     * Set excludedOn
     *
     * @param \DateTime $excludedOn
     * @return DoctorMonthlyStatementLine
     */
    public function setExcludedOn($excludedOn)
    {
        $this->excludedOn = $excludedOn;

        return $this;
    }

    /**
     * Get excludedOn
     *
     * @return \DateTime 
     */
    public function getExcludedOn()
    {
        return $this->excludedOn;
    }

    /**
     * Set issueStatus
     *
     * @param boolean $issueStatus
     * @return DoctorMonthlyStatementLine
     */
    public function setIssueStatus($issueStatus)
    {
        $this->issueStatus = $issueStatus;

        return $this;
    }

    /**
     * Get issueStatus
     *
     * @return boolean 
     */
    public function getIssueStatus()
    {
        return $this->issueStatus;
    }

    /**
     * Set filename
     *
     * @param string $filename
     * @return DoctorMonthlyStatementLine
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string 
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set invoiceFilename
     *
     * @param string $invoiceFilename
     * @return DoctorMonthlyStatementLine
     */
    public function setInvoiceFilename($invoiceFilename)
    {
        $this->invoiceFilename = $invoiceFilename;

        return $this;
    }

    /**
     * Get invoiceFilename
     *
     * @return string 
     */
    public function getInvoiceFilename()
    {
        return $this->invoiceFilename;
    }

    /**
     * Set totalAmount
     *
     * @param string $totalAmount
     * @return DoctorMonthlyStatementLine
     */
    public function setTotalAmount($totalAmount)
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    /**
     * Get totalAmount
     *
     * @return string 
     */
    public function getTotalAmount()
    {
        return $this->totalAmount;
    }

    /**
     * Set invoiceNumber
     *
     * @param string $invoiceNumber
     * @return DoctorMonthlyStatementLine
     */
    public function setInvoiceNumber($invoiceNumber)
    {
        $this->invoiceNumber = $invoiceNumber;

        return $this;
    }

    /**
     * Get invoiceNumber
     *
     * @return string 
     */
    public function getInvoiceNumber()
    {
        return $this->invoiceNumber;
    }

    /**
     * Set amountPaid
     *
     * @param string $amountPaid
     * @return DoctorMonthlyStatementLine
     */
    public function setAmountPaid($amountPaid)
    {
        $this->amountPaid = $amountPaid;

        return $this;
    }

    /**
     * Get amountPaid
     *
     * @return string 
     */
    public function getAmountPaid()
    {
        return $this->amountPaid;
    }

    /**
     * Set exceptionStatement
     *
     * @param string $exceptionStatement
     * @return DoctorMonthlyStatementLine
     */
    public function setExceptionStatement($exceptionStatement)
    {
        $this->exceptionStatement = $exceptionStatement;

        return $this;
    }

    /**
     * Get exceptionStatement
     *
     * @return string 
     */
    public function getExceptionStatement()
    {
        return $this->exceptionStatement;
    }

    /**
     * Set exceptionDoctorMonthlyFee
     *
     * @param string $exceptionDoctorMonthlyFee
     * @return DoctorMonthlyStatementLine
     */
    public function setExceptionDoctorMonthlyFee($exceptionDoctorMonthlyFee)
    {
        $this->exceptionDoctorMonthlyFee = $exceptionDoctorMonthlyFee;

        return $this;
    }

    /**
     * Get exceptionDoctorMonthlyFee
     *
     * @return string 
     */
    public function getExceptionDoctorMonthlyFee()
    {
        return $this->exceptionDoctorMonthlyFee;
    }

    /**
     * Set postDate
     *
     * @param \DateTime $postDate
     * @return DoctorMonthlyStatementLine
     */
    public function setPostDate($postDate)
    {
        $this->postDate = $postDate;

        return $this;
    }

    /**
     * Get postDate
     *
     * @return \DateTime 
     */
    public function getPostDate()
    {
        return $this->postDate;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return DoctorMonthlyStatementLine
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
     * Set doctorMonthlyStatement
     *
     * @param \UtilBundle\Entity\DoctorMonthlyStatement $doctorMonthlyStatement
     * @return DoctorMonthlyStatementLine
     */
    public function setDoctorMonthlyStatement(\UtilBundle\Entity\DoctorMonthlyStatement $doctorMonthlyStatement = null)
    {
        $this->doctorMonthlyStatement = $doctorMonthlyStatement;

        return $this;
    }

    /**
     * Get doctorMonthlyStatement
     *
     * @return \UtilBundle\Entity\DoctorMonthlyStatement 
     */
    public function getDoctorMonthlyStatement()
    {
        return $this->doctorMonthlyStatement;
    }

    /**
     * Set doctor
     *
     * @param \UtilBundle\Entity\Doctor $doctor
     * @return DoctorMonthlyStatementLine
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
}
