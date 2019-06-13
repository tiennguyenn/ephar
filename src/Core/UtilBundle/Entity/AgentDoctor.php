<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AgentDoctor
 *
 * @ORM\Table(name="agent_doctor", indexes={@ORM\Index(name="agent_id", columns={"agent_id"}), @ORM\Index(name="doctor_id", columns={"doctor_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\AgentDoctorRepository")
 * @ORM\HasLifecycleCallbacks
 */
class AgentDoctor
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
     * @ORM\Column(name="is_active", type="integer", nullable=true)
     */
    private $isActive;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_primary", type="boolean", nullable=true)
     */
    private $isPrimary;

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
     * @ORM\ManyToOne(targetEntity="Agent",cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="agent_id", referencedColumnName="id")
     * })
     */
    private $agent;

    /**
     * @var \Doctor
     *
     * @ORM\ManyToOne(targetEntity="Doctor", inversedBy="agentDoctors",cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="doctor_id", referencedColumnName="id")
     * })
     */
    private $doctor;



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
     * @param integer $isActive
     * @return AgentDoctor
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return integer 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return AgentDoctor
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
     * @return AgentDoctor
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
     * @return AgentDoctor
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
     * Set doctor
     *
     * @param \UtilBundle\Entity\Doctor $doctor
     * @return AgentDoctor
     */
    public function setDoctor(\UtilBundle\Entity\Doctor $doctor = null)
    {
        $this->doctor = $doctor;

        return $this;
    }

    /**
     * Get doctor
     *
     * @return \UtilBundle\Entity\Doctor 
     */
    public function getDoctor()
    {
        return $this->doctor;
    }
     /**
     * Gets triggered only on insert

     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdOn = new \DateTime("now");
       
    }


    /**
     * Set isPrimary
     *
     * @param boolean $isPrimary
     *
     * @return AgentDoctor
     */
    public function setIsPrimary($isPrimary)
    {
        $this->isPrimary = $isPrimary;

        return $this;
    }

    /**
     * Get isPrimary
     *
     * @return boolean
     */
    public function getIsPrimary()
    {
        return $this->isPrimary;
    }
}
