<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PlatformSettingGstCode
 *
 * @ORM\Table(name="platform_setting_gst_code", uniqueConstraints={@ORM\UniqueConstraint(name="fee_code_unique", columns={"fee_code"})}, indexes={@ORM\Index(name="FK_gst_code_fee", columns={"gst_code_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\PlatformSettingGstCodeRepository")
 */
class PlatformSettingGstCode
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
     * @ORM\Column(name="fee_code", type="string", length=20, nullable=true)
     */
    private $feeCode;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=250, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="gst_type", type="string", length=50, nullable=true)
     */
    private $gstType;

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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set feeCode
     *
     * @param string $feeCode
     * @return PlatformSettingGstCode
     */
    public function setFeeCode($feeCode)
    {
        $this->feeCode = $feeCode;

        return $this;
    }

    /**
     * Get feeCode
     *
     * @return string 
     */
    public function getFeeCode()
    {
        return $this->feeCode;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return PlatformSettingGstCode
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set gstType
     *
     * @param string $gstType
     * @return PlatformSettingGstCode
     */
    public function setGstType($gstType)
    {
        $this->gstType = $gstType;

        return $this;
    }

    /**
     * Get gstType
     *
     * @return string 
     */
    public function getGstType()
    {
        return $this->gstType;
    }

    /**
     * Set updatedOn
     *
     * @param \DateTime $updatedOn
     * @return PlatformSettingGstCode
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
     * @return PlatformSettingGstCode
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
}
