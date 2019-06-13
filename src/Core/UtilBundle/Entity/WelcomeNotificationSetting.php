<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WelcomeNotificationSetting
 *
 * @ORM\Table(name="welcome_notification_setting")
 * @ORM\Entity
 */
class WelcomeNotificationSetting
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
     * @ORM\Column(name="send_to", type="string", length=50, nullable=false)
     */
    private $sendTo;

    /**
     * @var string
     *
     * @ORM\Column(name="email_subject", type="string", length=250, nullable=false)
     */
    private $emailSubject;

    /**
     * @var string
     *
     * @ORM\Column(name="email_body", type="text", length=65535, nullable=false)
     */
    private $emailBody;

    /**
     * @var string
     *
     * @ORM\Column(name="sms_body", type="string", length=250, nullable=false)
     */
    private $smsBody;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_on", type="datetime", nullable=true)
     */
    private $updatedOn;



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
     * @return WelcomeNotificationSetting
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
     * Set emailSubject
     *
     * @param string $emailSubject
     *
     * @return WelcomeNotificationSetting
     */
    public function setEmailSubject($emailSubject)
    {
        $this->emailSubject = $emailSubject;

        return $this;
    }

    /**
     * Get emailSubject
     *
     * @return string
     */
    public function getEmailSubject()
    {
        return $this->emailSubject;
    }

    /**
     * Set emailBody
     *
     * @param string $emailBody
     *
     * @return WelcomeNotificationSetting
     */
    public function setEmailBody($emailBody)
    {
        $this->emailBody = $emailBody;

        return $this;
    }

    /**
     * Get emailBody
     *
     * @return string
     */
    public function getEmailBody()
    {
        return $this->emailBody;
    }

    /**
     * Set smsBody
     *
     * @param string $smsBody
     *
     * @return WelcomeNotificationSetting
     */
    public function setSmsBody($smsBody)
    {
        $this->smsBody = $smsBody;

        return $this;
    }

    /**
     * Get smsBody
     *
     * @return string
     */
    public function getSmsBody()
    {
        return $this->smsBody;
    }

    /**
     * Set updatedOn
     *
     * @param \DateTime $updatedOn
     *
     * @return WelcomeNotificationSetting
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
}
