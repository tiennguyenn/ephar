<?php

namespace UtilBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * XeroPaymentLine
 *
 * @ORM\Table(name="xero_payment_line", indexes={@ORM\Index(name="FK_xero_payment_line", columns={"xero_mapping_id"}), @ORM\Index(name="FK_xero_payment_line_1", columns={"xero_payment_id"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class XeroPaymentLine
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
     * @ORM\Column(name="gmeds_code", type="string", length=50, nullable=false)
     */
    private $gmedsCode;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=240, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $amount;



    /**
     * @var integer
     *
     * @ORM\Column(name="xero_tracking_id", type="integer", nullable=true)
     */
    private $xeroTrackingId;

    /**
     * @var string
     *
     * @ORM\Column(name="invoice_number", type="string", length=250, nullable=true)
     */
    private $invoiceNumber;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var \XeroMapping
     *
     * @ORM\ManyToOne(targetEntity="XeroMapping", cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="xero_mapping_id", referencedColumnName="id")
     * })
     */
    private $xeroMapping;

    /**
     * @var \XeroPayment
     *
     * @ORM\ManyToOne(targetEntity="XeroPayment", cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="xero_payment_id", referencedColumnName="id")
     * })
     */
    private $xeroPayment;

    /**
     * @ORM\OneToMany(targetEntity="XeroPaymentLineSource", mappedBy="xeroPaymentLine", cascade={"persist", "remove" })
     */
    private $sources;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->sources = new ArrayCollection();
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set gmedsCode
     *
     * @param string $gmedsCode
     *
     * @return XeroPaymentLine
     */
    public function setGmedsCode($gmedsCode)
    {
        $this->gmedsCode = $gmedsCode;

        return $this;
    }

    /**
     * Get gmedsCode
     *
     * @return string
     */
    public function getGmedsCode()
    {
        return $this->gmedsCode;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return XeroPaymentLine
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set debitAmount
     *
     * @param string $debitAmount
     *
     * @return XeroPaymentLine
     */
    public function setDebitAmount($debitAmount)
    {
        $this->debitAmount = $debitAmount;

        return $this;
    }

    /**
     * Get debitAmount
     *
     * @return string
     */
    public function getDebitAmount()
    {
        return $this->debitAmount;
    }

    /**
     * Set creditAmount
     *
     * @param string $creditAmount
     *
     * @return XeroPaymentLine
     */
    public function setCreditAmount($creditAmount)
    {
        $this->creditAmount = $creditAmount;

        return $this;
    }

    /**
     * Get creditAmount
     *
     * @return string
     */
    public function getCreditAmount()
    {
        return $this->creditAmount;
    }

    /**
     * Set xeroTrackingId
     *
     * @param integer $xeroTrackingId
     *
     * @return XeroPaymentLine
     */
    public function setXeroTrackingId($xeroTrackingId)
    {
        $this->xeroTrackingId = $xeroTrackingId;

        return $this;
    }

    /**
     * Get xeroTrackingId
     *
     * @return integer
     */
    public function getXeroTrackingId()
    {
        return $this->xeroTrackingId;
    }

    /**
     * Set invoiceNumber
     *
     * @param string $invoiceNumber
     *
     * @return XeroPaymentLine
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
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return XeroPaymentLine
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
     * Set xeroMapping
     *
     * @param \UtilBundle\Entity\XeroMapping $xeroMapping
     *
     * @return XeroPaymentLine
     */
    public function setXeroMapping(\UtilBundle\Entity\XeroMapping $xeroMapping = null)
    {
        $this->xeroMapping = $xeroMapping;

        return $this;
    }

    /**
     * Get xeroMapping
     *
     * @return \UtilBundle\Entity\XeroMapping
     */
    public function getXeroMapping()
    {
        return $this->xeroMapping;
    }

    /**
     * Set xeroPayment
     *
     * @param \UtilBundle\Entity\XeroPayment $xeroPayment
     *
     * @return XeroPaymentLine
     */
    public function setXeroPayment(\UtilBundle\Entity\XeroPayment $xeroPayment = null)
    {
        $this->xeroPayment = $xeroPayment;

        return $this;
    }

    /**
     * Get xeroPayment
     *
     * @return \UtilBundle\Entity\XeroPayment
     */
    public function getXeroPayment()
    {
        return $this->xeroPayment;
    }

    /**
     * Add source
     *
     * @param \UtilBundle\Entity\XeroPaymentLineSource $source
     *
     * @return XeroPaymentLine
     */
    public function addSource(\UtilBundle\Entity\XeroPaymentLineSource $source)
    {
        $source->setXeroPaymentLine($this);
        $this->sources[] = $source;

        return $this;
    }

    /**
     * Remove source
     *
     * @param \UtilBundle\Entity\XeroPaymentLineSource $source
     */
    public function removeSource(\UtilBundle\Entity\XeroPaymentLineSource $source)
    {
        $this->sources->removeElement($source);
    }

    /**
     * Get sources
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSources()
    {
        return $this->sources;
    }

    /**
     * Set amount
     *
     * @param string $amount
     *
     * @return XeroPaymentLine
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
}
