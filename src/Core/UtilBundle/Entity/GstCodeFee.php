<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GstCodeFee
 *
 * @ORM\Table(name="gst_code_fee", uniqueConstraints={@ORM\UniqueConstraint(name="fee_code_unique", columns={"fee_code"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\GstCodeFeeRepository")
 */
class GstCodeFee
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
     * @ORM\Column(name="gst_code", type="string", length=5, nullable=true)
     */
    private $gstCode;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=250, nullable=true)
     */
    private $description;

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
     * Set feeCode
     *
     * @param string $feeCode
     * @return GstCodeFee
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
     * Set gstCode
     *
     * @param string $gstCode
     * @return GstCodeFee
     */
    public function setGstCode($gstCode)
    {
        $this->gstCode = $gstCode;

        return $this;
    }

    /**
     * Get gstCode
     *
     * @return string 
     */
    public function getGstCode()
    {
        return $this->gstCode;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return GstCodeFee
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
     * Set updatedOn
     *
     * @param \DateTime $updatedOn
     * @return GstCodeFee
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
