<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Agent3paFee
 *
 * @ORM\Table(name="agent_3pa_fee", uniqueConstraints={@ORM\UniqueConstraint(name="FK_agent_3pa_fee_1", columns={"fee_setting_id"})}, indexes={@ORM\Index(name="FK_agent_3pa_fee", columns={"agent_id"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Agent3paFee
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
     * @ORM\Column(name="area", type="integer", nullable=true)
     */
    private $area;

    /**
     * @var integer
     *
     * @ORM\Column(name="fee_type", type="integer", nullable=true)
     */
    private $feeType;

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
     * @var \DateTime
     *
     * @ORM\Column(name="deleted_on", type="datetime", nullable=true)
     */
    private $deletedOn;

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
     * @var \FeeSetting
     *
     * @ORM\ManyToOne(targetEntity="FeeSetting", cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fee_setting_id", referencedColumnName="id")
     * })
     */
    private $feeSetting;

    /**
     * Gets triggered only on insert

     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdOn = new \DateTime("now");
    }

    /**
     * Gets triggered every time on update

     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->updatedOn = new \DateTime("now");
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
     * Set area
     *
     * @param  integer $area
     *
     * @return Agent3paFee
     */
    public function setArea($area)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * Get area
     *
     * @return integer
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * Set feeType
     *
     * @param integer $feeType
     *
     * @return Agent3paFee
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
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return Agent3paFee
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
     * @return Agent3paFee
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
     * @return Agent3paFee
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

    /**
     * Set feeSetting
     *
     * @param \UtilBundle\Entity\FeeSetting $feeSetting
     *
     * @return Agent3paFee
     */
    public function setFeeSetting(\UtilBundle\Entity\FeeSetting $feeSetting = null)
    {
        $this->feeSetting = $feeSetting;

        return $this;
    }

    /**
     * Get feeSetting
     *
     * @return \UtilBundle\Entity\FeeSetting
     */
    public function getFeeSetting()
    {
        return $this->feeSetting;
    }

    /**
     * Set deletedOn
     *
     * @param \DateTime $deletedOn
     *
     * @return Agent3paFee
     */
    public function setDeletedOn($deletedOn)
    {
        $this->deletedOn = $deletedOn;

        return $this;
    }

    /**
     * Get deletedOn
     *
     * @return \DateTime
     */
    public function getDeletedOn()
    {
        return $this->deletedOn;
    }
}
