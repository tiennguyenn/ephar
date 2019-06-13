<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ResolveInvoicePartyLine
 *
 * @ORM\Table(name="resolve_invoice_party_line", uniqueConstraints={@ORM\UniqueConstraint(name="NewIndex1", columns={"document_id"})}, indexes={@ORM\Index(name="FK_resolve_invoice_party_line", columns={"resolve_invoice_party_id"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class ResolveInvoicePartyLine
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
     * @ORM\Column(name="party_invoice_to", type="string", length=20, nullable=true)
     */
    private $partyInvoiceTo;

    /**
     * @var string
     *
     * @ORM\Column(name="invoice_no", type="string", length=20, nullable=true)
     */
    private $invoiceNo;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $amount;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="sent_on", type="datetime", nullable=true)
     */
    private $sentOn;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted_on", type="datetime", nullable=true)
     */
    private $deletedOn;
    
    /**
     * @var \ResolveInvoiceParty
     *
     * @ORM\ManyToOne(targetEntity="ResolveInvoiceParty")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="resolve_invoice_party_id", referencedColumnName="id")
     * })
     */
    private $resolveInvoiceParty;

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
     * Set partyInvoiceTo
     *
     * @param string $partyInvoiceTo
     *
     * @return ResolveInvoicePartyLine
     */
    public function setPartyInvoiceTo($partyInvoiceTo)
    {
        $this->partyInvoiceTo = $partyInvoiceTo;

        return $this;
    }

    /**
     * Get partyInvoiceTo
     *
     * @return string
     */
    public function getPartyInvoiceTo()
    {
        return $this->partyInvoiceTo;
    }

    /**
     * Set invoiceNo
     *
     * @param string $invoiceNo
     *
     * @return ResolveInvoicePartyLine
     */
    public function setInvoiceNo($invoiceNo)
    {
        $this->invoiceNo = $invoiceNo;

        return $this;
    }

    /**
     * Get invoiceNo
     *
     * @return string
     */
    public function getInvoiceNo()
    {
        return $this->invoiceNo;
    }

    /**
     * Set amount
     *
     * @param string $amount
     *
     * @return ResolveInvoicePartyLine
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

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return ResolveInvoicePartyLine
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
     * Set resolveInvoiceParty
     *
     * @param \UtilBundle\Entity\ResolveInvoiceParty $resolveInvoiceParty
     *
     * @return ResolveInvoicePartyLine
     */
    public function setResolveInvoiceParty(\UtilBundle\Entity\ResolveInvoiceParty $resolveInvoiceParty = null)
    {
        $this->resolveInvoiceParty = $resolveInvoiceParty;

        return $this;
    }

    /**
     * Get resolveInvoiceParty
     *
     * @return \UtilBundle\Entity\ResolveInvoiceParty
     */
    public function getResolveInvoiceParty()
    {
        return $this->resolveInvoiceParty;
    }

    /**
     * Set document
     *
     * @param \UtilBundle\Entity\Document $document
     *
     * @return ResolveInvoicePartyLine
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
     * Set deletedOn
     *
     * @param \DateTime $deletedOn
     *
     * @return ResolveInvoicePartyLine
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
     * Set sentOn
     *
     * @param \DateTime $sentOn
     *
     * @return ResolveInvoicePartyLine
     */
    public function setSentOn($sentOn)
    {
        $this->sentOn = $sentOn;

        return $this;
    }

    /**
     * Get sentOn
     *
     * @return \DateTime
     */
    public function getSentOn()
    {
        return $this->sentOn;
    }
}
