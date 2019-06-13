<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AgentPrimaryCustomFee
 *
 * @ORM\Table(name="agent_primary_custom_fee", indexes={@ORM\Index(name="agent_primary_custom_fee", columns={"agent_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\AgentPrimaryCustomFeeRepository")
 */
class AgentPrimaryCustomFee
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
     * @var \Agent
     *
     * @ORM\ManyToOne(targetEntity="Agent")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="agent_id", referencedColumnName="id")
     * })
     */
    private $agent;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->isActive = true;
        $this->createdOn = new \DateTime();
        $this->updatedOn = new \DateTime();
    }

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
     * @return AgentPrimaryCustomFee
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
     * @return AgentPrimaryCustomFee
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
     * @return AgentPrimaryCustomFee
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
     * @return AgentPrimaryCustomFee
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
     * @return AgentPrimaryCustomFee
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
     * @return AgentPrimaryCustomFee
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
     * @return AgentPrimaryCustomFee
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
     * @return AgentPrimaryCustomFee
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
     * @return AgentPrimaryCustomFee
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
     * @return AgentPrimaryCustomFee
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
     * Set agent
     *
     * @param \UtilBundle\Entity\Agent $agent
     *
     * @return AgentPrimaryCustomFee
     */
    public function setAgent(\UtilBundle\Entity\Agent $agent = null)
    {
        $this->agent = $agent;

        return $this;
    }

    /**
     * Get agent
     *
     * @return \UtilBundle\Entity\Agent
     */
    public function getAgent()
    {
        return $this->agent;
    }
}
