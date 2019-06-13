<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Resolve
 *
 * @ORM\Table(name="resolve", indexes={@ORM\Index(name="FK_resolve_rx", columns={"rx_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\ResolveRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Resolve
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
     * @ORM\Column(name="reason", type="string", length=250, nullable=true)
     */
    private $reason;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_locked", type="boolean", nullable=true)
     */
    private $isLocked;

    /**
     * @var string
     *
     * @ORM\Column(name="created_by", type="string", length=250, nullable=true)
     */
    private $createdBy;

    /**
     * @var datetime
     *
     * @ORM\Column(name="deleted_on", type="datetime",  nullable=true)
     */
    private $deletedOn;
    /**
     * @var \Rx
     *
     * @ORM\ManyToOne(targetEntity="Rx",inversedBy="resolves", cascade={"persist", "remove"} )
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="rx_id", referencedColumnName="id")
     * })
     */
    private $rx;
    /**
     * @ORM\OneToMany(targetEntity="ResolveRefund", mappedBy="resolve", cascade={"persist", "remove" })
     */
    private $resolveRefunds;

    /**
     * @ORM\OneToMany(targetEntity="ResolveParcel", mappedBy="resolve", cascade={"persist", "remove" })
     */
    private $resolveParcels;



    /**
     * @ORM\OneToMany(targetEntity="ResolveRedispense", mappedBy="resolve", cascade={"persist", "remove" })
     */
    private $resolveRedispenses;

    /**
     * @ORM\OneToMany(targetEntity="ResolveTrackingCompleted", mappedBy="resolve", cascade={"persist", "remove" })
     */
    private $resolveTrackings;

    /**
     * @ORM\OneToMany(targetEntity="ResolveAttachment", mappedBy="resolve", cascade={"persist", "remove" })
     */
    private $resolveAttachments;

    /**
     * @ORM\OneToMany(targetEntity="ResolveBankInfo", mappedBy="resolve", cascade={"persist", "remove" })
     */
    private $resolveBankInfos;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="ResolveIncidentReport", mappedBy="resolve", cascade={"persist", "remove" })
     *
     */
    private $incidents;
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="ResolveInvoiceParty", mappedBy="resolve", cascade={"persist", "remove" })
     *
     */
    private $invoiceParties;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="ResolveDoctorReissue", mappedBy="resolve", cascade={"persist", "remove" })
     *
     */
    private $doctorReissues;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Issue", inversedBy="resolves", cascade={"persist", "remove" })
     * @ORM\JoinTable(name="resolve_issue")
     */
    private $issues;
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Log", inversedBy="resolves", cascade={"persist", "remove" })
     * @ORM\JoinTable(name="resolve_log")
     */
    private $logs;


    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="ResolveChangeAddress", mappedBy="resolve", cascade={"persist", "remove" })
     *
     */
    private $addresses;

    /**
     * @var \Gap
     *
     * @ORM\ManyToOne(targetEntity="Gap")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="gap_id", referencedColumnName="id")
     * })
     */
    private $gap;


    public function __construct() {
        $this->resolveAttachments = new ArrayCollection();
        $this->resolveRefunds = new ArrayCollection();
        $this->resolveRedispenses = new ArrayCollection();
        $this->resolveParcels = new ArrayCollection();
        $this->incidents = new ArrayCollection();
        $this->issues = new ArrayCollection();
        $this->logs = new ArrayCollection();
        $this->addresses = new ArrayCollection();
        $this->resolveTrackings = new ArrayCollection();
        $this->resolveBankInfos = new ArrayCollection();
        $this->invoiceParties = new ArrayCollection();
        $this->doctorReissues = new ArrayCollection();

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
     * Set reason
     *
     * @param string $reason
     * @return Resolve
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * Get reason
     *
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return Resolve
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
     * @return Resolve
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
     * Set rx
     *
     * @param \UtilBundle\Entity\Rx $rx
     * @return Resolve
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
     * Gets triggered only on insert

     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdOn = new \DateTime("now");
    }

    /**
     * Add resolveAttachment
     *
     * @param \UtilBundle\Entity\ResolveAttachment $resolveAttachment
     *
     * @return Resolve
     */
    public function addResolveAttachment(\UtilBundle\Entity\ResolveAttachment $resolveAttachment)
    {
        $resolveAttachment->setResolve($this);
        $this->resolveAttachments[] = $resolveAttachment;

        return $this;
    }

    /**
     * Remove resolveAttachment
     *
     * @param \UtilBundle\Entity\ResolveAttachment $resolveAttachment
     */
    public function removeResolveAttachment(\UtilBundle\Entity\ResolveAttachment $resolveAttachment)
    {
        $this->resolveAttachments->removeElement($resolveAttachment);
    }

    /**
     * Get resolveAttachments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getResolveAttachments()
    {
        $results = $this->resolveAttachments;
        $listValid = [];
        foreach ($results as $obj){
            if(empty($obj->getDeletedOn())){
                array_push($listValid, $obj);
            }
        }
        return $listValid;
    }


    /**
     * Add resolveRefund
     *
     * @param \UtilBundle\Entity\ResolveRefund $resolveRefund
     *
     * @return Resolve
     */
    public function addResolveRefund(\UtilBundle\Entity\ResolveRefund $resolveRefund)
    {
        $resolveRefund->setResolve($this);
        $this->resolveRefunds[] = $resolveRefund;

        return $this;
    }

    /**
     * Remove resolveRefund
     *
     * @param \UtilBundle\Entity\ResolveRefund $resolveRefund
     */
    public function removeResolveRefund(\UtilBundle\Entity\ResolveRefund $resolveRefund)
    {
        $this->resolveRefunds->removeElement($resolveRefund);
    }

    /**
     * Get resolveRefunds
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getResolveRefunds()
    {
        return $this->resolveRefunds;
    }

    /**
     * Add resolveRedispense
     *
     * @param \UtilBundle\Entity\ResolveRedispense $resolveRedispense
     *
     * @return Resolve
     */
    public function addResolveRedispense(\UtilBundle\Entity\ResolveRedispense $resolveRedispense)
    {
        $resolveRedispense->setResolve($this);
        $this->resolveRedispenses[] = $resolveRedispense;

        return $this;
    }

    /**
     * Remove resolveRedispense
     *
     * @param \UtilBundle\Entity\ResolveRedispense $resolveRedispense
     */
    public function removeResolveRedispense(\UtilBundle\Entity\ResolveRedispense $resolveRedispense)
    {
        $this->resolveRedispenses->removeElement($resolveRedispense);
    }

    /**
     * Get resolveRedispenses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getResolveRedispenses()
    {
        return $this->resolveRedispenses;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Resolve
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
     * Add issue
     *
     * @param \UtilBundle\Entity\Issue $issue
     *
     * @return Resolve
     */
    public function addIssue(\UtilBundle\Entity\Issue $issue)
    {

        $this->issues[] = $issue;

        return $this;
    }

    /**
     * Remove issue
     *
     * @param \UtilBundle\Entity\Issue $issue
     */
    public function removeIssue(\UtilBundle\Entity\Issue $issue)
    {
        $this->issues->removeElement($issue);
    }

    /**
     * Get issues
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIssues()
    {
        return $this->issues;
    }

    /**
     * Add log
     *
     * @param \UtilBundle\Entity\Log $log
     *
     * @return Resolve
     */
    public function addLog(\UtilBundle\Entity\Log $log)
    {
        $this->logs[] = $log;

        return $this;
    }

    /**
     * Remove log
     *
     * @param \UtilBundle\Entity\Log $log
     */
    public function removeLog(\UtilBundle\Entity\Log $log)
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
     * Set deletedOn
     *
     * @param \DateTime $deletedOn
     *
     * @return Resolve
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

    /**
     * Add resolveParcel
     *
     * @param \UtilBundle\Entity\ResolveParcel $resolveParcel
     *
     * @return Resolve
     */
    public function addResolveParcel(\UtilBundle\Entity\ResolveParcel $resolveParcel)
    {
        $resolveParcel->setResolve($this);
        $this->resolveParcels[] = $resolveParcel;

        return $this;
    }

    /**
     * Remove resolveParcel
     *
     * @param \UtilBundle\Entity\ResolveParcel $resolveParcel
     */
    public function removeResolveParcel(\UtilBundle\Entity\ResolveParcel $resolveParcel)
    {
        $this->resolveParcels->removeElement($resolveParcel);
    }

    /**
     * Get resolveParcels
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getResolveParcels()
    {
        return $this->resolveParcels;
    }

   
    /**
     * Add resolveTracking
     *
     * @param \UtilBundle\Entity\ResolveTrackingCompleted $resolveTracking
     *
     * @return Resolve
     */
    public function addResolveTracking(\UtilBundle\Entity\ResolveTrackingCompleted $resolveTracking)
    {
        $resolveTracking->setResolve($this);
        $this->resolveTrackings[] = $resolveTracking;

        return $this;
    }

    /**
     * Remove resolveTracking
     *
     * @param \UtilBundle\Entity\ResolveTrackingCompleted $resolveTracking
     */
    public function removeResolveTracking(\UtilBundle\Entity\ResolveTrackingCompleted $resolveTracking)
    {
        $this->resolveTrackings->removeElement($resolveTracking);
    }

    /**
     * Get resolveTrackings
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getResolveTrackings()
    {
        return $this->resolveTrackings;
    }

    /**
     * Add resolveBankInfo
     *
     * @param \UtilBundle\Entity\ResolveBankInfo $resolveBankInfo
     *
     * @return Resolve
     */
    public function addResolveBankInfo(\UtilBundle\Entity\ResolveBankInfo $resolveBankInfo)
    {
        $resolveBankInfo->setResolve($this);
        $this->resolveBankInfos[] = $resolveBankInfo;

        return $this;
    }

    /**
     * Remove resolveBankInfo
     *
     * @param \UtilBundle\Entity\ResolveBankInfo $resolveBankInfo
     */
    public function removeResolveBankInfo(\UtilBundle\Entity\ResolveBankInfo $resolveBankInfo)
    {
        $this->resolveBankInfos->removeElement($resolveBankInfo);
    }

    /**
     * Get resolveBankInfos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getResolveBankInfos()
    {
        return $this->resolveBankInfos;
    }

    /**
     * Add incident
     *
     * @param \UtilBundle\Entity\ResolveIncidentReport $incident
     *
     * @return Resolve
     */
    public function addIncident(\UtilBundle\Entity\ResolveIncidentReport $incident)
    {
        $incident->setResolve($this);
        $this->incidents[] = $incident;

        return $this;
    }

    /**
     * Remove incident
     *
     * @param \UtilBundle\Entity\ResolveIncidentReport $incident
     */
    public function removeIncident(\UtilBundle\Entity\ResolveIncidentReport $incident)
    {
        $this->incidents->removeElement($incident);
    }

    /**
     * Get incidents
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIncidents()
    {
        return $this->incidents;
    }

    /**
     * Set gap
     *
     * @param \UtilBundle\Entity\Gap $gap
     *
     * @return Resolve
     */
    public function setGap(\UtilBundle\Entity\Gap $gap = null)
    {
        $this->gap = $gap;

        return $this;
    }

    /**
     * Get gap
     *
     * @return \UtilBundle\Entity\Gap
     */
    public function getGap()
    {
        return $this->gap;
    }

    /**
     * Add invoiceParty
     *
     * @param \UtilBundle\Entity\ResolveInvoiceParty $invoiceParty
     *
     * @return Resolve
     */
    public function addInvoiceParty(\UtilBundle\Entity\ResolveInvoiceParty $invoiceParty)
    {
        $invoiceParty->setResolve($this);
        $this->invoiceParties[] = $invoiceParty;

        return $this;
    }

    /**
     * Remove invoiceParty
     *
     * @param \UtilBundle\Entity\ResolveInvoiceParty $invoiceParty
     */
    public function removeInvoiceParty(\UtilBundle\Entity\ResolveInvoiceParty $invoiceParty)
    {
        $this->invoiceParties->removeElement($invoiceParty);
    }

    /**
     * Get invoiceParties
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInvoiceParties()
    {
        return $this->invoiceParties;
    }

    /**
     * Add doctorReissue
     *
     * @param \UtilBundle\Entity\ResolveDoctorReissue $doctorReissue
     *
     * @return Resolve
     */
    public function addDoctorReissue(\UtilBundle\Entity\ResolveDoctorReissue $doctorReissue)
    {
        $doctorReissue->setResolve($this);
        $this->doctorReissues[] = $doctorReissue;

        return $this;
    }

    /**
     * Remove doctorReissue
     *
     * @param \UtilBundle\Entity\ResolveDoctorReissue $doctorReissue
     */
    public function removeDoctorReissue(\UtilBundle\Entity\ResolveDoctorReissue $doctorReissue)
    {
        $this->doctorReissues->removeElement($doctorReissue);
    }

    /**
     * Get doctorReissues
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDoctorReissues()
    {
        return $this->doctorReissues;
    }

    /**
     * Add address
     *
     * @param \UtilBundle\Entity\ResolveChangeAddress $address
     *
     * @return Resolve
     */
    public function addAddress(\UtilBundle\Entity\ResolveChangeAddress $address)
    {
        $address->setResolve($this);
        $this->addresses[] = $address;

        return $this;
    }

    /**
     * Remove address
     *
     * @param \UtilBundle\Entity\ResolveChangeAddress $address
     */
    public function removeAddress(\UtilBundle\Entity\ResolveChangeAddress $address)
    {
        $this->addresses->removeElement($address);
    }

    /**
     * Get addresses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAddresses()
    {
        return $this->addresses;
    }

    /**
     * Set isLocked
     *
     * @param boolean $isLocked
     *
     * @return Resolve
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
}
