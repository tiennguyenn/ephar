<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IssuesAttachment
 *
 * @ORM\Table(name="issues_attachment", indexes={@ORM\Index(name="FK_issues_attachment", columns={"issues_id"})})
 * @ORM\Entity
 */
class IssuesAttachment
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
     * @ORM\Column(name="document_url", type="string", length=250, nullable=true)
     */
    private $documentUrl;
    
     /**
     * @var string
     *
     * @ORM\Column(name="document_name", type="string", length=250, nullable=true)
     */
    private $documentName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var string
     *
     * @ORM\Column(name="created_by", type="string", length=250,  nullable=true)
     */
    private $createdBy;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted_on", type="datetime", nullable=true)
     */
    private $deletedOn;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="sent_on", type="datetime", nullable=true)
     */
    private $sentOn;

    /**
     * @var \Issue
     *
     * @ORM\ManyToOne(targetEntity="Issue" ,inversedBy="issueAttachments", cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="issues_id", referencedColumnName="id")
     * })
     */
    private $issues;
    

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
     * @return IssuesAttachment
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
     * @return IssuesAttachment
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
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return IssuesAttachment
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
     * @return IssuesAttachment
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
     * Set issues
     *
     * @param \UtilBundle\Entity\Issue $issues
     *
     * @return IssuesAttachment
     */
    public function setIssues(\UtilBundle\Entity\Issue $issues = null)
    {
        $this->issues = $issues;

        return $this;
    }

    /**
     * Get issues
     *
     * @return \UtilBundle\Entity\Issue
     */
    public function getIssues()
    {
        return $this->issues;
    }

    /**
     * Set deletedOn
     *
     * @param \DateTime $deletedOn
     *
     * @return IssuesAttachment
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
     * @return IssuesAttachment
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
