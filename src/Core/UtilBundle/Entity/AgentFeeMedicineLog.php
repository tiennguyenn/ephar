<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AgentFeeMedicineLog
 *
 * @ORM\Table(name="agent_fee_medicine_log", indexes={@ORM\Index(name="FK_agent_fee_medicine", columns={"agent_id"})})
 * @ORM\Entity
 */
class AgentFeeMedicineLog
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
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer", nullable=true)
     */
    private $userId;

    /**
     * @var integer
     *
     * @ORM\Column(name="agent_fee_medicine_id", type="integer", nullable=true)
     */
    private $agentFeeMedicineId;

    /**
     * @var integer
     *
     * @ORM\Column(name="area_type", type="integer", nullable=true)
     */
    private $areaType;

    /**
     * @var string
     *
     * @ORM\Column(name="old_agent_fee", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $oldAgentFee;

    /**
     * @var string
     *
     * @ORM\Column(name="new_agent_fee", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $newAgentFee;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="old_effective_date", type="datetime", nullable=true)
     */
    private $oldEffectiveDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="new_effective_date", type="datetime", nullable=true)
     */
    private $newEffectiveDate;

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
     * Set userId
     *
     * @param integer $userId
     * @return PaymentStatus
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set agentFeeMedicineId
     *
     * @param integer $agentFeeMedicineId
     * @return PaymentStatus
     */
    public function setAgentFeeMedicineId($agentFeeMedicineId)
    {
        $this->agentFeeMedicineId = $agentFeeMedicineId;

        return $this;
    }

    /**
     * Get agentFeeMedicineId
     *
     * @return integer 
     */
    public function getAgentFeeMedicineId()
    {
        return $this->agentFeeMedicineId;
    }

    /**
     * Set areaType
     *
     * @param boolean $areaType
     *
     * @return AgentFeeMedicineLog
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
     * Set oldAgentFee
     *
     * @param string $oldAgentFee
     *
     * @return AgentFeeMedicineLog
     */
    public function setOldAgentFee($oldAgentFee)
    {
        $this->oldAgentFee = $oldAgentFee;

        return $this;
    }

    /**
     * Get oldAgentFee
     *
     * @return string
     */
    public function getOldAgentFee()
    {
        return $this->oldAgentFee;
    }

    /**
     * Set newAgentFee
     *
     * @param string $newAgentFee
     *
     * @return AgentFeeMedicineLog
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
     * Set oldEffectiveDate
     *
     * @param \DateTime $oldEffectiveDate
     *
     * @return AgentFeeMedicineLog
     */
    public function setOldEffectiveDate($oldEffectiveDate)
    {
        $this->oldEffectiveDate = $oldEffectiveDate;

        return $this;
    }

    /**
     * Get oldEffectiveDate
     *
     * @return \DateTime
     */
    public function getOldEffectiveDate()
    {
        return $this->oldEffectiveDate;
    }

    /**
     * Set newEffectiveDate
     *
     * @param \DateTime $newEffectiveDate
     *
     * @return AgentFeeMedicineLog
     */
    public function setNewEffectiveDate($newEffectiveDate)
    {
        $this->newEffectiveDate = $newEffectiveDate;

        return $this;
    }

    /**
     * Get newEffectiveDate
     *
     * @return \DateTime
     */
    public function getNewEffectiveDate()
    {
        return $this->newEffectiveDate;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return AgentFeeMedicineLog
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
     * @return AgentFeeMedicineLog
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
