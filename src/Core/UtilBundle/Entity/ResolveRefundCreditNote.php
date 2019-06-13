<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ResolveRefundCreditNote
 *
 * @ORM\Table(name="resolve_refund_credit_note", indexes={@ORM\Index(name="FK_resolve_refund_credit_note", columns={"resolve_refund_id"}), @ORM\Index(name="FK_resolve_refund_credit_note_document", columns={"document_id"})})
 * @ORM\Entity
 */
class ResolveRefundCreditNote
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
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=false)
     */
    private $createdOn;

    /**
     * @var \Document
     *
     * @ORM\ManyToOne(targetEntity="Document")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="document_id", referencedColumnName="id")
     * })
     */
    private $document;

    /**
     * @var \ResolveRefund
     *
     * @ORM\ManyToOne(targetEntity="ResolveRefund")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="resolve_refund_id", referencedColumnName="id")
     * })
     */
    private $resolveRefund;



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
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return ResolveRefundCreditNote
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
     * @return ResolveRefundCreditNote
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
     * Set resolveRefund
     *
     * @param \UtilBundle\Entity\ResolveRefund $resolveRefund
     * @return ResolveRefundCreditNote
     */
    public function setResolveRefund(\UtilBundle\Entity\ResolveRefund $resolveRefund = null)
    {
        $this->resolveRefund = $resolveRefund;

        return $this;
    }

    /**
     * Get resolveRefund
     *
     * @return \UtilBundle\Entity\ResolveRefund 
     */
    public function getResolveRefund()
    {
        return $this->resolveRefund;
    }
}
