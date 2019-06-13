<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SmsLog
 *
 * @ORM\Table(name="sms_log")
 * @ORM\Entity
 */
class SmsLog
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
     * @ORM\Column(name="send_to", type="string", length=100, nullable=true)
     */
    private $sendTo;    
    
    /**
     * @var string
     *
     * @ORM\Column(name="send_from", type="string", length=100, nullable=true)
     */
    private $sendFrom;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="send_date", type="datetime", nullable=true)
     */
    private $sendDate;
    
    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text", length=65535, nullable=true)
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
     * Set sendTo
     *
     * @param string $sendTo
     *
     * @return SmsLog
     */
    public function setSendTo($sendTo)
    {
        $this->sendTo = $sendTo;

        return $this;
    }
    
    /**
     * Get sendTo
     *
     * @return string
     */
    public function getSendTo()
    {
        return $this->sendTo;
    }

    /**
     * Set sendFrom
     *
     * @param string $sendFrom
     *
     * @return SmsLog
     */
    public function setSendFrom($sendFrom)
    {
        $this->sendFrom = $sendFrom;

        return $this;
    }
    
    /**
     * Get sendFrom
     *
     * @return string
     */
    public function getSendFrom()
    {
        return $this->sendFrom;
    }

    /**
     * Set sendDate
     *
     * @param \DateTime $sendDate
     *
     * @return SmsLog
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
     * @return SmsLog
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
    
}
