<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * XeroJournal
 *
 * @ORM\Table(name="xero_journal", indexes={@ORM\Index(name="FK_xero_journal", columns={"xero_sync_data_id"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class XeroJournal
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
     * @ORM\Column(name="xero_guid", type="string", length=38, nullable=true)
     */
    private $xeroGuid;

    /**
     * @var string
     *
     * @ORM\Column(name="reference", type="string", length=255, nullable=false)
     */
    private $reference;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=false)
     */
    private $createdOn;

    /**
     * @var string
     *
     * @ORM\Column(name="created_by", type="string", length=50, nullable=false)
     */
    private $createdBy;

    /**
     * @var \XeroSyncData
     *
     * @ORM\ManyToOne(targetEntity="XeroSyncData", cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="xero_sync_data_id", referencedColumnName="id")
     * })
     */
    private $xeroSyncData;

    /**
     * @ORM\OneToMany(targetEntity="XeroJournalLine", mappedBy="xeroJournal", cascade={"persist", "remove" })
     */
    private $lines;

    /**
     * Constructor
     */
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set xeroGuid
     *
     * @param string $xeroGuid
     *
     * @return XeroJournal
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
     * Set reference
     *
     * @param string $reference
     *
     * @return XeroJournal
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get reference
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return XeroJournal
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
     * @return XeroJournal
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
     * Set xeroSyncData
     *
     * @param \UtilBundle\Entity\XeroSyncData $xeroSyncData
     *
     * @return XeroJournal
     */
    public function setXeroSyncData(\UtilBundle\Entity\XeroSyncData $xeroSyncData = null)
    {
        $this->xeroSyncData = $xeroSyncData;

        return $this;
    }

    /**
     * Get xeroSyncData
     *
     * @return \UtilBundle\Entity\XeroSyncData
     */
    public function getXeroSyncData()
    {
        return $this->xeroSyncData;
    }

    /**
     * Add line
     *
     * @param \UtilBundle\Entity\XeroJournalLine $line
     *
     * @return XeroJournal
     */
    public function addLine(\UtilBundle\Entity\XeroJournalLine $line)
    {
        $line->setXeroJournal($this);
        $this->lines[] = $line;

        return $this;
    }

    /**
     * Remove line
     *
     * @param \UtilBundle\Entity\XeroJournalLine $line
     */
    public function removeLine(\UtilBundle\Entity\XeroJournalLine $line)
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
}
