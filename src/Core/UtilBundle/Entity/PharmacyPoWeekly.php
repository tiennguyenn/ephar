<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PharmacyPoWeekly
 *
 * @ORM\Table(name="pharmacy_po_weekly", indexes={@ORM\Index(name="FK_pharmacy_po_weekly", columns={"invoice_upload_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\PharmacyPoWeeklyRepository")
 */
class PharmacyPoWeekly
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
     * @ORM\Column(name="po_number", type="string", length=25, nullable=true)
     */
    private $poNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="cycle", type="string", length=7, nullable=true)
     */
    private $cycle;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="cycle_from_date", type="datetime", nullable=true)
     */
    private $cycleFromDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="cycle_to_date", type="datetime", nullable=true)
     */
    private $cycleToDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="weekly_po_date", type="datetime", nullable=true)
     */
    private $weeklyPoDate;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="exception_po_weekly", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $exceptionPoWeekly;

    /**
     * @var string
     *
     * @ORM\Column(name="out_standing_amount", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $outStandingAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="remark", type="string", length=250, nullable=true)
     */
    private $remark;

    /**
     * @var string
     *
     * @ORM\Column(name="amount_paid", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $amountPaid;

    /**
     * @var string
     *
     * @ORM\Column(name="after_excludement_amount", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $afterExcludementAmount;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="projected_payment_date", type="date", nullable=true)
     */
    private $projectedPaymentDate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="filename", type="string", length=100, nullable=true)
     */
    private $filename;

    /**
     * @var string
     *
     * @ORM\Column(name="customer_reference", type="string", length=16, nullable=true)
     */
    private $customerReference;

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
     *
     * @ORM\OneToMany(targetEntity="PharmacyPoDaily", mappedBy="poWeekly", cascade={"persist", "remove"})
     */
    private $dailies;

    /**
     * @var Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="PharmacyPoDaily", mappedBy="poWeekly")
     */
    private $poDaily;

    public function __construct()
    {
        $this->dailies = new \Doctrine\Common\Collections\ArrayCollection();
        $this->poDaily = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getPoDaily()
    {
        return $this->poDaily;
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
     * Set poNumber
     *
     * @param string $poNumber
     * @return PharmacyPoWeekly
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
     * Set cycle
     *
     * @param string $cycle
     * @return PharmacyPoWeekly
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
     * Set cycleFromDate
     *
     * @param \DateTime $cycleFromDate
     * @return PharmacyPoWeekly
     */
    public function setCycleFromDate($cycleFromDate)
    {
        $this->cycleFromDate = $cycleFromDate;

        return $this;
    }

    /**
     * Get cycleFromDate
     *
     * @return \DateTime 
     */
    public function getCycleFromDate()
    {
        return $this->cycleFromDate;
    }

    /**
     * Set cycleToDate
     *
     * @param \DateTime $cycleToDate
     * @return PharmacyPoWeekly
     */
    public function setCycleToDate($cycleToDate)
    {
        $this->cycleToDate = $cycleToDate;

        return $this;
    }

    /**
     * Get cycleToDate
     *
     * @return \DateTime 
     */
    public function getCycleToDate()
    {
        return $this->cycleToDate;
    }

    /**
     * Set weeklyPoDate
     *
     * @param \DateTime $weeklyPoDate
     * @return PharmacyPoWeekly
     */
    public function setWeeklyPoDate($weeklyPoDate)
    {
        $this->weeklyPoDate = $weeklyPoDate;

        return $this;
    }

    /**
     * Get weeklyPoDate
     *
     * @return \DateTime 
     */
    public function getWeeklyPoDate()
    {
        return $this->weeklyPoDate;
    }

    /**
     * Set amount
     *
     * @param string $amount
     * @return PharmacyPoWeekly
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
     * Set exceptionPoWeekly
     *
     * @param string $exceptionPoWeekly
     * @return PharmacyPoWeekly
     */
    public function setExceptionPoWeekly($exceptionPoWeekly)
    {
        $this->exceptionPoWeekly = $exceptionPoWeekly;

        return $this;
    }

    /**
     * Get exceptionPoWeekly
     *
     * @return string 
     */
    public function getExceptionPoWeekly()
    {
        return $this->exceptionPoWeekly;
    }

    /**
     * Set outStandingAmount
     *
     * @param string $outStandingAmount
     * @return PharmacyPoWeekly
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
     * Set remark
     *
     * @param string $remark
     * @return PharmacyPoWeekly
     */
    public function setRemark($remark)
    {
        $this->remark = $remark;

        return $this;
    }

    /**
     * Get remark
     *
     * @return string 
     */
    public function getRemark()
    {
        return $this->remark;
    }

    /**
     * Set amountPaid
     *
     * @param string $amountPaid
     * @return PharmacyPoWeekly
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
     * Set afterExcludementAmount
     *
     * @param string $afterExcludementAmount
     * @return PharmacyPoWeekly
     */
    public function setAfterExcludementAmount($afterExcludementAmount)
    {
        $this->afterExcludementAmount = $afterExcludementAmount;

        return $this;
    }

    /**
     * Get afterExcludementAmount
     *
     * @return string 
     */
    public function getAfterExcludementAmount()
    {
        return $this->afterExcludementAmount;
    }

    /**
     * Set projectedPaymentDate
     *
     * @param \DateTime $projectedPaymentDate
     * @return PharmacyPoWeekly
     */
    public function setProjectedPaymentDate($projectedPaymentDate)
    {
        $this->projectedPaymentDate = $projectedPaymentDate;

        return $this;
    }

    /**
     * Get projectedPaymentDate
     *
     * @return \DateTime 
     */
    public function getProjectedPaymentDate()
    {
        return $this->projectedPaymentDate;
    }

    /**
     * Set status
     *
     * @param boolean $status
     * @return PharmacyPoWeekly
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
     * Set filename
     *
     * @param string $filename
     * @return PharmacyPoWeekly
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
     * Set customerReference
     *
     * @param string $customerReference
     * @return PharmacyPoWeekly
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
     * Set postDate
     *
     * @param \DateTime $postDate
     * @return PharmacyPoWeekly
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
     * @return PharmacyPoWeekly
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
     * @return PharmacyPoWeekly
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
     * @return PharmacyPoWeekly
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
     * Add daily
     *
     * @param \UtilBundle\Entity\PharmacyPoDaily $daily
     *
     * @return PharmacyPoWeekly
     */
    public function addDaily(\UtilBundle\Entity\PharmacyPoDaily $daily)
    {
        $this->dailies[] = $daily;

        return $this;
    }

    /**
     * Remove daily
     *
     * @param \UtilBundle\Entity\PharmacyPoDaily $daily
     */
    public function removeDaily(\UtilBundle\Entity\PharmacyPoDaily $daily)
    {
        $this->dailies->removeElement($daily);
    }

    /**
     * Get dailies
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDailies()
    {
        return $this->dailies;
    }

    /**
     * Add poDaily
     *
     * @param \UtilBundle\Entity\PharmacyPoDaily $poDaily
     *
     * @return PharmacyPoWeekly
     */
    public function addPoDaily(\UtilBundle\Entity\PharmacyPoDaily $poDaily)
    {
        $this->poDaily[] = $poDaily;

        return $this;
    }

    /**
     * Remove poDaily
     *
     * @param \UtilBundle\Entity\PharmacyPoDaily $poDaily
     */
    public function removePoDaily(\UtilBundle\Entity\PharmacyPoDaily $poDaily)
    {
        $this->poDaily->removeElement($poDaily);
    }
}
