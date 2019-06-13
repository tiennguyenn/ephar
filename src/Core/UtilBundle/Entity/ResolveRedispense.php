<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * ResolveRedispense
 *
 * @ORM\Table(name="resolve_redispense", indexes={@ORM\Index(name="FK_resolve_redispense", columns={"resolve_id"}), @ORM\Index(name="FK_resolve_redispense_url", columns={"document_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\ResolveRedispenseRepository")
 * @ORM\HasLifecycleCallbacks
 */
class ResolveRedispense
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
     * @ORM\Column(name="reason", type="text", length=65535, nullable=true)
     */
    private $reason;

    /**
     * @var string
     *
     * @ORM\Column(name="acceptance_by", type="string", length=250, nullable=true)
     */
    private $acceptanceBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="review_date", type="date", nullable=true)
     */
    private $reviewDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="review_time", type="string",  length=250,nullable=true)
     */
    private $reviewTime;

    /**
     * @var boolean
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
     * @var \DateTime
     *
     * @ORM\Column(name="deleted_on", type="datetime", nullable=true)
     */
    private $deletedOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="completed_on", type="datetime", nullable=true)
     */
    private $completedOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="approved_on", type="datetime", nullable=true)
     */
    private $approvedOn;

    /**
     * @var \Document
     *
     * @ORM\ManyToOne(targetEntity="Document", cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="document_id", referencedColumnName="id")
     * })
     */
    private $document;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_locked", type="boolean", nullable=true)
     */
    private $isLocked;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="parent_number", type="string",  length=20,nullable=true)
     */
    private $parentNumber;


    /**
     * @var \Document
     *
     * @ORM\ManyToOne(targetEntity="Document", cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="child_document_id", referencedColumnName="id")
     * })
     */
    private $childDocument;

    /**
     * @var \Resolve
     *
     * @ORM\ManyToOne(targetEntity="Resolve")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="resolve_id", referencedColumnName="id")
     * })
     */
    private $resolve;



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
     * Set reason
     *
     * @param string $reason
     *
     * @return ResolveRedispense
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
     * Set acceptanceBy
     *
     * @param string $acceptanceBy
     *
     * @return ResolveRedispense
     */
    public function setAcceptanceBy($acceptanceBy)
    {
        $this->acceptanceBy = $acceptanceBy;

        return $this;
    }

    /**
     * Get acceptanceBy
     *
     * @return string
     */
    public function getAcceptanceBy()
    {
        return $this->acceptanceBy;
    }

    /**
     * Set reviewDate
     *
     * @param \DateTime $reviewDate
     *
     * @return ResolveRedispense
     */
    public function setReviewDate($reviewDate)
    {
        $this->reviewDate = $reviewDate;

        return $this;
    }

    /**
     * Get reviewDate
     *
     * @return \DateTime
     */
    public function getReviewDate()
    {
        return $this->reviewDate;
    }

    /**
     * Set reviewTime
     *
     * @param \DateTime $reviewTime
     *
     * @return ResolveRedispense
     */
    public function setReviewTime($reviewTime)
    {
        $this->reviewTime = $reviewTime;

        return $this;
    }

    /**
     * Get reviewTime
     *
     * @return \DateTime
     */
    public function getReviewTime()
    {
        return $this->reviewTime;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return ResolveRedispense
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
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return ResolveRedispense
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
     * Set document
     *
     * @param \UtilBundle\Entity\Document $document
     *
     * @return ResolveRedispense
     */
    public function setDocument(\UtilBundle\Entity\Document $document = null)
    {
        $this->document = $document;

        return $this;
    }

    /**
     * Get document
     *
     * @return \UtilBundle\Entity\Document
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Set resolve
     *
     * @param \UtilBundle\Entity\Resolve $resolve
     *
     * @return ResolveRedispense
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
     * Set completedOn
     *
     * @param \DateTime $completedOn
     *
     * @return ResolveRedispense
     */
    public function setCompletedOn($completedOn)
    {
        $this->completedOn = $completedOn;

        return $this;
    }

    /**
     * Get completedOn
     *
     * @return \DateTime
     */
    public function getCompletedOn()
    {
        return $this->completedOn;
    }

    /**
     * Set approvedOn
     *
     * @param \DateTime $approvedOn
     *
     * @return ResolveRedispense
     */
    public function setApprovedOn($approvedOn)
    {
        $this->approvedOn = $approvedOn;

        return $this;
    }

    /**
     * Get approvedOn
     *
     * @return \DateTime
     */
    public function getApprovedOn()
    {
        return $this->approvedOn;
    }

    /**
     * Set childDocument
     *
     * @param \UtilBundle\Entity\Document $childDocument
     *
     * @return ResolveRedispense
     */
    public function setChildDocument(\UtilBundle\Entity\Document $childDocument = null)
    {
        $this->childDocument = $childDocument;

        return $this;
    }

    /**
     * Get childDocument
     *
     * @return \UtilBundle\Entity\Document
     */
    public function getChildDocument()
    {
        return $this->childDocument;
    }

    /**
     * Set isLocked
     *
     * @param boolean $isLocked
     *
     * @return ResolveRedispense
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
     * Set parentNumber
     *
     * @param string $parentNumber
     *
     * @return ResolveRedispense
     */
    public function setParentNumber($parentNumber)
    {
        $this->parentNumber = $parentNumber;

        return $this;
    }

    /**
     * Get parentNumber
     *
     * @return string
     */
    public function getParentNumber()
    {
        return $this->parentNumber;
    }

    /**
     * Set deletedOn
     *
     * @param \DateTime $deletedOn
     *
     * @return ResolveRedispense
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
