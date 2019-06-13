<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MessageAttachment
 *
 * @ORM\Table(name="message_attachment", indexes={@ORM\Index(name="FK_message_attachment", columns={"message_id"})})
 * @ORM\Entity
 */
class MessageAttachment
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
     * @ORM\Column(name="url_attachment", type="string", length=250, nullable=true)
     */
    private $urlAttachment;
    
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=250, nullable=true)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var \Message
     *
     * @ORM\ManyToOne(targetEntity="Message", cascade={"persist"}, inversedBy="attachments")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="message_id", referencedColumnName="id")
     * })
     */
    private $message;



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
     * Set urlAttachment
     *
     * @param string $urlAttachment
     *
     * @return MessageAttachment
     */
    public function setUrlAttachment($urlAttachment)
    {
        $this->urlAttachment = $urlAttachment;

        return $this;
    }

    /**
     * Get urlAttachment
     *
     * @return string
     */
    public function getUrlAttachment()
    {
        return $this->urlAttachment;
    }
    
    /**
     * Set name
     *
     * @param string $name
     *
     * @return MessageAttachment
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return MessageAttachment
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
     * Set message
     *
     * @param \UtilBundle\Entity\Message $message
     *
     * @return MessageAttachment
     */
    public function setMessage(\UtilBundle\Entity\Message $message = null)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return \UtilBundle\Entity\Message
     */
    public function getMessage()
    {
        return $this->message;
    }
}
