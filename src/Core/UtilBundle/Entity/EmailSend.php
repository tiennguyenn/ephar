<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmailSend
 *
 * @ORM\Table(name="email_send")
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\EmailSendRepository")
 */
class EmailSend
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
     * @ORM\Column(name="`from`", type="string", length=250, nullable=true)
     */
    private $from;

    /**
     * @var string
     *
     * @ORM\Column(name="`to`", type="string", length=250, nullable=true)
     */
    private $to;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=250, nullable=true)
     */
    private $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", length=65535, nullable=true)
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time_to_send", type="datetime", nullable=true)
     */
    private $timeToSend;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="sent_on", type="datetime", nullable=true)
     */
    private $sentOn;

    /**
     * @var integer
     *
     * @ORM\Column(name="counter", type="integer", nullable=true)
     */
    private $counter;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private $status = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="rx_id", type="integer", nullable=true)
     */
    private $rxId;

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
     * Set from
     *
     * @param string $from
     *
     * @return EmailSend
     */
    public function setFrom($from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * Get from
     *
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Set to
     *
     * @param string $to
     *
     * @return EmailSend
     */
    public function setTo($to)
    {
        $this->to = $to;

        return $this;
    }

    /**
     * Get to
     *
     * @return string
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Set subject
     *
     * @param string $subject
     *
     * @return EmailSend
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
     * @return EmailSend
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
     * Set timeToSend
     *
     * @param \DateTime $timeToSend
     *
     * @return EmailSend
     */
    public function setTimeToSend($timeToSend)
    {
        $this->timeToSend = $timeToSend;

        return $this;
    }

    /**
     * Get timeToSend
     *
     * @return \DateTime
     */
    public function getTimeToSend()
    {
        return $this->timeToSend;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return EmailSend
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
     * Set sentOn
     *
     * @param \DateTime $sentOn
     *
     * @return EmailSend
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

    /**
     * Set counter
     *
     * @param integer $counter
     *
     * @return EmailSend
     */
    public function setCounter($counter)
    {
        $this->counter = $counter;

        return $this;
    }

    /**
     * Get counter
     *
     * @return integer
     */
    public function getCounter()
    {
        return $this->counter;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return EmailSend
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }
    
    /**
     * Set rxId
     *
     * @param integer $rxId
     * @return EmailSend
     */
    public function setRxId($rxId)
    {
        $this->rxId = $rxId;

        return $this;
    }

    /**
     * Get rxId
     *
     * @return integer 
     */
    public function getRxId()
    {
        return $this->rxId;
    }
}
