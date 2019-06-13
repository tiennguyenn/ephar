<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * XeroPaymentLineSource
 *
 * @ORM\Table(name="xero_payment_line_source", indexes={@ORM\Index(name="FK_xero_payment_line_source", columns={"xero_payment_line_id"}), @ORM\Index(name="FK_xero_payment_line_source_1", columns={"xero_region_id"}), @ORM\Index(name="FK_xero_payment_line_source_tracking_option", columns={"xero_tracking_option_id"})})
* @ORM\Entity(repositoryClass="UtilBundle\Repository\XeroPaymentLineSourceRepository")
 * @ORM\HasLifecycleCallbacks
 */
class XeroPaymentLineSource
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
     * @var integer
     *
     * @ORM\Column(name="rx_invoice_id", type="integer", nullable=true)
     */
    private $rxInvoiceId;

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
     * @ORM\Column(name="invoice_upload_id",  type="string", length=50, nullable=true)
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
     * @var \XeroTrackingOption
     *
     * @ORM\ManyToOne(targetEntity="XeroTrackingOption", cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="xero_tracking_option_id", referencedColumnName="id")
     * })
     */
    private $xeroTrackingOption;

    /**
     * @var \XeroPaymentLine
     *
     * @ORM\ManyToOne(targetEntity="XeroPaymentLine", cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="xero_payment_line_id", referencedColumnName="id")
     * })
     */
    private $xeroPaymentLine;

    /**
     * @var \XeroRegion
     *
     * @ORM\ManyToOne(targetEntity="XeroRegion", cascade={"persist", "remove" })
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
     * @return XeroPaymentLineSource
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
     * @return XeroPaymentLineSource
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
     * @return XeroPaymentLineSource
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
     * @return XeroPaymentLineSource
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
     * Set xeroTrackingOption
     *
     * @param \UtilBundle\Entity\XeroTrackingOption $xeroTrackingOption
     *
     * @return XeroPaymentLineSource
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
     * Set xeroPaymentLine
     *
     * @param \UtilBundle\Entity\XeroPaymentLine $xeroPaymentLine
     *
     * @return XeroPaymentLineSource
     */
    public function setXeroPaymentLine(\UtilBundle\Entity\XeroPaymentLine $xeroPaymentLine = null)
    {
        $this->xeroPaymentLine = $xeroPaymentLine;

        return $this;
    }

    /**
     * Get xeroPaymentLine
     *
     * @return \UtilBundle\Entity\XeroPaymentLine
     */
    public function getXeroPaymentLine()
    {
        return $this->xeroPaymentLine;
    }

    /**
     * Set xeroRegion
     *
     * @param \UtilBundle\Entity\XeroRegion $xeroRegion
     *
     * @return XeroPaymentLineSource
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
