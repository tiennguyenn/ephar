<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * XeroBillLineSource
 *
 * @ORM\Table(name="xero_bill_line_source", indexes={@ORM\Index(name="supplier_invoice_id", columns={"invoice_upload_id"}), @ORM\Index(name="xero_sale_line_id", columns={"xero_bill_line_id"}), @ORM\Index(name="rx_invoice_id", columns={"rx_invoice_id"}), @ORM\Index(name="xero_region_id", columns={"xero_region_id"}), @ORM\Index(name="xero_tracking_option_id", columns={"xero_tracking_option_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\XeroBillLineSourceRepository")
 * @ORM\HasLifecycleCallbacks
 */
class XeroBillLineSource
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
     * @ORM\Column(name="invoice_upload_id", type="integer", nullable=true)
     */
    private $invoiceUploadId;

    /**
     * @var string
     *
     * @ORM\Column(name="calculated_amount", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $calculatedAmount;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var \XeroBillLine
     *
     * @ORM\ManyToOne(targetEntity="XeroBillLine")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="xero_bill_line_id", referencedColumnName="id")
     * })
     */
    private $xeroBillLine;

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
     * @var \XeroRegion
     *
     * @ORM\ManyToOne(targetEntity="XeroRegion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="xero_region_id", referencedColumnName="id")
     * })
     */
    private $xeroRegion;


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
     * @return XeroBillLineSource
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
     * @return XeroBillLineSource
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
     * @return XeroBillLineSource
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
     * @return XeroBillLineSource
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
     * Set xeroBillLine
     *
     * @param \UtilBundle\Entity\XeroBillLine $xeroBillLine
     *
     * @return XeroBillLineSource
     */
    public function setXeroBillLine(\UtilBundle\Entity\XeroBillLine $xeroBillLine = null)
    {
        $this->xeroBillLine = $xeroBillLine;

        return $this;
    }

    /**
     * Get xeroBillLine
     *
     * @return \UtilBundle\Entity\XeroBillLine
     */
    public function getXeroBillLine()
    {
        return $this->xeroBillLine;
    }

    /**
     * Set xeroTrackingOption
     *
     * @param \UtilBundle\Entity\XeroTrackingOption $xeroTrackingOption
     *
     * @return XeroBillLineSource
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
     * Set xeroRegion
     *
     * @param \UtilBundle\Entity\XeroRegion $xeroRegion
     *
     * @return XeroBillLineSource
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
}
