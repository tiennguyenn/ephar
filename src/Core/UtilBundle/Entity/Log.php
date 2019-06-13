<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Log
 *
 * @ORM\Table(name="log")
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\LogRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Log
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
     * @var integer
     *
     * @ORM\Column(name="entity_id", type="integer", nullable=true)
     */
    private $entityId;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=250, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="action", type="string", length=20, nullable=true)
     */
    private $action;

    /**
     * @var string
     *
     * @ORM\Column(name="module", type="string", length=100, nullable=true)
     */
    private $module;

    /**
     * @var string
     *
     * @ORM\Column(name="old_value", type="text", length=65535, nullable=true)
     */
    private $oldValue;

    /**
     * @var string
     *
     * @ORM\Column(name="new_value", type="text", length=65535, nullable=true)
     */
    private $newValue;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var string
     *
     * @ORM\Column(name="created_by", type="string", length=250, nullable=true)
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity="IssuesAttachmentLog", mappedBy="log", cascade={"persist", "remove" })
     */
    private $attachment;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Resolve", inversedBy="logs", cascade={"persist", "remove" })
     * @ORM\JoinTable(name="resolve_log")
     *
     */
    private $resolves;

    public function __construct() {
        $this->attachment = new ArrayCollection();
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
     * Set entityId
     *
     * @param integer $entityId
     * @return Log
     */
    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;

        return $this;
    }

    /**
     * Get entityId
     *
     * @return integer 
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Log
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set action
     *
     * @param string $action
     * @return Log
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action
     *
     * @return string 
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set module
     *
     * @param string $module
     * @return Log
     */
    public function setModule($module)
    {
        $this->module = $module;

        return $this;
    }

    /**
     * Get module
     *
     * @return string 
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Set oldValue
     *
     * @param string $oldValue
     * @return Log
     */
    public function setOldValue($oldValue)
    {
        $this->oldValue = $oldValue;

        return $this;
    }

    /**
     * Get oldValue
     *
     * @return string 
     */
    public function getOldValue()
    {
        return $this->oldValue;
    }

    /**
     * Set newValue
     *
     * @param string $newValue
     * @return Log
     */
    public function setNewValue($newValue)
    {
        $this->newValue = $newValue;

        return $this;
    }

    /**
     * Get newValue
     *
     * @return string 
     */
    public function getNewValue()
    {
        return $this->newValue;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return Log
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
     * @return Log
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
     * Add attachment
     *
     * @param \UtilBundle\Entity\IssuesAttachmentLog $attachment
     *
     * @return Log
     */
    public function addAttachment(\UtilBundle\Entity\IssuesAttachmentLog $attachment)
    {
        $attachment->setLog($this);
        $this->attachment[] = $attachment;

        return $this;
    }

    /**
     * Remove attachment
     *
     * @param \UtilBundle\Entity\IssuesAttachmentLog $attachment
     */
    public function removeAttachment(\UtilBundle\Entity\IssuesAttachmentLog $attachment)
    {
        $this->attachment->removeElement($attachment);
    }

    /**
     * Get attachment
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAttachment()
    {
        return $this->attachment;
    }

    /**
     * Add resolf
     *
     * @param \UtilBundle\Entity\Log $resolf
     *
     * @return Log
     */
    public function addResolf(\UtilBundle\Entity\Log $resolf)
    {
        $this->resolves[] = $resolf;

        return $this;
    }

    /**
     * Remove resolf
     *
     * @param \UtilBundle\Entity\Log $resolf
     */
    public function removeResolf(\UtilBundle\Entity\Log $resolf)
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
    /**
     * Gets triggered only on insert

     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdOn = new \DateTime("now");
    }
}
