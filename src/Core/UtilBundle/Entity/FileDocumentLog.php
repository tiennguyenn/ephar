<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FileDocumentLog
 *
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\FileDocumentLogRepository")
 * @ORM\Table(name="file_document_log", indexes={@ORM\Index(name="FK_file_document", columns={"file_document_id"}), @ORM\Index(name="FK_file_document_log", columns={"before_file_document_log_id"})})
 * @ORM\HasLifecycleCallbacks
 */
class FileDocumentLog
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
     * @var FileDocument
     *
     * @ORM\ManyToOne(targetEntity="FileDocument")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="file_document_id", referencedColumnName="id")
     * })
     */
    private $fileDocument;

    /**
     * @var FileDocumentLog
     *
     * @ORM\ManyToOne(targetEntity="FileDocumentLog")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="before_file_document_log_id", referencedColumnName="id")
     * })
     */
    private $beforeFileDocumentLog;

    /**
     * @var string
     *
     * @ORM\Column(name="content_after", type="text", nullable=false)
     */
    private $contentAfter;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="effective_date", type="datetime", nullable=true)
     */
    private $effectiveDate;

    /**
     *
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->setCreatedAt(new \DateTime("now"));
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
     * Set fileDocument
     *
     * @param \UtilBundle\Entity\FileDocument $fileDocument
     *
     * @return FileDocumentLog
     */
    public function setFileDocument(\UtilBundle\Entity\FileDocument $fileDocument = null)
    {
        $this->fileDocument = $fileDocument;

        return $this;
    }

    /**
     * Get fileDocument
     *
     * @return \UtilBundle\Entity\FileDocument
     */
    public function getFileDocument()
    {
        return $this->fileDocument;
    }

    /**
     * Set before_file_document_log
     *
     * @param \UtilBundle\Entity\FileDocumentLog $fileDocument
     *
     * @return FileDocumentLog
     */
    public function setBeforeFileDocumentLog(\UtilBundle\Entity\FileDocumentLog $fileDocument = null)
    {
        $this->beforeFileDocumentLog = $fileDocument;

        return $this;
    }

    /**
     * Get before_file_document_log
     *
     * @return \UtilBundle\Entity\FileDocumentLog
     */
    public function getBeforeFileDocumentLog()
    {
        return $this->beforeFileDocumentLog;
    }

    /**
     * Set content_after
     *
     * @param string $contentAfter
     *
     * @return FileDocumentLog
     */
    public function setContentAfter($contentAfter)
    {
        $this->contentAfter = $contentAfter;

        return $this;
    }

    /**
     * Get contentAfter
     *
     * @return string
     */
    public function getContentAfter()
    {
        return $this->contentAfter;
    }

    /**
     * Set effective_date
     *
     * @param \DateTime $effectiveDate
     *
     * @return FileDocumentLog
     */
    public function setEffectiveDate($effectiveDate)
    {
        $this->effectiveDate = $effectiveDate;

        return $this;
    }

    /**
     * Get effective_date
     *
     * @return \DateTime
     */
    public function getEffectiveDate()
    {
        return $this->effectiveDate;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     *
     * @return FileDocumentLog
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
