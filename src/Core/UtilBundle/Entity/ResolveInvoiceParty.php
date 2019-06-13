<?php

namespace UtilBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * ResolveInvoiceParty
 *
 * @ORM\Table(name="resolve_invoice_party", indexes={@ORM\Index(name="FK_resolve_invoice_party", columns={"resolve_id"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class ResolveInvoiceParty
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
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private $status = 1;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_locked", type="boolean", nullable=true)
     */
    private $isLocked;

    /**
     * @var boolean
     *
     * @ORM\Column(name="approve_status", type="integer", nullable=true)
     */
    private $approveStatus = 0;

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
     * @ORM\Column(name="updated_on", type="datetime", nullable=true)
     */
    private $updatedOn;

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
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="ResolveInvoicePartyLine", mappedBy="resolveInvoiceParty", cascade={"persist", "remove" })
     *
     */
    private $lines;

    public function __construct()
    {
        $this->lines = new ArrayCollection();
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
     * Gets triggered every time on update

     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->updatedOn = new \DateTime("now");
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
     * Set status
     *
     * @param boolean $status
     *
     * @return ResolveInvoiceParty
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
     * @return ResolveInvoiceParty
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
     *
     * @return ResolveInvoiceParty
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
     * Set resolve
     *
     * @param \UtilBundle\Entity\Resolve $resolve
     *
     * @return ResolveInvoiceParty
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
     * Add line
     *
     * @param \UtilBundle\Entity\resolveInvoiceParty $line
     *
     * @return ResolveInvoiceParty
     */
    public function addLine(\UtilBundle\Entity\ResolveInvoicePartyLine $line)
    {
        $line->setResolveInvoiceParty($this);
        $this->lines[] = $line;

        return $this;
    }

    /**
     * Remove line
     *
     * @param \UtilBundle\Entity\resolveInvoiceParty $line
     */
    public function removeLine(\UtilBundle\Entity\ResolveInvoicePartyLine $line)
    {
        $this->lines->removeElement($line);
    }

    /**
     * Get lines
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLines()
    {
        return $this->lines;
    }

    /**
     * Set approveStatus
     *
     * @param integer $approveStatus
     *
     * @return ResolveInvoiceParty
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
     * @return ResolveInvoiceParty
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
     * @return ResolveInvoiceParty
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
