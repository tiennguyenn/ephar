<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * ResolveRefund
 *
 * @ORM\Table(name="resolve_refund", indexes={@ORM\Index(name="FK_resolve_refund", columns={"resolve_id"}), @ORM\Index(name="FK_resolve_refund_payment_log", columns={"rx_payment_log_id"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class ResolveRefund
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
     * @var boolean
     *
     * @ORM\Column(name="refund_type", type="boolean", nullable=true)
     */
    private $refundType;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_locked", type="boolean", nullable=true)
     */
    private $isLocked;

    /**
     * @var string
     *
     * @ORM\Column(name="refund_value", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $refundValue;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted_on", type="datetime", nullable=true)
     */
    private $deletedOn;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private $status;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="approve_status", type="integer", nullable=true)
     */
    private $approveStatus = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="refunded_date", type="datetime", nullable=true)
     */
    private $refundDate;

    /**
     * @var string
     *
     * @ORM\Column(name="created_by", type="string", length=250, nullable=true)
     */
    private $createdBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var \RxPaymentLog
     *
     * @ORM\ManyToOne(targetEntity="RxPaymentLog",inversedBy="resolveRefunds",cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="rx_payment_log_id", referencedColumnName="id")
     * })
     */
    private $rxPaymentLog;

    /**
     * @var \Resolve
     *
     * @ORM\ManyToOne(targetEntity="Resolve",inversedBy="resolveRefunds", cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="resolve_id", referencedColumnName="id")
     * })
     */
    private $resolve;


    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Document", inversedBy="refunds", cascade={"persist", "remove" })
     * @ORM\JoinTable(name="resolve_refund_credit_note")
     *
     */
    private $creditNotes;

      /**
     * Constructor
     */
    public function __construct()
    {
        $this->creditNotes = new ArrayCollection();
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
     * Set refundType
     *
     * @param boolean $refundType
     *
     * @return ResolveRefund
     */
    public function setRefundType($refundType)
    {
        $this->refundType = $refundType;

        return $this;
    }

    /**
     * Get refundType
     *
     * @return boolean
     */
    public function getRefundType()
    {
        return $this->refundType;
    }

    /**
     * Set refundValue
     *
     * @param string $refundValue
     *
     * @return ResolveRefund
     */
    public function setRefundValue($refundValue)
    {
        $this->refundValue = $refundValue;

        return $this;
    }

    /**
     * Get refundValue
     *
     * @return string
     */
    public function getRefundValue()
    {
        return $this->refundValue;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return ResolveRefund
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
     * Set createdBy
     *
     * @param string $createdBy
     *
     * @return ResolveRefund
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
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return ResolveRefund
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
     * Set rxPaymentLog
     *
     * @param \UtilBundle\Entity\RxPaymentLog $rxPaymentLog
     *
     * @return ResolveRefund
     */
    public function setRxPaymentLog(\UtilBundle\Entity\RxPaymentLog $rxPaymentLog = null)
    {
        $this->rxPaymentLog = $rxPaymentLog;

        return $this;
    }

    /**
     * Get rxPaymentLog
     *
     * @return \UtilBundle\Entity\RxPaymentLog
     */
    public function getRxPaymentLog()
    {
        return $this->rxPaymentLog;
    }

    /**
     * Set resolve
     *
     * @param \UtilBundle\Entity\Resolve $resolve
     *
     * @return ResolveRefund
     */
    public function setResolve(\UtilBundle\Entity\Resolve $resolve = null)
    {
        $this->resolve = $resolve;

        return $this;
    }

    /**
     * Get resolve
     *
     * @return \UtilBundle\Entity\Resolve
     */
    public function getResolve()
    {
        return $this->resolve;
    }


    /**
     * Set refundDate
     *
     * @param \DateTime $refundDate
     *
     * @return ResolveRefund
     */
    public function setRefundDate($refundDate)
    {
        $this->refundDate = $refundDate;

        return $this;
    }

    /**
     * Get refundDate
     *
     * @return \DateTime
     */
    public function getRefundDate()
    {
        return $this->refundDate;
    }

    /**
     * Add creditNote
     *
     * @param \UtilBundle\Entity\Document $creditNote
     *
     * @return ResolveRefund
     */
    public function addCreditNote(\UtilBundle\Entity\Document $creditNote)
    {
        $this->creditNotes[] = $creditNote;

        return $this;
    }

    /**
     * Remove creditNote
     *
     * @param \UtilBundle\Entity\Document $creditNote
     */
    public function removeCreditNote(\UtilBundle\Entity\Document $creditNote)
    {
        $this->creditNotes->removeElement($creditNote);
    }

    /**
     * Get creditNotes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCreditNotes()
    {
        return $this->creditNotes;
    }

    /**
     * Set approveStatus
     *
     * @param integer $approveStatus
     *
     * @return ResolveRefund
     */
    public function setApproveStatus($approveStatus)
    {
        $this->approveStatus = $approveStatus;

        return $this;
    }

    /**
     * Get approveStatus
     *
     * @return integer
     */
    public function getApproveStatus()
    {
        return $this->approveStatus;
    }

    /**
     * Set isLocked
     *
     * @param boolean $isLocked
     *
     * @return ResolveRefund
     */
    public function setIsLocked($isLocked)
    {
        $this->isLocked = $isLocked;

        return $this;
    }

    /**
     * Get isLocked
     *
     * @return boolean
     */
    public function getIsLocked()
    {
        return $this->isLocked;
    }

    /**
     * Set deletedOn
     *
     * @param \DateTime $deletedOn
     *
     * @return ResolveRefund
     */
    public function setDeletedOn($deletedOn)
    {
        $this->deletedOn = $deletedOn;

        return $this;
    }

    /**
     * Get deletedOn
     *
     * @return \DateTime
     */
    public function getDeletedOn()
    {
        return $this->deletedOn;
    }
}
