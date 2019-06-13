<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FinancialReminderSetting
 *
 * @ORM\Table(name="financial_reminder_setting")
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\FinancialReminderSettingRepository")
 */
class FinancialReminderSetting
{
    /**
     * @var string
     *
     * @ORM\Column(name="reminder_code", type="string", length=10, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $reminderCode;

    /**
     * @var string
     *
     * @ORM\Column(name="group_remnider", type="string", length=10, nullable=true)
     */
    private $groupRemnider;

    /**
     * @var integer
     *
     * @ORM\Column(name="frequency_send_reminder", type="integer", nullable=true)
     */
    private $frequencySendReminder;

    /**
     * @var string
     *
     * @ORM\Column(name="frequency_send_reminder_unit", type="string", length=5, nullable=true)
     */
    private $frequencySendReminderUnit;

    /**
     * @var integer
     *
     * @ORM\Column(name="frequency_upload", type="integer", nullable=true)
     */
    private $frequencyUpload;

    /**
     * @var string
     *
     * @ORM\Column(name="frequency_upload_unit", type="string", length=5, nullable=true)
     */
    private $frequencyUploadUnit;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=250, nullable=true)
     */
    private $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="email_template", type="text", length=65535, nullable=true)
     */
    private $emailTemplate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_on", type="datetime", nullable=true)
     */
    private $updatedOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="target_date", type="datetime", nullable=true)
     */
    private $targetDate;



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
     * Set groupRemnider
     *
     * @param string $groupRemnider
     * @return FinancialReminderSetting
     */
    public function setGroupRemnider($groupRemnider)
    {
        $this->groupRemnider = $groupRemnider;

        return $this;
    }

    /**
     * Get groupRemnider
     *
     * @return string 
     */
    public function getGroupRemnider()
    {
        return $this->groupRemnider;
    }

    /**
     * Set frequencySendReminder
     *
     * @param integer $frequencySendReminder
     * @return FinancialReminderSetting
     */
    public function setFrequencySendReminder($frequencySendReminder)
    {
        $this->frequencySendReminder = $frequencySendReminder;

        return $this;
    }

    /**
     * Get frequencySendReminder
     *
     * @return integer 
     */
    public function getFrequencySendReminder()
    {
        return $this->frequencySendReminder;
    }

    /**
     * Set frequencySendReminderUnit
     *
     * @param string $frequencySendReminderUnit
     * @return FinancialReminderSetting
     */
    public function setFrequencySendReminderUnit($frequencySendReminderUnit)
    {
        $this->frequencySendReminderUnit = $frequencySendReminderUnit;

        return $this;
    }

    /**
     * Get frequencySendReminderUnit
     *
     * @return string 
     */
    public function getFrequencySendReminderUnit()
    {
        return $this->frequencySendReminderUnit;
    }

    /**
     * Set frequencyUpload
     *
     * @param integer $frequencyUpload
     * @return FinancialReminderSetting
     */
    public function setFrequencyUpload($frequencyUpload)
    {
        $this->frequencyUpload = $frequencyUpload;

        return $this;
    }

    /**
     * Get frequencyUpload
     *
     * @return integer 
     */
    public function getFrequencyUpload()
    {
        return $this->frequencyUpload;
    }

    /**
     * Set frequencyUploadUnit
     *
     * @param string $frequencyUploadUnit
     * @return FinancialReminderSetting
     */
    public function setFrequencyUploadUnit($frequencyUploadUnit)
    {
        $this->frequencyUploadUnit = $frequencyUploadUnit;

        return $this;
    }

    /**
     * Get frequencyUploadUnit
     *
     * @return string 
     */
    public function getFrequencyUploadUnit()
    {
        return $this->frequencyUploadUnit;
    }

    /**
     * Set subject
     *
     * @param string $subject
     * @return FinancialReminderSetting
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
     * Set emailTemplate
     *
     * @param string $emailTemplate
     * @return FinancialReminderSetting
     */
    public function setEmailTemplate($emailTemplate)
    {
        $this->emailTemplate = $emailTemplate;

        return $this;
    }

    /**
     * Get emailTemplate
     *
     * @return string 
     */
    public function getEmailTemplate()
    {
        return $this->emailTemplate;
    }

    /**
     * Set updatedOn
     *
     * @param \DateTime $updatedOn
     * @return FinancialReminderSetting
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
     * Set targetDate
     *
     * @param \DateTime $targetDate
     * @return FinancialReminderSetting
     */
    public function setTargetDate($targetDate)
    {
        $this->targetDate = $targetDate;

        return $this;
    }

    /**
     * Get targetDate
     *
     * @return \DateTime 
     */
    public function getTargetDate()
    {
        return $this->targetDate;
    }
}
