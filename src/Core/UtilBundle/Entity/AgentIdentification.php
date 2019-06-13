<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AgentIdentification
 *
 * @ORM\Table(name="agent_identification", indexes={@ORM\Index(name="agent_id", columns={"agent_id"}), @ORM\Index(name="identification_id", columns={"identification_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\AgentIdentificationRepository")
 * @ORM\HasLifecycleCallbacks
 */
class AgentIdentification
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
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=false)
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
     *   @ORM\JoinColumn(name="agent_id", referencedColumnName="id")
     * })
     */
    private $agent;

    /**
     * @var \Identification
     *
     * @ORM\ManyToOne(targetEntity="Identification")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="identification_id", referencedColumnName="id")
     * })
     */
    private $identification;



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
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return AgentIdentification
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
     * @return AgentIdentification
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
     * Set agent
     *
     * @param \UtilBundle\Entity\Agent $agent
     * @return AgentIdentification
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
     * Set identification
     *
     * @param \UtilBundle\Entity\Identification $identification
     * @return AgentIdentification
     */
    public function setIdentification(\UtilBundle\Entity\Identification $identification = null)
    {
        $this->identification = $identification;

        return $this;
    }

    /**
     * Get identification
     *
     * @return \UtilBundle\Entity\Identification 
     */
    public function getIdentification()
    {
        return $this->identification;
    }
         /**
     * Gets triggered only on insert

     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdOn = new \DateTime("now");
       
    }
}
