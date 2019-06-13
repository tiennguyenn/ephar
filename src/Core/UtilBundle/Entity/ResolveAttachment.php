<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ResolveAttachment
 *
 * @ORM\Table(name="resolve_attachment", indexes={@ORM\Index(name="FK_resolve_report_attachment", columns={"resolve_id"})})
 * @ORM\Entity
 */
class ResolveAttachment
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
     * @ORM\Column(name="document_url", type="string", length=250, nullable=false)
     */
    private $documentUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="document_name", type="string", length=250, nullable=true)
     */
    private $documentName;

    /**
     * @var float
     *
     * @ORM\Column(name="document_size", type="float", precision=10, scale=0, nullable=true)
     */
    private $documentSize;

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
     * @var \DateTime
     *
     * @ORM\Column(name="deleted_on", type="datetime", nullable=true)
     */
    private $deletedOn;

    /**
     * @var \Resolve
     *
     * @ORM\ManyToOne(targetEntity="Resolve",inversedBy="resolveAttachments", cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="resolve_id", referencedColumnName="id")
     * })
     */
    private $resolve;



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
     * Set documentUrl
     *
     * @param string $documentUrl
     *
     * @return ResolveAttachment
     */
    public function setDocumentUrl($documentUrl)
    {
        $this->documentUrl = $documentUrl;

        return $this;
    }

    /**
     * Get documentUrl
     *
     * @return string
     */
    public function getDocumentUrl()
    {
        return $this->documentUrl;
    }

    /**
     * Set documentName
     *
     * @param string $documentName
     *
     * @return ResolveAttachment
     */
    public function setDocumentName($documentName)
    {
        $this->documentName = $documentName;

        return $this;
    }

    /**
     * Get documentName
     *
     * @return string
     */
    public function getDocumentName()
    {
        return $this->documentName;
    }

    /**
     * Set createdBy
     *
     * @param string $createdBy
     *
     * @return ResolveAttachment
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
     * @return ResolveAttachment
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
     * Set deletedOn
     *
     * @param \DateTime $deletedOn
     *
     * @return ResolveAttachment
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
     * Set resolve
     *
     * @param \UtilBundle\Entity\Resolve $resolve
     *
     * @return ResolveAttachment
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
     * Set documentSize
     *
     * @param float $documentSize
     *
     * @return ResolveAttachment
     */
    public function setDocumentSize($documentSize)
    {
        $this->documentSize = $documentSize;

        return $this;
    }

    /**
     * Get documentSize
     *
     * @return float
     */
    public function getDocumentSize()
    {
        return $this->documentSize;
    }
}
