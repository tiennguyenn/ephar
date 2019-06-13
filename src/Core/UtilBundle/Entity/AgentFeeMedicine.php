<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AgentFeeMedicine
 *
 * @ORM\Table(name="agent_fee_medicine", indexes={@ORM\Index(name="FK_agent_fee_medicine", columns={"agent_id"})})
 * @ORM\Entity
 */
class AgentFeeMedicine
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
     * @ORM\Column(name="new_agent_fee", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $newAgentFee;

    /**
     * @var string
     *
     * @ORM\Column(name="agent_fee", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $agentFee;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="take_effect_on", type="datetime", nullable=true)
     */
    private $takeEffectOn;

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
     * @ORM\ManyToOne(targetEntity="Agent", inversedBy="agentFeeMedicine")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="agent_id", referencedColumnName="id")
     * })
     */
    private $agent;



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
     * Set newAgentFee
     *
     * @param string $newAgentFee
     *
     * @return AgentFeeMedicine
     */
    public function setNewAgentFee($newAgentFee)
    {
        $this->newAgentFee = $newAgentFee;

        return $this;
    }

    /**
     * Get newAgentFee
     *
     * @return string
     */
    public function getNewAgentFee()
    {
        return $this->newAgentFee;
    }

    /**
     * Set agentFee
     *
     * @param string $agentFee
     *
     * @return AgentFeeMedicine
     */
    public function setAgentFee($agentFee)
    {
        $this->agentFee = $agentFee;

        return $this;
    }

    /**
     * Get agentFee
     *
     * @return string
     */
    public function getAgentFee()
    {
        return $this->agentFee;
    }

    /**
     * Set takeEffectOn
     *
     * @param \DateTime $takeEffectOn
     *
     * @return AgentFeeMedicine
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
     * Set areaType
     *
     * @param boolean $areaType
     *
     * @return AgentFeeMedicine
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
     * @return AgentFeeMedicine
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
     * @return AgentFeeMedicine
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
     * @return AgentFeeMedicine
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
     * @return AgentFeeMedicine
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
