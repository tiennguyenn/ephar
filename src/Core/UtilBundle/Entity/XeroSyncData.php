<?php

namespace UtilBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
/**
 * XeroSyncData
 *
 * @ORM\Table(name="xero_sync_data", indexes={@ORM\Index(name="FK_xero_sync_data", columns={"xero_batch_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\XeroSyncDataRepository")
 * @ORM\HasLifecycleCallbacks
 */
class XeroSyncData
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
     * @ORM\Column(name="xmldoc", type="blob", length=16777215, nullable=false)
     */
    private $xmldoc;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_local", type="boolean", nullable=false)
     */
    private $isLocal = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_synced", type="boolean", nullable=false)
     */
    private $isSynced = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="xero_guide", type="string", length=38, nullable=true)
     */
    private $xeroGuid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="synced_on", type="datetime", nullable=true)
     */
    private $syncedOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_retry_on", type="datetime", nullable=true)
     */
    private $lastRetryOn;

    /**
     * @var string
     *
     * @ORM\Column(name="xero_total", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $xeroTotal;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=false)
     */
    private $createdOn;

    /**
     * @var string
     *
     * @ORM\Column(name="created_by", type="string", length=50, nullable=true)
     */
    private $createdBy;

    /**
     * @var \XeroSyncData
     *
     * @ORM\ManyToOne(targetEntity="XeroSyncData", cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     * })
     */
    private $parent;

    /**
     * @var \XeroDocumentType
     *
     * @ORM\ManyToOne(targetEntity="XeroDocumentType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="xero_document_type_id", referencedColumnName="id")
     * })
     */
    private $xeroDocumentType;

    /**
     * @var string
     *
     * @ORM\Column(name="payee", type="string", length=250, nullable=true)
     */
    private $payee;

    /**
     * @var \XeroBatch
     *
     * @ORM\ManyToOne(targetEntity="XeroBatch", cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="xero_batch_id", referencedColumnName="id")
     * })
     */
    private $xeroBatch;

    /**
     * @var \XeroMapping
     *
     * @ORM\ManyToOne(targetEntity="XeroMapping")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="xero_mapping_id", referencedColumnName="id")
     * })
     */
    private $xeroMapping;

    /**
     * @ORM\OneToMany(targetEntity="XeroBill", mappedBy="xeroSyncData", cascade={"persist", "remove" })
     */
    private $bills;

    /**
     * @ORM\OneToMany(targetEntity="XeroPayment", mappedBy="xeroSyncData", cascade={"persist", "remove" })
     */
    private $payments;

    /**
     * @ORM\OneToMany(targetEntity="XeroSale", mappedBy="xeroSyncData", cascade={"persist", "remove" })
     */
    private $sales;

    /**
     * @ORM\OneToMany(targetEntity="XeroJournal", mappedBy="xeroSyncData", cascade={"persist", "remove" })
     */
    private $journals;
    /**
     * @ORM\OneToMany(targetEntity="XeroSyncDataLog", mappedBy="xeroSyncData", cascade={"persist", "remove" })
     */
    private $logs;

    /**
     * @ORM\OneToMany(targetEntity="XeroSyncData", mappedBy="parent", cascade={"persist", "remove" })
     */
    private $children;



    /**
     * Constructor
     */
    public function __construct()
    {
        $this->bills = new ArrayCollection();
        $this->sales = new ArrayCollection();
        $this->journals = new ArrayCollection();
        $this->logs = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->payments = new ArrayCollection();
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
     * Get xeroDocumentType
     *
     * @return \UtilBundle\Entity\XeroDocumentType
     */
    public function getXeroDocumentType()
    {
        return $this->xeroDocumentType;
    }


    /**
     * Set xmldoc
     *
     * @param string $xmldoc
     *
     * @return XeroSyncData
     */
    public function setXmldoc($xmldoc)
    {
        $this->xmldoc = $xmldoc;

        return $this;
    }

    /**
     * Get xmldoc
     *
     * @return string
     */
    public function getXmldoc()
    {
        return $this->xmldoc;
    }

    /**
     * Set isSynced
     *
     * @param boolean $isSynced
     *
     * @return XeroSyncData
     */
    public function setIsSynced($isSynced)
    {
        $this->isSynced = $isSynced;

        return $this;
    }

    /**
     * Get isSynced
     *
     * @return boolean
     */
    public function getIsSynced()
    {
        return $this->isSynced;
    }

    /**
     * Set syncedOn
     *
     * @param \DateTime $syncedOn
     *
     * @return XeroSyncData
     */
    public function setSyncedOn($syncedOn)
    {
        $this->syncedOn = $syncedOn;

        return $this;
    }

    /**
     * Get syncedOn
     *
     * @return \DateTime
     */
    public function getSyncedOn()
    {
        return $this->syncedOn;
    }

    /**
     * Set lastRetryOn
     *
     * @param \DateTime $lastRetryOn
     *
     * @return XeroSyncData
     */
    public function setLastRetryOn($lastRetryOn)
    {
        $this->lastRetryOn = $lastRetryOn;

        return $this;
    }

    /**
     * Get lastRetryOn
     *
     * @return \DateTime
     */
    public function getLastRetryOn()
    {
        return $this->lastRetryOn;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return XeroSyncData
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
     * Set createdBy
     *
     * @param string $createdBy
     *
     * @return XeroSyncData
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return string
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set xeroBatch
     *
     * @param \UtilBundle\Entity\XeroBatch $xeroBatch
     *
     * @return XeroSyncData
     */
    public function setXeroBatch(\UtilBundle\Entity\XeroBatch $xeroBatch = null)
    {
        $this->xeroBatch = $xeroBatch;

        return $this;
    }

    /**
     * Get xeroBatch
     *
     * @return \UtilBundle\Entity\XeroBatch
     */
    public function getXeroBatch()
    {
        return $this->xeroBatch;
    }

    /**
     * Add bill
     *
     * @param \UtilBundle\Entity\XeroBill $bill
     *
     * @return XeroSyncData
     */
    public function addBill(\UtilBundle\Entity\XeroBill $bill)
    {
        $bill->setXeroSyncData($this);
        $this->bills[] = $bill;

        return $this;
    }

    /**
     * Remove bill
     *
     * @param \UtilBundle\Entity\XeroBill $bill
     */
    public function removeBill(\UtilBundle\Entity\XeroBill $bill)
    {
        $this->bills->removeElement($bill);
    }

    /**
     * Get bills
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBills()
    {
        return $this->bills;
    }

    /**
     * Add sale
     *
     * @param \UtilBundle\Entity\XeroSale $sale
     *
     * @return XeroSyncData
     */
    public function addSale(\UtilBundle\Entity\XeroSale $sale)
    {
        $sale->setXeroSyncData($this);
        $this->sales[] = $sale;

        return $this;
    }

    /**
     * Remove sale
     *
     * @param \UtilBundle\Entity\XeroSale $sale
     */
    public function removeSale(\UtilBundle\Entity\XeroSale $sale)
    {
        $this->sales->removeElement($sale);
    }

    /**
     * Get sales
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSales()
    {
        return $this->sales;
    }

    /**
     * Add journal
     *
     * @param \UtilBundle\Entity\XeroJournal $journal
     *
     * @return XeroSyncData
     */
    public function addJournal(\UtilBundle\Entity\XeroJournal $journal)
    {
        $journal->setXeroSyncData($this);
        $this->journals[] = $journal;

        return $this;
    }

    /**
     * Remove journal
     *
     * @param \UtilBundle\Entity\XeroJournal $journal
     */
    public function removeJournal(\UtilBundle\Entity\XeroJournal $journal)
    {
        $this->journals->removeElement($journal);
    }

    /**
     * Get journals
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getJournals()
    {
        return $this->journals;
    }

    /**
     * Add log
     *
     * @param \UtilBundle\Entity\XeroSyncDataLog $log
     *
     * @return XeroSyncData
     */
    public function addLog(\UtilBundle\Entity\XeroSyncDataLog $log)
    {
        $log->setXeroSyncData($this);
        $this->logs[] = $log;

        return $this;
    }

    /**
     * Remove log
     *
     * @param \UtilBundle\Entity\XeroSyncDataLog $log
     */
    public function removeLog(\UtilBundle\Entity\XeroSyncDataLog $log)
    {
        $this->logs->removeElement($log);
    }

    /**
     * Get logs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLogs()
    {
        return $this->logs;
    }

    /**
     * Set parent
     *
     * @param \UtilBundle\Entity\XeroSyncData $parent
     *
     * @return XeroSyncData
     */
    public function setParent(\UtilBundle\Entity\XeroSyncData $parent = null)
    {

        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \UtilBundle\Entity\XeroSyncData
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set payee
     *
     * @param string $payee
     *
     * @return XeroSyncData
     */
    public function setPayee($payee)
    {
        $this->payee = $payee;

        return $this;
    }

    /**
     * Get payee
     *
     * @return string
     */
    public function getPayee()
    {
        return $this->payee;
    }

    /**
     * Set xeroDocumentType
     *
     * @param \UtilBundle\Entity\XeroDocumentType $xeroDocumentType
     *
     * @return XeroSyncData
     */
    public function setXeroDocumentType(\UtilBundle\Entity\XeroDocumentType $xeroDocumentType = null)
    {
        $this->xeroDocumentType = $xeroDocumentType;

        return $this;
    }


  
    /**
     * Set xeroGuid
     *
     * @param string $xeroGuid
     *
     * @return XeroSyncData
     */
    public function setXeroGuid($xeroGuid)
    {
        $this->xeroGuid = $xeroGuid;

        return $this;
    }

    /**
     * Get xeroGuid
     *
     * @return string
     */
    public function getXeroGuid()
    {
        return $this->xeroGuid;
    }

    /**
     * Add child
     *
     * @param \UtilBundle\Entity\XeroSyncData $child
     *
     * @return XeroSyncData
     */
    public function addChild(\UtilBundle\Entity\XeroSyncData $child)
    {
        $child->setParent($this);
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \UtilBundle\Entity\XeroSyncData $child
     */
    public function removeChild(\UtilBundle\Entity\XeroSyncData $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Add payment
     *
     * @param \UtilBundle\Entity\XeroPayemnt $payment
     *
     * @return XeroSyncData
     */
    public function addPayment(\UtilBundle\Entity\XeroPayment $payment)
    {
        $payment->setXeroSyncData($this);
        $this->payments[] = $payment;

        return $this;
    }

    /**
     * Remove payment
     *
     * @param \UtilBundle\Entity\XeroPayment $payment
     */
    public function removePayment(\UtilBundle\Entity\XeroPayment $payment)
    {
        $this->payments->removeElement($payment);
    }

    /**
     * Get payments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPayments()
    {
        return $this->payments;
    }

    /**
     * Set xeroTotal
     *
     * @param string $xeroTotal
     *
     * @return XeroSyncData
     */
    public function setXeroTotal($xeroTotal)
    {
        $this->xeroTotal = $xeroTotal;

        return $this;
    }

    /**
     * Get xeroTotal
     *
     * @return string
     */
    public function getXeroTotal()
    {
        return $this->xeroTotal;
    }

    /**
     * Set xeroMapping
     *
     * @param \UtilBundle\Entity\XeroMapping $xeroMapping
     *
     * @return XeroSyncData
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
     * Set isLocal
     *
     * @param boolean $isLocal
     *
     * @return XeroSyncData
     */
    public function setIsLocal($isLocal)
    {
        $this->isLocal = $isLocal;

        return $this;
    }

    /**
     * Get isLocal
     *
     * @return boolean
     */
    public function getIsLocal()
    {
        return $this->isLocal;
    }
}
