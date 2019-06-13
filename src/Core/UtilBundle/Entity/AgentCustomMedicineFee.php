<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AgentCustomMedicineFee
 *
 * @ORM\Table(name="agent_custom_medicine_fee", indexes={@ORM\Index(name="agent_minimim_fee", columns={"agent_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\AgentCustomMedicineFeeRepository")
 */
class AgentCustomMedicineFee
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
     * Set feeValue
     *
     * @param string $feeValue
     *
     * @return AgentCustomMedicineFee
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
     * @return AgentCustomMedicineFee
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
     * @return AgentCustomMedicineFee
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
     * @return AgentCustomMedicineFee
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
     * @return AgentCustomMedicineFee
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
