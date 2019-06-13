<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * Issue
 *
 * @ORM\Table(name="issue", indexes={@ORM\Index(name="FK_issue_rx", columns={"rx_id"}), @ORM\Index(name="FK_issue_gap", columns={"gaps_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\IssueRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Issue
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
     * @ORM\Column(name="remarks", type="text", length=65535, nullable=true)
     */
    private $remarks;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_resolution", type="boolean", nullable=false)
     */
    private $isResolution;

    /**
     * @var integer
     *
     * @ORM\Column(name="issue_type", type="integer", nullable=true)
     */
    private $issueType = 1;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private $status = '1';

    /**
     * @var string
     *
     * @ORM\Column(name="issue_classification", type="string", length=250, nullable=false)
     */
    private $issueClassification;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=false)
     */
    private $createdOn;

    /**
     * @var string
     *
     * @ORM\Column(name="created_by", type="string", length=150, nullable=false)
     */
    private $createdBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_on", type="datetime", nullable=false)
     */
    private $updatedOn;

    /**
     * @var string
     *
     * @ORM\Column(name="updated_by", type="string", length=150, nullable=false)
     */
    private $updatedBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted_on", type="datetime", nullable=true)
     */
    private $deletedOn;

    /**
     * @var \Gaps
     *
     * @ORM\ManyToOne(targetEntity="Gap")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="gaps_id", referencedColumnName="id")
     * })
     */
    private $gaps;

    /**
     * @var \Rx
     *
     * @ORM\ManyToOne(targetEntity="Rx")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="rx_id", referencedColumnName="id")
     * })
     */
    private $rx;

     /**
     * @ORM\OneToMany(targetEntity="IssuesAttachment", mappedBy="issues", cascade={"persist", "remove" })
     */
    private $issueAttachments;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Resolve", mappedBy="issues", cascade={"persist", "remove" })
     * @ORM\JoinTable(name="resolve_issue")
     */
     private $resolves;


      /**
     * Constructor
     */
    public function __construct()
    {
        $this->issueAttachments = new ArrayCollection();
        $this->resolves = new ArrayCollection();
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
     * Set remarks
     *
     * @param string $remarks
     * @return Issue
     */
    public function setRemarks($remarks)
    {
        $this->remarks = $remarks;

        return $this;
    }

    /**
     * Get remarks
     *
     * @return string 
     */
    public function getRemarks()
    {
        return $this->remarks;
    }

    /**
     * Set issue classification
     *
     * @param string $issueClassification
     * @return Issue
     */
    public function setIssueClassification($issueClassification)
    {
        $this->issueClassification = $issueClassification;

        return $this;
    }

    /**
     * Get issue classification
     *
     * @return string 
     */
    public function getIssueClassification()
    {
        return $this->issueClassification;
    }

    /**
     * Set isResolution
     *
     * @param boolean $isResolution
     * @return Issue
     */
    public function setIsResolution($isResolution)
    {
        $this->isResolution = $isResolution;

        return $this;
    }

    /**
     * Get isResolution
     *
     * @return boolean 
     */
    public function getIsResolution()
    {
        return $this->isResolution;
    }

    /**
     * Set issueType
     *
     * @param integer $issueType
     * @return Issue
     */
    public function setIssueType($issueType)
    {
        $this->issueType = $issueType;

        return $this;
    }

    /**
     * Get issueType
     *
     * @return integer
     */
    public function getIssueType()
    {
        return $this->issueType;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return Issue
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
     * @return Issue
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
     * Set updatedOn
     *
     * @param \DateTime $updatedOn
     * @return Issue
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
     * Set updatedBy
     *
     * @param string $updatedBy
     * @return Issue
     */
    public function setUpdatedBy($updatedBy)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return string 
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * Set deletedOn
     *
     * @param \DateTime $deletedOn
     * @return Issue
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
     * Set gaps
     *
     * @param \UtilBundle\Entity\Gaps $gaps
     * @return Issue
     */
    public function setGaps(\UtilBundle\Entity\Gap $gaps = null)
    {
        $this->gaps = $gaps;

        return $this;
    }

    /**
     * Get gaps
     *
     * @return \UtilBundle\Entity\Gaps 
     */
    public function getGaps()
    {
        return $this->gaps;
    }

    /**
     * Set rx
     *
     * @param \UtilBundle\Entity\Rx $rx
     * @return Issue
     */
    public function setRx(\UtilBundle\Entity\Rx $rx = null)
    {
        $this->rx = $rx;

        return $this;
    }

    /**
     * Get rx
     *
     * @return \UtilBundle\Entity\Rx 
     */
    public function getRx()
    {
        return $this->rx;
    }

    /**
     * Add issueAttachment
     *
     * @param \UtilBundle\Entity\IssuesAttachment $issueAttachment
     *
     * @return Issue
     */
    public function addIssueAttachment(\UtilBundle\Entity\IssuesAttachment $issueAttachment)
    {
        $issueAttachment->setIssues($this);

        $this->issueAttachments[] = $issueAttachment;

        return $this;
    }

    /**
     * Remove issueAttachment
     *
     * @param \UtilBundle\Entity\IssuesAttachment $issueAttachment
     */
    public function removeIssueAttachment(\UtilBundle\Entity\IssuesAttachment $issueAttachment)
    {
        $this->issueAttachments->removeElement($issueAttachment);
    }

    /**
     * Get issueAttachments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIssueAttachments()
    {
        $result = [];
        foreach ($this->issueAttachments as $att){
            if(empty($att->getDeletedOn())){
                array_push($result,$att);
            }
        }
        return $result;
    }

     /**
     * Gets triggered only on insert

     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdOn = new \DateTime("now");
        $this->updatedOn = new \DateTime("now");
        $this->updatedBy = $this->createdBy;
    }


    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return Issue
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
     * Add resolf
     *
     * @param \UtilBundle\Entity\Resolve $resolf
     *
     * @return Issue
     */
    public function addResolf(\UtilBundle\Entity\Resolve $resolf)
    {
        $this->resolves[] = $resolf;

        return $this;
    }

    /**
     * Remove resolf
     *
     * @param \UtilBundle\Entity\Resolve $resolf
     */
    public function removeResolf(\UtilBundle\Entity\Resolve $resolf)
    {
        $this->resolves->removeElement($resolf);
    }

    /**
     * Get resolves
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getResolves()
    {
        return $this->resolves;
    }
}
