<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * XeroJournalLineSource
 *
 * @ORM\Table(name="xero_journal_line_source", indexes={@ORM\Index(name="rx_invoice_id", columns={"rx_invoice_id"}), @ORM\Index(name="supplier_invoice_id", columns={"invoice_upload_id"}), @ORM\Index(name="xero_journal_line_id", columns={"xero_journal_line_id"}), @ORM\Index(name="xero_region_id", columns={"xero_region_id"}), @ORM\Index(name="xero_tracking_option_id", columns={"xero_tracking_option_id"})})
* @ORM\Entity(repositoryClass="UtilBundle\Repository\XeroJournalLineSourceRepository")
 * @ORM\HasLifecycleCallbacks
 */
class XeroJournalLineSource
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
     * @var \Rx
     *
     * @ORM\ManyToOne(targetEntity="Rx")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="rx_invoice_id", referencedColumnName="id")
     * })
     */
    private $rx;

    /**
     * @var integer
     *
     * @ORM\Column(name="rx_invoice_id", type="integer", nullable=true)
     */
    private $rxInvoiceId;

    /**
     * @var integer
     *
     * @ORM\Column(name="invoice_upload_id", type="integer", nullable=true)
     */
    private $invoiceUploadId;

    /**
     * @var string
     *
     * @ORM\Column(name="calculated_amount_credit", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $calculatedAmountCredit;

    /**
     * @var string
     *
     * @ORM\Column(name="calculated_amount_debit", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $calculatedAmountDebit;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var \XeroJournalLine
     *
     * @ORM\ManyToOne(targetEntity="XeroJournalLine")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="xero_journal_line_id", referencedColumnName="id")
     * })
     */
    private $xeroJournalLine;

    /**
     * @var \XeroRegion
     *
     * @ORM\ManyToOne(targetEntity="XeroRegion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="xero_region_id", referencedColumnName="id")
     * })
     */
    private $xeroRegion;

    /**
     * @var \XeroTrackingOption
     *
     * @ORM\ManyToOne(targetEntity="XeroTrackingOption")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="xero_tracking_option_id", referencedColumnName="id")
     * })
     */
    private $xeroTrackingOption;



    /**
     * Gets triggered only on insert

     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdOn = new \DateTime("now");
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
     * Set rxInvoiceId
     *
     * @param integer $rxInvoiceId
     *
     * @return XeroJournalLineSource
     */
    public function setRxInvoiceId($rxInvoiceId)
    {
        $this->rxInvoiceId = $rxInvoiceId;

        return $this;
    }

    /**
     * Get rxInvoiceId
     *
     * @return integer
     */
    public function getRxInvoiceId()
    {
        return $this->rxInvoiceId;
    }

    /**
     * Set invoiceUploadId
     *
     * @param integer $invoiceUploadId
     *
     * @return XeroJournalLineSource
     */
    public function setInvoiceUploadId($invoiceUploadId)
    {
        $this->invoiceUploadId = $invoiceUploadId;

        return $this;
    }

    /**
     * Get invoiceUploadId
     *
     * @return integer
     */
    public function getInvoiceUploadId()
    {
        return $this->invoiceUploadId;
    }

    /**
     * Set calculatedAmount
     *
     * @param string $calculatedAmount
     *
     * @return XeroJournalLineSource
     */
    public function setCalculatedAmount($calculatedAmount)
    {
        $this->calculatedAmount = $calculatedAmount;

        return $this;
    }

    /**
     * Get calculatedAmount
     *
     * @return string
     */
    public function getCalculatedAmount()
    {
        return $this->calculatedAmount;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return XeroJournalLineSource
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
     * Set xeroJournalLine
     *
     * @param \UtilBundle\Entity\XeroJournalLine $xeroJournalLine
     *
     * @return XeroJournalLineSource
     */
    public function setXeroJournalLine(\UtilBundle\Entity\XeroJournalLine $xeroJournalLine = null)
    {
        $this->xeroJournalLine = $xeroJournalLine;

        return $this;
    }

    /**
     * Get xeroJournalLine
     *
     * @return \UtilBundle\Entity\XeroJournalLine
     */
    public function getXeroJournalLine()
    {
        return $this->xeroJournalLine;
    }

    /**
     * Set xeroRegion
     *
     * @param \UtilBundle\Entity\XeroRegion $xeroRegion
     *
     * @return XeroJournalLineSource
     */
    public function setXeroRegion(\UtilBundle\Entity\XeroRegion $xeroRegion = null)
    {
        $this->xeroRegion = $xeroRegion;

        return $this;
    }

    /**
     * Get xeroRegion
     *
     * @return \UtilBundle\Entity\XeroRegion
     */
    public function getXeroRegion()
    {
        return $this->xeroRegion;
    }

    /**
     * Set xeroTrackingOption
     *
     * @param \UtilBundle\Entity\XeroTrackingOption $xeroTrackingOption
     *
     * @return XeroJournalLineSource
     */
    public function setXeroTrackingOption(\UtilBundle\Entity\XeroTrackingOption $xeroTrackingOption = null)
    {
        $this->xeroTrackingOption = $xeroTrackingOption;

        return $this;
    }

    /**
     * Get xeroTrackingOption
     *
     * @return \UtilBundle\Entity\XeroTrackingOption
     */
    public function getXeroTrackingOption()
    {
        return $this->xeroTrackingOption;
    }

    /**
     * Set calculatedAmountCredit
     *
     * @param string $calculatedAmountCredit
     *
     * @return XeroJournalLineSource
     */
    public function setCalculatedAmountCredit($calculatedAmountCredit)
    {
        $this->calculatedAmountCredit = $calculatedAmountCredit;

        return $this;
    }

    /**
     * Get calculatedAmountCredit
     *
     * @return string
     */
    public function getCalculatedAmountCredit()
    {
        return $this->calculatedAmountCredit;
    }

    /**
     * Set calculatedAmountDedit
     *
     * @param string $calculatedAmountDedit
     *
     * @return XeroJournalLineSource
     */
    public function setCalculatedAmountDedit($calculatedAmountDedit)
    {
        $this->calculatedAmountDedit = $calculatedAmountDedit;

        return $this;
    }

    /**
     * Get calculatedAmountDedit
     *
     * @return string
     */
    public function getCalculatedAmountDedit()
    {
        return $this->calculatedAmountDedit;
    }

    /**
     * Set calculatedAmountDebit
     *
     * @param string $calculatedAmountDebit
     *
     * @return XeroJournalLineSource
     */
    public function setCalculatedAmountDebit($calculatedAmountDebit)
    {
        $this->calculatedAmountDebit = $calculatedAmountDebit;

        return $this;
    }

    /**
     * Get calculatedAmountDebit
     *
     * @return string
     */
    public function getCalculatedAmountDebit()
    {
        return $this->calculatedAmountDebit;
    }
}
