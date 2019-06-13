<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FileDocumentNotification
 *
 * @ORM\Entity
 * @ORM\Table(name="file_document_notification")
 * @ORM\HasLifecycleCallbacks
 */
class FileDocumentNotification
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
     * @ORM\Column(name="document_name", type="string", length=255, nullable=true)
     */
    private $documentName;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=255, nullable=true)
     */
    private $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="send_date", type="datetime", nullable=true)
     */
    private $sendDate;

    /**
     * @var Site
     *
     * @ORM\ManyToOne(targetEntity="Site")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="site_id", referencedColumnName="id")
     * })
     */
    private $site;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

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
     * Set document_name
     *
     * @param string $documentName
     *
     * @return FileDocumentNotification
     */
    public function setDocumentName($documentName)
    {
        $this->documentName = $documentName;

        return $this;
    }

    /**
     * Get document_name
     *
     * @return string
     */
    public function getDocumentName()
    {
        return $this->documentName;
    }

    /**
     * Set subject
     *
     * @param string $subject
     *
     * @return FileDocumentNotification
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return FileDocumentNotification
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set send_date
     *
     * @param \DateTime $sendDate
     *
     * @return FileDocumentNotification
     */
    public function setSendDate($sendDate)
    {
        $this->sendDate = $sendDate;

        return $this;
    }

    /**
     * Get send_date
     *
     * @return \DateTime
     */
    public function getSendDate()
    {
        return $this->sendDate;
    }

    /**
     * Set site
     *
     * @param \UtilBundle\Entity\Site $site
     *
     * @return FileDocumentNotification
     */
    public function setSite(\UtilBundle\Entity\Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get site
     *
     * @return \UtilBundle\Entity\Site
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     *
     * @return FileDocumentNotification
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
