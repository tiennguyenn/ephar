<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PharmacyPoDaily
 *
 * @ORM\Table(name="pharmacy_po_daily", indexes={@ORM\Index(name="FK_pharmacy_po_daily_weekly", columns={"po_weekly_id"}), @ORM\Index(name="FK_pharmacy_po_daily_pharmacy", columns={"pharmacy_id"}), @ORM\Index(name="FK_pharmacy_po_daily_invoice_upload", columns={"invoice_upload_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\PharmacyPoDailyRepository")
 */
class PharmacyPoDaily
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
     * @var \DateTime
     *
     * @ORM\Column(name="po_date", type="datetime", nullable=false)
     */
    private $poDate;

    /**
     * @var string
     *
     * @ORM\Column(name="cycle", type="string", length=7, nullable=true)
     */
    private $cycle;

    /**
     * @var string
     *
     * @ORM\Column(name="po_number", type="string", length=25, nullable=false)
     */
    private $poNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="total_amount", type="decimal", precision=13, scale=2, nullable=false)
     */
    private $totalAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="out_standing_amount", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $outStandingAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="exclude_gst_amount", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $excludeGstAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="include_gst_amount", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $includeGstAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="gst_amount", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $gstAmount;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_exclude_payment", type="boolean", nullable=true)
     */
    private $isExcludePayment;

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
     * @var string
     *
     * @ORM\Column(name="filename", type="string", length=100, nullable=true)
     */
    private $filename;

    /**
     * @var string
     *
     * @ORM\Column(name="exclude_payment_note", type="text", length=65535, nullable=true)
     */
    private $excludePaymentNote;

    /**
     * @var integer
     *
     * @ORM\Column(name="issue_status", type="integer", nullable=true)
     */
    private $issueStatus;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private $status = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="times_sent", type="integer", nullable=true)
     */
    private $timesSent = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="sent_on", type="datetime", nullable=true)
     */
    private $sentOn;

    /**
     * @var string
     *
     * @ORM\Column(name="customer_reference", type="string", length=16, nullable=true)
     */
    private $customerReference;

    /**
     * @var string
     *
     * @ORM\Column(name="amount_paid", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $amountPaid;

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
     * @var \DateTime
     *
     * @ORM\Column(name="updated_on", type="datetime", nullable=true)
     */
    private $updatedOn;

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
     * @var \Pharmacy
     *
     * @ORM\ManyToOne(targetEntity="Pharmacy")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pharmacy_id", referencedColumnName="id")
     * })
     */
    private $pharmacy;

    /**
     * @var \PharmacyPoWeekly
     *
     * @ORM\ManyToOne(targetEntity="PharmacyPoWeekly")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="po_weekly_id", referencedColumnName="id")
     * })
     */
    private $poWeekly;

    /**
     *
     * @ORM\OneToMany(targetEntity="PharmacyPoDailyLine", mappedBy="poDaily", cascade={"persist", "remove"})
     */
    private $lines;

    public function __construct()
    {
        $this->lines = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set poDate
     *
     * @param \DateTime $poDate
     * @return PharmacyPoDaily
     */
    public function setPoDate($poDate)
    {
        $this->poDate = $poDate;

        return $this;
    }

    /**
     * Get poDate
     *
     * @return \DateTime 
     */
    public function getPoDate()
    {
        return $this->poDate;
    }

    /**
     * Set cycle
     *
     * @param string $cycle
     * @return PharmacyPoDaily
     */
    public function setCycle($cycle)
    {
        $this->cycle = $cycle;

        return $this;
    }

    /**
     * Get cycle
     *
     * @return string 
     */
    public function getCycle()
    {
        return $this->cycle;
    }

    /**
     * Set poNumber
     *
     * @param string $poNumber
     * @return PharmacyPoDaily
     */
    public function setPoNumber($poNumber)
    {
        $this->poNumber = $poNumber;

        return $this;
    }

    /**
     * Get poNumber
     *
     * @return string 
     */
    public function getPoNumber()
    {
        return $this->poNumber;
    }

    /**
     * Set totalAmount
     *
     * @param string $totalAmount
     * @return PharmacyPoDaily
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
     * Set outStandingAmount
     *
     * @param string $outStandingAmount
     * @return PharmacyPoDaily
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
     * Set excludeGstAmount
     *
     * @param string $excludeGstAmount
     * @return PharmacyPoDaily
     */
    public function setExcludeGstAmount($excludeGstAmount)
    {
        $this->excludeGstAmount = $excludeGstAmount;

        return $this;
    }

    /**
     * Get excludeGstAmount
     *
     * @return string 
     */
    public function getExcludeGstAmount()
    {
        return $this->excludeGstAmount;
    }

    /**
     * Set includeGstAmount
     *
     * @param string $includeGstAmount
     * @return PharmacyPoDaily
     */
    public function setIncludeGstAmount($includeGstAmount)
    {
        $this->includeGstAmount = $includeGstAmount;

        return $this;
    }

    /**
     * Get includeGstAmount
     *
     * @return string 
     */
    public function getIncludeGstAmount()
    {
        return $this->includeGstAmount;
    }

    /**
     * Set gstAmount
     *
     * @param string $gstAmount
     * @return PharmacyPoDaily
     */
    public function setGstAmount($gstAmount)
    {
        $this->gstAmount = $gstAmount;

        return $this;
    }

    /**
     * Get gstAmount
     *
     * @return string 
     */
    public function getGstAmount()
    {
        return $this->gstAmount;
    }

    /**
     * Set isExcludePayment
     *
     * @param boolean $isExcludePayment
     * @return PharmacyPoDaily
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
     * Set excludedOn
     *
     * @param \DateTime $excludedOn
     * @return PharmacyPoDaily
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
     * @return PharmacyPoDaily
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
     * Set filename
     *
     * @param string $filename
     * @return PharmacyPoDaily
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
     * Set excludePaymentNote
     *
     * @param string $excludePaymentNote
     * @return PharmacyPoDaily
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
     * Set issueStatus
     *
     * @param boolean $issueStatus
     * @return PharmacyPoDaily
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
     * Set status
     *
     * @param integer $status
     * @return PharmacyPoDaily
     */
    public function setStatus($status)
    {
        $this->status = $status;

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
     * Set timesSent
     *
     * @param integer $timesSent
     * @return PharmacyPoDaily
     */
    public function setTimesSent($timesSent)
    {
        $this->timesSent = $timesSent;

        return $this;
    }

    /**
     * Get timesSent
     *
     * @return integer 
     */
    public function getTimesSent()
    {
        return $this->timesSent;
    }

    /**
     * Set sentOn
     *
     * @param \DateTime $sentOn
     * @return PharmacyPoDaily
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
     * Set customerReference
     *
     * @param string $customerReference
     * @return PharmacyPoDaily
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
     * Set amountPaid
     *
     * @param string $amountPaid
     * @return PharmacyPoDaily
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
     * Set postDate
     *
     * @param \DateTime $postDate
     * @return PharmacyPoDaily
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
     * @return PharmacyPoDaily
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
     * @return PharmacyPoDaily
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
     * Set invoiceUpload
     *
     * @param \UtilBundle\Entity\InvoiceUpload $invoiceUpload
     * @return PharmacyPoDaily
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

    /**
     * Set pharmacy
     *
     * @param \UtilBundle\Entity\Pharmacy $pharmacy
     * @return PharmacyPoDaily
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
     * Set poWeekly
     *
     * @param \UtilBundle\Entity\PharmacyPoWeekly $poWeekly
     * @return PharmacyPoDaily
     */
    public function setPoWeekly(\UtilBundle\Entity\PharmacyPoWeekly $poWeekly = null)
    {
        $this->poWeekly = $poWeekly;

        return $this;
    }

    /**
     * Get poWeekly
     *
     * @return \UtilBundle\Entity\PharmacyPoWeekly 
     */
    public function getPoWeekly()
    {
        return $this->poWeekly;
    }

    /**
     * Add line
     *
     * @param \UtilBundle\Entity\PharmacyPoDailyLine $line
     *
     * @return PharmacyPoDaily
     */
    public function addLine(\UtilBundle\Entity\PharmacyPoDailyLine $line)
    {
        $this->lines[] = $line;

        return $this;
    }

    /**
     * Remove line
     *
     * @param \UtilBundle\Entity\PharmacyPoDailyLine $line
     */
    public function removeLine(\UtilBundle\Entity\PharmacyPoDailyLine $line)
    {
        $this->lines->removeElement($line);
    }

    /**
     * Get lines
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLines()
    {
        return $this->lines;
    }
}
