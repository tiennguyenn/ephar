<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CourierRateGstSetting
 *
 * @ORM\Table(name="courier_rate_gst_setting", indexes={@ORM\Index(name="FK_courier_rate_gst_setting", columns={"gst_code_id"}), @ORM\Index(name="FK_courier_rate_gst_setting_new", columns={"new_gst_code_id"}), @ORM\Index(name="FK_courier_rate_gst_setting_courier", columns={"courier_id"})})
 * @ORM\Entity
 */
class CourierRateGstSetting
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
     * @ORM\Column(name="is_has_gst", type="boolean", nullable=false)
     */
    private $isHasGst;

    /**
     * @var integer
     *
     * @ORM\Column(name="fee_type", type="integer", nullable=false)
     */
    private $feeType;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="effective_date", type="datetime", nullable=true)
     */
    private $effectiveDate;

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
     *   @ORM\JoinColumn(name="gst_code_id", referencedColumnName="id")
     * })
     */
    private $gstCode;

    /**
     * @var \Courier
     *
     * @ORM\ManyToOne(targetEntity="Courier")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="courier_id", referencedColumnName="id")
     * })
     */
    private $courier;

    /**
     * @var \GstCode
     *
     * @ORM\ManyToOne(targetEntity="GstCode")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="new_gst_code_id", referencedColumnName="id")
     * })
     */
    private $newGstCode;



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
     * @return CourierRateGstSetting
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
     * Set feeType
     *
     * @param integer $feeType
     *
     * @return CourierRateGstSetting
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
     * Set effectiveDate
     *
     * @param \DateTime $effectiveDate
     *
     * @return CourierRateGstSetting
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
     * Set updatedOn
     *
     * @param \DateTime $updatedOn
     *
     * @return CourierRateGstSetting
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
     * Set gstCode
     *
     * @param \UtilBundle\Entity\GstCode $gstCode
     *
     * @return CourierRateGstSetting
     */
    public function setGstCode(\UtilBundle\Entity\GstCode $gstCode = null)
    {
        $this->gstCode = $gstCode;

        return $this;
    }

    /**
     * Get gstCode
     *
     * @return \UtilBundle\Entity\GstCode
     */
    public function getGstCode()
    {
        return $this->gstCode;
    }

    /**
     * Set courier
     *
     * @param \UtilBundle\Entity\Courier $courier
     *
     * @return CourierRateGstSetting
     */
    public function setCourier(\UtilBundle\Entity\Courier $courier = null)
    {
        $this->courier = $courier;

        return $this;
    }

    /**
     * Get courier
     *
     * @return \UtilBundle\Entity\Courier
     */
    public function getCourier()
    {
        return $this->courier;
    }

    /**
     * Set newGstCode
     *
     * @param \UtilBundle\Entity\GstCode $newGstCode
     *
     * @return CourierRateGstSetting
     */
    public function setNewGstCode(\UtilBundle\Entity\GstCode $newGstCode = null)
    {
        $this->newGstCode = $newGstCode;

        return $this;
    }

    /**
     * Get newGstCode
     *
     * @return \UtilBundle\Entity\GstCode
     */
    public function getNewGstCode()
    {
        return $this->newGstCode;
    }
}
