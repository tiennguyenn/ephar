<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MarginShare
 *
 * @ORM\Table(name="margin_share", uniqueConstraints={@ORM\UniqueConstraint(name="id", columns={"id"})}, indexes={@ORM\Index(name="FK_margin_share_rx", columns={"rx_id"}), @ORM\Index(name="FK_margin_share_agent", columns={"agent_id"}), @ORM\Index(name="FK_margin_share_doctor", columns={"doctor_id"}), @ORM\Index(name="FK_margin_share", columns={"platform_share_percentages_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\MarginShareRepository")
 */
class MarginShare
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
     * @ORM\Column(name="platform_amount", type="decimal", precision=14, scale=3, nullable=false)
     */
    private $platformAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="agent_amount", type="decimal", precision=14, scale=3, nullable=false)
     */
    private $agentAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="agent_amount_3pa", type="decimal", precision=14, scale=3, nullable=false)
     */
    private $agentAmount3pa;

    /**
     * @var string
     *
     * @ORM\Column(name="doctor_amount", type="decimal", precision=14, scale=3, nullable=false)
     */
    private $doctorAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text", length=65535, nullable=true)
     */
    private $note;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var \PlatformSharePercentages
     *
     * @ORM\ManyToOne(targetEntity="PlatformSharePercentages")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="platform_share_percentages_id", referencedColumnName="id")
     * })
     */
    private $platformSharePercentages;

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
     * @var \Agent
     *
     * @ORM\ManyToOne(targetEntity="Agent")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="agent_id_3pa", referencedColumnName="id")
     * })
     */
    private $agent3pa;

    /**
     * @var \Doctor
     *
     * @ORM\ManyToOne(targetEntity="Doctor")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="doctor_id", referencedColumnName="id")
     * })
     */
    private $doctor;

    /**
     * @var \Rx
     *
     * @ORM\ManyToOne(targetEntity="Rx")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="rx_id", referencedColumnName="id")
     * })
     */
    private $rx;



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
     * Set platformAmount
     *
     * @param string $platformAmount
     * @return MarginShare
     */
    public function setPlatformAmount($platformAmount)
    {
        $this->platformAmount = $platformAmount;

        return $this;
    }

    /**
     * Get platformAmount
     *
     * @return string 
     */
    public function getPlatformAmount()
    {
        return $this->platformAmount;
    }

    /**
     * Set agentAmount
     *
     * @param string $agentAmount
     * @return MarginShare
     */
    public function setAgentAmount($agentAmount)
    {
        $this->agentAmount = $agentAmount;

        return $this;
    }

    /**
     * Get agentAmount
     *
     * @return string 
     */
    public function getAgentAmount()
    {
        return $this->agentAmount;
    }

    /**
     * Set agentAmount3pa
     *
     * @param string $agentAmount3pa
     * @return MarginShare
     */
    public function setAgentAmount3pa($agentAmount3pa)
    {
        $this->agentAmount3pa = $agentAmount3pa;

        return $this;
    }

    /**
     * Get agentAmount3pa
     *
     * @return string 
     */
    public function getAgentAmount3pa()
    {
        return $this->agentAmount3pa;
    }

    /**
     * Set doctorAmount
     *
     * @param string $doctorAmount
     * @return MarginShare
     */
    public function setDoctorAmount($doctorAmount)
    {
        $this->doctorAmount = $doctorAmount;

        return $this;
    }

    /**
     * Get doctorAmount
     *
     * @return string 
     */
    public function getDoctorAmount()
    {
        return $this->doctorAmount;
    }

    /**
     * Set note
     *
     * @param string $note
     * @return MarginShare
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return string 
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return MarginShare
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
     * Set platformSharePercentages
     *
     * @param \UtilBundle\Entity\PlatformSharePercentages $platformSharePercentages
     * @return MarginShare
     */
    public function setPlatformSharePercentages(\UtilBundle\Entity\PlatformSharePercentages $platformSharePercentages = null)
    {
        $this->platformSharePercentages = $platformSharePercentages;

        return $this;
    }

    /**
     * Get platformSharePercentages
     *
     * @return \UtilBundle\Entity\PlatformSharePercentages 
     */
    public function getPlatformSharePercentages()
    {
        return $this->platformSharePercentages;
    }

    /**
     * Set agent
     *
     * @param \UtilBundle\Entity\Agent $agent
     * @return MarginShare
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
     * Set agent
     *
     * @param \UtilBundle\Entity\Agent $agent
     * @return MarginShare
     */
    public function setAgent3pa(\UtilBundle\Entity\Agent $agent = null)
    {
        $this->agent3pa = $agent;

        return $this;
    }

    /**
     * Get agent
     *
     * @return \UtilBundle\Entity\Agent 
     */
    public function getAgent3pa()
    {
        return $this->agent3pa;
    }

    /**
     * Set doctor
     *
     * @param \UtilBundle\Entity\Doctor $doctor
     * @return MarginShare
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
     * Set rx
     *
     * @param \UtilBundle\Entity\Rx $rx
     * @return MarginShare
     */
    public function setRx(\UtilBundle\Entity\Rx $rx = null)
    {
        $this->rx = $rx;

        return $this;
    }

    /**
     * Get rx
     *
     * @return \UtilBundle\Entity\Rx 
     */
    public function getRx()
    {
        return $this->rx;
    }
}
