<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Message
 *
 * @ORM\Table(name="message", indexes={@ORM\Index(name="FK_message_content", columns={"content_id"}), @ORM\Index(name="FK_message_user_sender", columns={"sender_id"}), @ORM\Index(name="FK_message_receive", columns={"receiver_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\MessageRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Message
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
     * @ORM\Column(name="parent_message_id", type="integer", nullable=true)
     */
    private $parentMessageId;

    /**
     * @var string
     *
     * @ORM\Column(name="sender_name", type="string", length=255, nullable=true)
     */
    private $senderName;

    /**
     * @var string
     *
     * @ORM\Column(name="sender_email", type="string", length=255, nullable=true)
     */
    private $senderEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="receiver_name", type="string", length=255, nullable=true)
     */
    private $receiverName;

    /**
     * @var string
     *
     * @ORM\Column(name="receiver_email", type="string", length=255, nullable=true)
     */
    private $receiverEmail;

    /**
     * @var integer
     *
     * @ORM\Column(name="receiver_type", type="integer", nullable=true)
     */
    private $receiverType;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="receiver_deleted_on", type="datetime", nullable=true)
     */
    private $receiverDeletedOn;
    
    /**
     * @var string
     *
     * @ORM\Column(name="receiver_group", type="string", length=50, nullable=true)
     */
    private $receiverGroup;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="sent_date", type="datetime", nullable=true)
     */
    private $sentDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="read_date", type="datetime", nullable=true)
     */
    private $readDate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean", nullable=true)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted_on", type="datetime", nullable=true)
     */
    private $deletedOn;

    /**
     * @var \MessageContent
     *
     * @ORM\ManyToOne(targetEntity="MessageContent",cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="content_id", referencedColumnName="id")
     * })
     */
    private $content;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="receiver_id", referencedColumnName="id")
     * })
     */
    private $receiver;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sender_id", referencedColumnName="id")
     * })
     */
    private $sender;

     /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="MessageAttachment", mappedBy="message", cascade={"persist"})
     */
    private $attachments;


    public function __construct()
    {
        $this->attachments = new ArrayCollection();
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
     * Set parentMessageId
     *
     * @param integer $parentMessageId
     * @return Message
     */
    public function setParentMessageId($parentMessageId)
    {
        $this->parentMessageId = $parentMessageId;

        return $this;
    }

    /**
     * Get parentMessageId
     *
     * @return integer 
     */
    public function getParentMessageId()
    {
        return $this->parentMessageId;
    }

    /**
     * Set senderName
     *
     * @param string $senderName
     * @return Message
     */
    public function setSenderName($senderName)
    {
        $this->senderName = $senderName;

        return $this;
    }

    /**
     * Get senderName
     *
     * @return string 
     */
    public function getSenderName()
    {
        return $this->senderName;
    }

    /**
     * Set senderEmail
     *
     * @param string $senderEmail
     * @return Message
     */
    public function setSenderEmail($senderEmail)
    {
        $this->senderEmail = $senderEmail;

        return $this;
    }

    /**
     * Get senderEmail
     *
     * @return string 
     */
    public function getSenderEmail()
    {
        return $this->senderEmail;
    }

    /**
     * Set receiverName
     *
     * @param string $receiverName
     * @return Message
     */
    public function setReceiverName($receiverName)
    {
        $this->receiverName = $receiverName;

        return $this;
    }

    /**
     * Get receiverName
     *
     * @return string 
     */
    public function getReceiverName()
    {
        return $this->receiverName;
    }

    /**
     * Set receiverEmail
     *
     * @param string $receiverEmail
     * @return Message
     */
    public function setReceiverEmail($receiverEmail)
    {
        $this->receiverEmail = $receiverEmail;

        return $this;
    }

    /**
     * Get receiverEmail
     *
     * @return string 
     */
    public function getReceiverEmail()
    {
        return $this->receiverEmail;
    }

    /**
     * Set receiverType
     *
     * @param integer $receiverType
     * @return Message
     */
    public function setReceiverType($receiverType)
    {
        $this->receiverType = $receiverType;

        return $this;
    }

    /**
     * Get receiverType
     *
     * @return integer 
     */
    public function getReceiverType()
    {
        return $this->receiverType;
    }
    
    /**
     * Set receiverDeletedOn
     *
     * @param \DateTime $receiverDeletedOn
     * @return Message
     */
    public function setReceiverDeletedOn($receiverDeletedOn)
    {
        $this->receiverDeletedOn = $receiverDeletedOn;

        return $this;
    }

    /**
     * Get receiverDeletedOn
     *
     * @return \DateTime 
     */
    public function getReceiverDeletedOn()
    {
        return $this->receiverDeletedOn;
    }
    
    /**
     * Set receiverGroup
     *
     * @param string $receiverGroup
     * @return Message
     */
    public function setReceiverGroup($receiverGroup)
    {
        $this->receiverGroup = $receiverGroup;

        return $this;
    }
    
    /**
     * Get receiverGroup
     *
     * @return string 
     */
    public function getReceiverGroup()
    {
        return $this->receiverGroup;
    }


    /**
     * Set sentDate
     *
     * @param \DateTime $sentDate
     * @return Message
     */
    public function setSentDate($sentDate)
    {
        $this->sentDate = $sentDate;

        return $this;
    }

    /**
     * Get sentDate
     *
     * @return \DateTime 
     */
    public function getSentDate()
    {
        return $this->sentDate;
    }

    /**
     * Set readDate
     *
     * @param \DateTime $readDate
     * @return Message
     */
    public function setReadDate($readDate)
    {
        $this->readDate = $readDate;

        return $this;
    }

    /**
     * Get readDate
     *
     * @return \DateTime 
     */
    public function getReadDate()
    {
        return $this->readDate;
    }

    /**
     * Set status
     *
     * @param boolean $status
     * @return Message
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
     * Set deletedOn
     *
     * @param \DateTime $deletedOn
     * @return Message
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
     * Set content
     *
     * @param \UtilBundle\Entity\MessageContent $content
     * @return Message
     */
    public function setContent(\UtilBundle\Entity\MessageContent $content = null)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return \UtilBundle\Entity\MessageContent 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set receiver
     *
     * @param \UtilBundle\Entity\User $receiver
     * @return Message
     */
    public function setReceiver(\UtilBundle\Entity\User $receiver = null)
    {
        $this->receiver = $receiver;

        return $this;
    }

    /**
     * Get receiver
     *
     * @return \UtilBundle\Entity\User 
     */
    public function getReceiver()
    {
        return $this->receiver;
    }

    /**
     * Set sender
     *
     * @param \UtilBundle\Entity\User $sender
     * @return Message
     */
    public function setSender(\UtilBundle\Entity\User $sender = null)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Get sender
     *
     * @return \UtilBundle\Entity\User 
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Add attachment
     *
     * @param \UtilBundle\Entity\MessageAttachment $attachment
     * @return Doctor
     */
    public function addAttachment(\UtilBundle\Entity\MessageAttachment $attachment)
    {
        $this->attachments[] = $attachment;

        return $this;
    }

    /**
     * Remove agents
     *
     * @param \UtilBundle\Entity\MessageAttachment $attachment
     */
    public function removeAttachment(\UtilBundle\Entity\MessageAttachment $attachment)
    {
        $this->attachments->removeElement($attachment);
    }

    /**
     * Get agents
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAttachments()
    {
        return $this->attachments;
    }
    /**
     * Gets triggered only on insert

     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->sentDate = new \DateTime("now");
    }


}
