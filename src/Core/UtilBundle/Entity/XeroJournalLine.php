<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * XeroJournalLine
 *
 * @ORM\Table(name="xero_journal_line", indexes={@ORM\Index(name="xero_journal_id", columns={"xero_journal_id"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class XeroJournalLine
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
     * @ORM\Column(name="description", type="string", length=240, nullable=false)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="debit_amount", type="decimal", precision=13, scale=2, nullable=false)
     */
    private $debitAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="credit_amount", type="decimal", precision=13, scale=2, nullable=false)
     */
    private $creditAmount;

    /**
     * @var \XeroJournal
     *
     * @ORM\ManyToOne(targetEntity="XeroJournal", cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="xero_journal_id", referencedColumnName="id")
     * })
     */
    private $xeroJournal;

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
     * @ORM\OneToMany(targetEntity="XeroJournalLineSource", mappedBy="xeroJournalLine", cascade={"persist", "remove" })
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
     * @return XeroJournalLine
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
     * @return XeroJournalLine
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
     * @return XeroJournalLine
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
     * @return XeroJournalLine
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
     * Set xeroJournal
     *
     * @param \UtilBundle\Entity\XeroJournal $xeroJournal
     *
     * @return XeroJournalLine
     */
    public function setXeroJournal(\UtilBundle\Entity\XeroJournal $xeroJournal = null)
    {
        $this->xeroJournal = $xeroJournal;

        return $this;
    }

    /**
     * Get xeroJournal
     *
     * @return \UtilBundle\Entity\XeroJournal
     */
    public function getXeroJournal()
    {
        return $this->xeroJournal;
    }

    /**
     * Add source
     *
     * @param \UtilBundle\Entity\XeroJournalLineSource $source
     *
     * @return XeroJournalLine
     */
    public function addSource(\UtilBundle\Entity\XeroJournalLineSource $source)
    {
        $source->setXeroJournalLine($this);
        $this->sources[] = $source;

        return $this;
    }

    /**
     * Remove source
     *
     * @param \UtilBundle\Entity\XeroJournalLineSource $source
     */
    public function removeSource(\UtilBundle\Entity\XeroJournalLineSource $source)
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
     * Set xeroMapping
     *
     * @param \UtilBundle\Entity\XeroMapping $xeroMapping
     *
     * @return XeroJournalLine
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
}
