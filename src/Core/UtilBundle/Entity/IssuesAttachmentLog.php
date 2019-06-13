<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IssuesAttachmentLog
 *
 * @ORM\Table(name="issues_attachment_log", indexes={@ORM\Index(name="FK_issues_attachment_log_issue", columns={"issue_attachment_id"}), @ORM\Index(name="FK_issues_attachment_log", columns={"log_id"})})
 * @ORM\Entity
 */
class IssuesAttachmentLog
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
     * @ORM\Column(name="is_restored", type="boolean", nullable=true)
     */
    private $isRestored;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var \Log
     *
     * @ORM\ManyToOne(targetEntity="Log")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="log_id", referencedColumnName="id")
     * })
     */
    private $log;

    /**
     * @var \IssuesAttachment
     *
     * @ORM\ManyToOne(targetEntity="IssuesAttachment")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="issue_attachment_id", referencedColumnName="id")
     * })
     */
    private $issueAttachment;


    

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
     * Set isRestored
     *
     * @param boolean $isRestored
     *
     * @return IssuesAttachmentLog
     */
    public function setIsRestored($isRestored)
    {
        $this->isRestored = $isRestored;

        return $this;
    }

    /**
     * Get isRestored
     *
     * @return boolean
     */
    public function getIsRestored()
    {
        return $this->isRestored;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return IssuesAttachmentLog
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
     * Set log
     *
     * @param \UtilBundle\Entity\Log $log
     *
     * @return IssuesAttachmentLog
     */
    public function setLog(\UtilBundle\Entity\Log $log = null)
    {
        $this->log = $log;

        return $this;
    }

    /**
     * Get log
     *
     * @return \UtilBundle\Entity\Log
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * Set issueAttachment
     *
     * @param \UtilBundle\Entity\IssuesAttachment $issueAttachment
     *
     * @return IssuesAttachmentLog
     */
    public function setIssueAttachment(\UtilBundle\Entity\IssuesAttachment $issueAttachment = null)
    {
        $this->issueAttachment = $issueAttachment;

        return $this;
    }

    /**
     * Get issueAttachment
     *
     * @return \UtilBundle\Entity\IssuesAttachment
     */
    public function getIssueAttachment()
    {
        return $this->issueAttachment;
    }
}
