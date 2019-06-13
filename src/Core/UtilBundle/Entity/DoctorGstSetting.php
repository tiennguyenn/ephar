<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DoctorGstSetting
 *
 * @ORM\Table(name="doctor_gst_setting", indexes={@ORM\Index(name="FK_doctor_gst_setting", columns={"gst_id"}), @ORM\Index(name="FK_doctor_gst_setting_1", columns={"new_gst_id"}), @ORM\Index(name="FK_doctor_gst_setting_doctor", columns={"doctor_id"})})
 * @ORM\Entity
 */
class DoctorGstSetting
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
     * @var boolean
     *
     * @ORM\Column(name="is_has_gst", type="boolean", nullable=true)
     */
    private $isHasGst;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="effective_date", type="datetime", nullable=true)
     */
    private $effectiveDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="fee_type", type="integer", length=1, nullable=true)
     */
    private $feeType;

    /**
     * @var string
     *
     * @ORM\Column(name="area", type="string", length=10, nullable=true)
     */
    private $area;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_on", type="datetime", nullable=true)
     */
    private $updatedOn;

    /**
     * @var \GstCode
     *
     * @ORM\ManyToOne(targetEntity="GstCode")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="gst_id", referencedColumnName="id")
     * })
     */
    private $gst;

    /**
     * @var \GstCode
     *
     * @ORM\ManyToOne(targetEntity="GstCode")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="new_gst_id", referencedColumnName="id")
     * })
     */
    private $newGst;

    /**
     * @var \Doctor
     *
     * @ORM\ManyToOne(targetEntity="Doctor")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="doctor_id", referencedColumnName="id")
     * })
     */
    private $doctor;



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
     * Set isHasGst
     *
     * @param boolean $isHasGst
     *
     * @return DoctorGstSetting
     */
    public function setIsHasGst($isHasGst)
    {
        $this->isHasGst = $isHasGst;

        return $this;
    }

    /**
     * Get isHasGst
     *
     * @return boolean
     */
    public function getIsHasGst()
    {
        return $this->isHasGst;
    }

    /**
     * Set effectiveDate
     *
     * @param \DateTime $effectiveDate
     *
     * @return DoctorGstSetting
     */
    public function setEffectiveDate($effectiveDate)
    {
        $this->effectiveDate = $effectiveDate;

        return $this;
    }

    /**
     * Get effectiveDate
     *
     * @return \DateTime
     */
    public function getEffectiveDate()
    {
        return $this->effectiveDate;
    }

    /**
     * Set feeType
     *
     * @param integer $feeType
     *
     * @return DoctorGstSetting
     */
    public function setFeeType($feeType)
    {
        $this->feeType = $feeType;

        return $this;
    }

    /**
     * Get feeType
     *
     * @return integer
     */
    public function getFeeType()
    {
        return $this->feeType;
    }

    /**
     * Set area
     *
     * @param string $area
     *
     * @return DoctorGstSetting
     */
    public function setArea($area)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * Get area
     *
     * @return string
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return DoctorGstSetting
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
     * @return DoctorGstSetting
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
     * Set gst
     *
     * @param \UtilBundle\Entity\GstCode $gst
     *
     * @return DoctorGstSetting
     */
    public function setGst(\UtilBundle\Entity\GstCode $gst = null)
    {
        $this->gst = $gst;

        return $this;
    }

    /**
     * Get gst
     *
     * @return \UtilBundle\Entity\GstCode
     */
    public function getGst()
    {
        return $this->gst;
    }

    /**
     * Set newGst
     *
     * @param \UtilBundle\Entity\GstCode $newGst
     *
     * @return DoctorGstSetting
     */
    public function setNewGst(\UtilBundle\Entity\GstCode $newGst = null)
    {
        $this->newGst = $newGst;

        return $this;
    }

    /**
     * Get newGst
     *
     * @return \UtilBundle\Entity\GstCode
     */
    public function getNewGst()
    {
        return $this->newGst;
    }

    /**
     * Set doctor
     *
     * @param \UtilBundle\Entity\Doctor $doctor
     *
     * @return DoctorGstSetting
     */
    public function setDoctor(\UtilBundle\Entity\Doctor $doctor = null)
    {
        $this->doctor = $doctor;

        return $this;
    }

    /**
     * Get doctor
     *
     * @return \UtilBundle\Entity\Doctor
     */
    public function getDoctor()
    {
        return $this->doctor;
    }
}
