<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RxRefillReminder
 *
 * @ORM\Table(name="rx_refill_reminder", indexes={@ORM\Index(name="rx_id", columns={"rx_id"}), @ORM\Index(name="FK_rx_refill_reminder_message", columns={"message_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\RxRefillReminderRepository")
 */
class RxRefillReminder
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
     * @ORM\Column(name="refill_supply_duration", type="integer", nullable=false)
     */
    private $refillSupplyDuration;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_on", type="datetime", nullable=true)
     */
    private $startOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="remind_on", type="datetime", nullable=true)
     */
    private $remindOn;

    /**
     * @var boolean
     *
     * @ORM\Column(name="has_been_reminded", type="boolean", nullable=true)
     */
    private $hasBeenReminded;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=false)
     */
    private $createdOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_on", type="datetime", nullable=false)
     */
    private $updatedOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted_on", type="datetime", nullable=true)
     */
    private $deletedOn;

    /**
     * @var \Message
     *
     * @ORM\ManyToOne(targetEntity="Message")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="message_id", referencedColumnName="id")
     * })
     */
    private $message;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set refillSupplyDuration
     *
     * @param integer $refillSupplyDuration
     *
     * @return RxRefillReminder
     */
    public function setRefillSupplyDuration($refillSupplyDuration)
    {
        $this->refillSupplyDuration = $refillSupplyDuration;

        return $this;
    }

    /**
     * Get refillSupplyDuration
     *
     * @return integer
     */
    public function getRefillSupplyDuration()
    {
        return $this->refillSupplyDuration;
    }

    /**
     * Set startOn
     *
     * @param \DateTime $startOn
     *
     * @return RxRefillReminder
     */
    public function setStartOn($startOn)
    {
        $this->startOn = $startOn;

        return $this;
    }

    /**
     * Get startOn
     *
     * @return \DateTime
     */
    public function getStartOn()
    {
        return $this->startOn;
    }

    /**
     * Set remindOn
     *
     * @param \DateTime $remindOn
     *
     * @return RxRefillReminder
     */
    public function setRemindOn($remindOn)
    {
        $this->remindOn = $remindOn;

        return $this;
    }

    /**
     * Get remindOn
     *
     * @return \DateTime
     */
    public function getRemindOn()
    {
        return $this->remindOn;
    }

    /**
     * Set hasBeenReminded
     *
     * @param boolean $hasBeenReminded
     *
     * @return RxRefillReminder
     */
    public function setHasBeenReminded($hasBeenReminded)
    {
        $this->hasBeenReminded = $hasBeenReminded;

        return $this;
    }

    /**
     * Get hasBeenReminded
     *
     * @return boolean
     */
    public function getHasBeenReminded()
    {
        return $this->hasBeenReminded;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return RxRefillReminder
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
     * Set updatedOn
     *
     * @param \DateTime $updatedOn
     *
     * @return RxRefillReminder
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
     * Set deletedOn
     *
     * @param \DateTime $deletedOn
     *
     * @return RxRefillReminder
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
     * Set message
     *
     * @param \UtilBundle\Entity\Message $message
     *
     * @return RxRefillReminder
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

    /**
     * Set rx
     *
     * @param \UtilBundle\Entity\Rx $rx
     *
     * @return RxRefillReminder
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
}
