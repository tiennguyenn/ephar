<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AgentMonthlyStatementLine
 *
 * @ORM\Table(name="agent_monthly_statement_line", indexes={@ORM\Index(name="FK_agent_monthly_statement_line", columns={"agent_monthly_statement_id"}), @ORM\Index(name="FK_agent_monthly_statement_line_invoice_upload", columns={"invoice_upload_id"}), @ORM\Index(name="FK_agent_monthly_statement_line_agent", columns={"agent_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\AgentMonthlyStatementLineRepository")
 */
class AgentMonthlyStatementLine
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
     * @ORM\Column(name="agent_monthly_fee", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $agentMonthlyFee;    

    /**
     * @var string
     *
     * @ORM\Column(name="out_standing_amount", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $outStandingAmount;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean", nullable=false)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="bank_transaction", type="string", length=10, nullable=true)
     */
    private $bankTransaction;

    /**
     * @var string
     *
     * @ORM\Column(name="customer_reference", type="string", length=50, nullable=true)
     */
    private $customerReference;

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
     * @ORM\Column(name="total_amount", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $totalAmount;

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
     * @ORM\Column(name="agent_monthly_fee_gst", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $agentMonthlyFeeGst;

    /**
     * @var string
     *
     * @ORM\Column(name="exception_agent_monthly_fee", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $exceptionAgentMonthlyFee;

    /**
     * @var string
     *
     * @ORM\Column(name="patient_fee", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $patientFee = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="excluded_on", type="datetime", nullable=true)
     */
    private $excludedOn;

    /**
     * @var string
     *
     * @ORM\Column(name="excluded_by", type="string", length=250, nullable=true)
     */
    private $excludedBy;

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
     * @var \AgentMonthlyStatement
     *
     * @ORM\ManyToOne(targetEntity="AgentMonthlyStatement")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="agent_monthly_statement_id", referencedColumnName="id")
     * })
     */
    private $agentMonthlyStatement;

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
     * @var \InvoiceUpload
     *
     * @ORM\ManyToOne(targetEntity="InvoiceUpload")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="invoice_upload_id", referencedColumnName="id")
     * })
     */
    private $invoiceUpload;



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
     * Set agentMonthlyFee
     *
     * @param string $agentMonthlyFee
     * @return AgentMonthlyStatementLine
     */
    public function setAgentMonthlyFee($agentMonthlyFee)
    {
        $this->agentMonthlyFee = $agentMonthlyFee;

        return $this;
    }

    /**
     * Get agentMonthlyFee
     *
     * @return string 
     */
    public function getAgentMonthlyFee()
    {
        return $this->agentMonthlyFee;
    }

    /**
     * Set patientFee
     *
     * @param string $patientFee
     * @return AgentMonthlyStatementLine
     */
    public function setPatientFee($patientFee)
    {
        $this->patientFee = $patientFee;

        return $this;
    }

    /**
     * Get patientFee
     *
     * @return string 
     */
    public function getPatientFee()
    {
        return $this->patientFee;
    }

    /**
     * Set outStandingAmount
     *
     * @param string $outStandingAmount
     * @return AgentMonthlyStatementLine
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
     * Set status
     *
     * @param boolean $status
     * @return AgentMonthlyStatementLine
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
     * Set bankTransaction
     *
     * @param string $bankTransaction
     * @return AgentMonthlyStatementLine
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
     * Set customerReference
     *
     * @param string $customerReference
     * @return AgentMonthlyStatementLine
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
     * Set isExcludePayment
     *
     * @param boolean $isExcludePayment
     * @return AgentMonthlyStatementLine
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
     * @return AgentMonthlyStatementLine
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
     * Set totalAmount
     *
     * @param string $totalAmount
     * @return AgentMonthlyStatementLine
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
     * Set amountPaid
     *
     * @param string $amountPaid
     * @return AgentMonthlyStatementLine
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
     * @return AgentMonthlyStatementLine
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
     * Set agentMonthlyFeeGst
     *
     * @param string $agentMonthlyFeeGst
     * @return AgentMonthlyStatementLine
     */
    public function setAgentMonthlyFeeGst($agentMonthlyFeeGst)
    {
        $this->agentMonthlyFeeGst = $agentMonthlyFeeGst;

        return $this;
    }

    /**
     * Get agentMonthlyFeeGst
     *
     * @return string 
     */
    public function getAgentMonthlyFeeGst()
    {
        return $this->agentMonthlyFeeGst;
    }

    /**
     * Set exceptionAgentMonthlyFee
     *
     * @param string $exceptionAgentMonthlyFee
     * @return AgentMonthlyStatementLine
     */
    public function setExceptionAgentMonthlyFee($exceptionAgentMonthlyFee)
    {
        $this->exceptionAgentMonthlyFee = $exceptionAgentMonthlyFee;

        return $this;
    }

    /**
     * Get exceptionAgentMonthlyFee
     *
     * @return string 
     */
    public function getExceptionAgentMonthlyFee()
    {
        return $this->exceptionAgentMonthlyFee;
    }

    /**
     * Set excludedOn
     *
     * @param \DateTime $excludedOn
     * @return AgentMonthlyStatementLine
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
     * Set excludedBy
     *
     * @param string $excludedBy
     * @return AgentMonthlyStatementLine
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
     * Set issueStatus
     *
     * @param boolean $issueStatus
     * @return AgentMonthlyStatementLine
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
     * @return AgentMonthlyStatementLine
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
     * Set postDate
     *
     * @param \DateTime $postDate
     * @return AgentMonthlyStatementLine
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
     * @return AgentMonthlyStatementLine
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
     * Set agentMonthlyStatement
     *
     * @param \UtilBundle\Entity\AgentMonthlyStatement $agentMonthlyStatement
     * @return AgentMonthlyStatementLine
     */
    public function setAgentMonthlyStatement(\UtilBundle\Entity\AgentMonthlyStatement $agentMonthlyStatement = null)
    {
        $this->agentMonthlyStatement = $agentMonthlyStatement;

        return $this;
    }

    /**
     * Get agentMonthlyStatement
     *
     * @return \UtilBundle\Entity\AgentMonthlyStatement 
     */
    public function getAgentMonthlyStatement()
    {
        return $this->agentMonthlyStatement;
    }

    /**
     * Set agent
     *
     * @param \UtilBundle\Entity\Agent $agent
     * @return AgentMonthlyStatementLine
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
     * Set invoiceUpload
     *
     * @param \UtilBundle\Entity\InvoiceUpload $invoiceUpload
     * @return AgentMonthlyStatementLine
     */
    public function setInvoiceUpload(\UtilBundle\Entity\InvoiceUpload $invoiceUpload = null)
    {
        $this->invoiceUpload = $invoiceUpload;

        return $this;
    }

    /**
     * Get invoiceUpload
     *
     * @return \UtilBundle\Entity\InvoiceUpload 
     */
    public function getInvoiceUpload()
    {
        return $this->invoiceUpload;
    }
}
