<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RxReminderSetting
 *
 * @ORM\Table(name="rx_reminder_setting")
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\RxReminderSettingRepository")
 */
class RxReminderSetting
{
    /**
     * @var string
     *
     * @ORM\Column(name="reminder_code", type="string", length=20, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $reminderCode;

    /**
     * @var string
     *
     * @ORM\Column(name="reminder_name", type="string", length=50, nullable=true)
     */
    private $reminderName;

    /**
     * @var integer
     *
     * @ORM\Column(name="duration_time", type="integer", nullable=true)
     */
    private $durationTime;

    /**
     * @var integer
     *
     * @ORM\Column(name="expired_time", type="integer", nullable=true)
     */
    private $expiredTime;

    /**
     * @var string
     *
     * @ORM\Column(name="time_unit", type="string", length=10, nullable=true)
     */
    private $timeUnit;

    /**
     * @var string
     *
     * @ORM\Column(name="time_unit_expire", type="string", length=5, nullable=true)
     */
    private $timeUnitExpire;

    /**
     * @var string
     *
     * @ORM\Column(name="template_body_email", type="text", length=255, nullable=true)
     */
    private $templateBodyEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="template_subject_email", type="string", length=250, nullable=true)
     */
    private $templateSubjectEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="template_sms", type="string", length=420, nullable=true)
     */
    private $templateSms;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_on", type="datetime", nullable=true)
     */
    private $updatedOn;

    /**
     * Set reminderCode
     *
     * @param string $reminderCode
     *
     * @return RxReminderSetting
     */
    public function setReminderCode($reminderCode)
    {
        $this->reminderCode = $reminderCode;

        return $this;
    }

    /**
     * Get reminderCode
     *
     * @return string
     */
    public function getReminderCode()
    {
        return $this->reminderCode;
    }

    /**
     * Set reminderName
     *
     * @param string $reminderName
     *
     * @return RxReminderSetting
     */
    public function setReminderName($reminderName)
    {
        $this->reminderName = $reminderName;

        return $this;
    }

    /**
     * Get reminderName
     *
     * @return string
     */
    public function getReminderName()
    {
        return $this->reminderName;
    }

    /**
     * Set durationTime
     *
     * @param integer $durationTime
     *
     * @return RxReminderSetting
     */
    public function setDurationTime($durationTime)
    {
        $this->durationTime = $durationTime;

        return $this;
    }

    /**
     * Get durationTime
     *
     * @return integer
     */
    public function getDurationTime()
    {
        return $this->durationTime;
    }

    /**
     * Set expiredTime
     *
     * @param integer $expiredTime
     *
     * @return RxReminderSetting
     */
    public function setExpiredTime($expiredTime)
    {
        $this->expiredTime = $expiredTime;

        return $this;
    }

    /**
     * Get expiredTime
     *
     * @return integer
     */
    public function getExpiredTime()
    {
        return $this->expiredTime;
    }

    /**
     * Set timeUnit
     *
     * @param string $timeUnit
     *
     * @return RxReminderSetting
     */
    public function setTimeUnit($timeUnit)
    {
        $this->timeUnit = $timeUnit;

        return $this;
    }

    /**
     * Get timeUnit
     *
     * @return string
     */
    public function getTimeUnit()
    {
        return $this->timeUnit;
    }

    /**
     * Set timeUnitExpire
     *
     * @param string $timeUnitExpire
     *
     * @return RxReminderSetting
     */
    public function setTimeUnitExpire($timeUnitExpire)
    {
        $this->timeUnitExpire = $timeUnitExpire;

        return $this;
    }

    /**
     * Get timeUnitExpire
     *
     * @return string
     */
    public function getTimeUnitExpire()
    {
        return $this->timeUnitExpire;
    }

    /**
     * Set templateBodyEmail
     *
     * @param string $templateBodyEmail
     *
     * @return RxReminderSetting
     */
    public function setTemplateBodyEmail($templateBodyEmail)
    {
        $this->templateBodyEmail = $templateBodyEmail;

        return $this;
    }

    /**
     * Get templateBodyEmail
     *
     * @return string
     */
    public function getTemplateBodyEmail()
    {
        return $this->templateBodyEmail;
    }

    /**
     * Set templateSubjectEmail
     *
     * @param string $templateSubjectEmail
     *
     * @return RxReminderSetting
     */
    public function setTemplateSubjectEmail($templateSubjectEmail)
    {
        $this->templateSubjectEmail = $templateSubjectEmail;

        return $this;
    }

    /**
     * Get templateSubjectEmail
     *
     * @return string
     */
    public function getTemplateSubjectEmail()
    {
        return $this->templateSubjectEmail;
    }

    /**
     * Set templateSms
     *
     * @param string $templateSms
     *
     * @return RxReminderSetting
     */
    public function setTemplateSms($templateSms)
    {
        $this->templateSms = $templateSms;

        return $this;
    }

    /**
     * Get templateSms
     *
     * @return string
     */
    public function getTemplateSms()
    {
        return $this->templateSms;
    }

    /**
     * Set updatedOn
     *
     * @param \DateTime $updatedOn
     *
     * @return RxReminderSetting
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
