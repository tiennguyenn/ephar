<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * XeroSaleLineSource
 *
 * @ORM\Table(name="xero_sale_line_source", indexes={@ORM\Index(name="rx_invoice_id", columns={"rx_invoice_id"}), @ORM\Index(name="xero_sale_line_id", columns={"xero_sale_line_id"}), @ORM\Index(name="xero_tracking_option_id", columns={"xero_tracking_option_id"}), @ORM\Index(name="xero_region_id", columns={"xero_region_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\XeroSaleLineSourceRepository")
 * @ORM\HasLifecycleCallbacks
 */
class XeroSaleLineSource
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
     * @var string
     *
     * @ORM\Column(name="calculated_amount", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $calculatedAmount;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="date", nullable=true)
     */
    private $createdOn;

    /**
     * @var \XeroSaleLine
     *
     * @ORM\ManyToOne(targetEntity="XeroSaleLine")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="xero_sale_line_id", referencedColumnName="id")
     * })
     */
    private $xeroSaleLine;

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
     * @return XeroSaleLineSource
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
     * Set calculatedAmount
     *
     * @param string $calculatedAmount
     *
     * @return XeroSaleLineSource
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
     * @return XeroSaleLineSource
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
     * Set xeroSaleLine
     *
     * @param \UtilBundle\Entity\XeroSaleLine $xeroSaleLine
     *
     * @return XeroSaleLineSource
     */
    public function setXeroSaleLine(\UtilBundle\Entity\XeroSaleLine $xeroSaleLine = null)
    {
        $this->xeroSaleLine = $xeroSaleLine;

        return $this;
    }

    /**
     * Get xeroSaleLine
     *
     * @return \UtilBundle\Entity\XeroSaleLine
     */
    public function getXeroSaleLine()
    {
        return $this->xeroSaleLine;
    }

    /**
     * Set xeroTrackingOption
     *
     * @param \UtilBundle\Entity\XeroTrackingOption $xeroTrackingOption
     *
     * @return XeroSaleLineSource
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
     * @return XeroSaleLineSource
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
