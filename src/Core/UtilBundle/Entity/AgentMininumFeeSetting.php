<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AgentMininumFeeSetting
 *
 * @ORM\Table(name="agent_mininum_fee_setting")
 * @ORM\Entity
 */
class AgentMininumFeeSetting
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
     * @ORM\Column(name="fee_value", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $feeValue;

    /**
     * @var string
     *
     * @ORM\Column(name="fee_name", type="string", length=50, nullable=true)
     */
    private $feeName;

    /**
     * @var string
     *
     * @ORM\Column(name="fee_code", type="string", length=10, nullable=true)
     */
    private $feeCode;

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
     * Set feeValue
     *
     * @param string $feeValue
     *
     * @return AgentMininumFeeSetting
     */
    public function setFeeValue($feeValue)
    {
        $this->feeValue = $feeValue;

        return $this;
    }

    /**
     * Get feeValue
     *
     * @return string
     */
    public function getFeeValue()
    {
        return $this->feeValue;
    }

    /**
     * Set feeName
     *
     * @param string $feeName
     *
     * @return AgentMininumFeeSetting
     */
    public function setFeeName($feeName)
    {
        $this->feeName = $feeName;

        return $this;
    }

    /**
     * Get feeName
     *
     * @return string
     */
    public function getFeeName()
    {
        return $this->feeName;
    }

    /**
     * Set feeCode
     *
     * @param string $feeCode
     *
     * @return AgentMininumFeeSetting
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
     * Set updatedOn
     *
     * @param \DateTime $updatedOn
     *
     * @return AgentMininumFeeSetting
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
