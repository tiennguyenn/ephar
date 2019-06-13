<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PlatformShareFee
 *
 * @ORM\Table(name="platform_share_fee")
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\PlatformShareFeeRepository")
 */
class PlatformShareFee
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
     * @var float
     *
     * @ORM\Column(name="platform_percentage", type="float", precision=10, scale=0, nullable=false)
     */
    private $platformPercentage;

    /**
     * @var float
     *
     * @ORM\Column(name="new_platform_percentage", type="float", precision=10, scale=0, nullable=true)
     */
    private $newPlatformPercentage;

    /**
     * @var float
     *
     * @ORM\Column(name="agent_percentage", type="float", precision=10, scale=0, nullable=false)
     */
    private $agentPercentage;

    /**
     * @var float
     *
     * @ORM\Column(name="new_agent_percentage", type="float", precision=10, scale=0, nullable=true)
     */
    private $newAgentPercentage;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="take_effect_on", type="datetime", nullable=false)
     */
    private $takeEffectOn;

    /**
     * @var integer
     *
     * @ORM\Column(name="margin_share_type", type="integer", nullable=true)
     */
    private $marginShareType;

    /**
     * @var integer
     *
     * @ORM\Column(name="area_type", type="integer", nullable=true)
     */
    private $areaType;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set platformPercentage
     *
     * @param float $platformPercentage
     *
     * @return PlatformShareFee
     */
    public function setPlatformPercentage($platformPercentage)
    {
        $this->platformPercentage = $platformPercentage;

        return $this;
    }

    /**
     * Get platformPercentage
     *
     * @return float
     */
    public function getPlatformPercentage()
    {
        return $this->platformPercentage;
    }

    /**
     * Set newPlatformPercentage
     *
     * @param float $newPlatformPercentage
     *
     * @return PlatformShareFee
     */
    public function setNewPlatformPercentage($newPlatformPercentage)
    {
        $this->newPlatformPercentage = $newPlatformPercentage;

        return $this;
    }

    /**
     * Get newPlatformPercentage
     *
     * @return float
     */
    public function getNewPlatformPercentage()
    {
        return $this->newPlatformPercentage;
    }

    /**
     * Set agentPercentage
     *
     * @param float $agentPercentage
     *
     * @return PlatformShareFee
     */
    public function setAgentPercentage($agentPercentage)
    {
        $this->agentPercentage = $agentPercentage;

        return $this;
    }

    /**
     * Get agentPercentage
     *
     * @return float
     */
    public function getAgentPercentage()
    {
        return $this->agentPercentage;
    }

    /**
     * Set newAgentPercentage
     *
     * @param float $newAgentPercentage
     *
     * @return PlatformShareFee
     */
    public function setNewAgentPercentage($newAgentPercentage)
    {
        $this->newAgentPercentage = $newAgentPercentage;

        return $this;
    }

    /**
     * Get newAgentPercentage
     *
     * @return float
     */
    public function getNewAgentPercentage()
    {
        return $this->newAgentPercentage;
    }

    /**
     * Set takeEffectOn
     *
     * @param \DateTime $takeEffectOn
     *
     * @return PlatformShareFee
     */
    public function setTakeEffectOn($takeEffectOn)
    {
        $this->takeEffectOn = $takeEffectOn;

        return $this;
    }

    /**
     * Get takeEffectOn
     *
     * @return \DateTime
     */
    public function getTakeEffectOn()
    {
        return $this->takeEffectOn;
    }

    /**
     * Set marginShareType
     *
     * @param boolean $marginShareType
     *
     * @return PlatformShareFee
     */
    public function setMarginShareType($marginShareType)
    {
        $this->marginShareType = $marginShareType;

        return $this;
    }

    /**
     * Get marginShareType
     *
     * @return boolean
     */
    public function getMarginShareType()
    {
        return $this->marginShareType;
    }

    /**
     * Set areaType
     *
     * @param boolean $areaType
     *
     * @return PlatformShareFee
     */
    public function setAreaType($areaType)
    {
        $this->areaType = $areaType;

        return $this;
    }

    /**
     * Get areaType
     *
     * @return boolean
     */
    public function getAreaType()
    {
        return $this->areaType;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return PlatformShareFee
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return PlatformShareFee
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
     * @return PlatformShareFee
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
