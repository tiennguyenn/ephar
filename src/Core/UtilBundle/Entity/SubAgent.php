<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SubAgent
 *
 * @ORM\Table(name="sub_agent", indexes={@ORM\Index(name="FK_sub_agent", columns={"agent_id"}), @ORM\Index(name="FK_sub_agent_parent_id", columns={"parent_id"})})
 * @ORM\Entity
 */
class SubAgent
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
     * @ORM\Column(name="deleted_on", type="datetime", nullable=true)
     */
    private $deletedOn;

    /**
     * @var \Agent
     *
     * @ORM\ManyToOne(targetEntity="Agent")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     * })
     */
    private $parent;

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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return SubAgent
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
     * @return SubAgent
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
     * Set deletedOn
     *
     * @param \DateTime $deletedOn
     * @return SubAgent
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

    /**
     * Set parent
     *
     * @param \UtilBundle\Entity\Agent $parent
     * @return SubAgent
     */
    public function setParent(\UtilBundle\Entity\Agent $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \UtilBundle\Entity\Agent 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set agent
     *
     * @param \UtilBundle\Entity\Agent $agent
     * @return SubAgent
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
