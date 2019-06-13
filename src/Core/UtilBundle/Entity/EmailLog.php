<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmailLog
 *
 * @ORM\Table(name="email_log")
 * @ORM\Entity
 */
class EmailLog
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
     * @ORM\Column(name="email_to", type="text", length=65535, nullable=true)
     */
    private $emailTo;
    
    /**
     * @var string
     *
     * @ORM\Column(name="email_from", type="string", length=100, nullable=true)
     */
    private $emailFrom;
    
    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=100, nullable=true)
     */
    private $subject;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="send_date", type="datetime", nullable=true)
     */
    private $sendDate;
    
    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text", nullable=true)
     */
    private $message;
    
    /**
     * @var string
     *
     * @ORM\Column(name="attachment", type="text", nullable=true)
     */
    private $attachment;
    
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
     * Set emailTo
     *
     * @param string $emailTo
     *
     * @return EmailLog
     */
    public function setEmailTo($emailTo)
    {
        $this->emailTo = $emailTo;

        return $this;
    }
    
    /**
     * Get emailTo
     *
     * @return string
     */
    public function getEmailTo()
    {
        return $this->emailTo;
    }

    /**
     * Set emailFrom
     *
     * @param string $emailFrom
     *
     * @return EmailLog
     */
    public function setEmailFrom($emailFrom)
    {
        $this->emailFrom = $emailFrom;

        return $this;
    }
    
    /**
     * Get emailFrom
     *
     * @return string
     */
    public function getEmailFrom()
    {
        return $this->emailFrom;
    }
    
    /**
     * Set subject
     *
     * @param string $subject
     *
     * @return EmailLog
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
     * Set sendDate
     *
     * @param \DateTime $sendDate
     *
     * @return EmailLog
     */
    public function setSendDate($sendDate)
    {
        $this->sendDate = $sendDate;

        return $this;
    }
    
    /**
     * Get sendDate
     *
     * @return \DateTime
     */
    public function getSendDate()
    {
        return $this->sendDate;
    }
    
    /**
     * Set message
     *
     * @param string $message
     *
     * @return EmailLog
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }
    
    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
    
    /**
     * Set attachment
     *
     * @param string $attachment
     *
     * @return EmailLog
     */
    public function setAttachment($attachment)
    {
        $this->attachment = $attachment;

        return $this;
    }
    
    /**
     * Get attachment
     *
     * @return string
     */
    public function getAttachment()
    {
        return $this->attachment;
    }
}
