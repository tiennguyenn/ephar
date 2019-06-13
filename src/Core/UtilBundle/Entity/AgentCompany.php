<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AgentCompany
 *
 * @ORM\Table(name="agent_company", indexes={@ORM\Index(name="FK_agent_company", columns={"agent_id"}), @ORM\Index(name="FK_agent_company_address", columns={"address_id"}), @ORM\Index(name="FK_agent_company_phone", columns={"phone_id"})})
 * @ORM\Entity
 */
class AgentCompany
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
     * @ORM\Column(name="company_name", type="string", length=250, nullable=true)
     */
    private $companyName;

    /**
     * @var string
     *
     * @ORM\Column(name="company_registration_number", type="string", length=50, nullable=true)
     */
    private $companyRegistrationNumber;

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
     * @var \Phone
     *
     * @ORM\ManyToOne(targetEntity="Phone")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="phone_id", referencedColumnName="id")
     * })
     */
    private $phone;

    /**
     * @var \Agent
     *
     * @ORM\ManyToOne(targetEntity="Agent", inversedBy="companies", cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="agent_id", referencedColumnName="id")
     * })
     */
    private $agent;

    /**
     * @var \Address
     *
     * @ORM\ManyToOne(targetEntity="Address")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="address_id", referencedColumnName="id")
     * })
     */
    private $address;



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
     * Set companyName
     *
     * @param string $companyName
     * @return AgentCompany
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;

        return $this;
    }

    /**
     * Get companyName
     *
     * @return string 
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * Set companyRegistrationNumber
     *
     * @param string $companyRegistrationNumber
     * @return AgentCompany
     */
    public function setCompanyRegistrationNumber($companyRegistrationNumber)
    {
        $this->companyRegistrationNumber = $companyRegistrationNumber;

        return $this;
    }

    /**
     * Get companyRegistrationNumber
     *
     * @return string 
     */
    public function getCompanyRegistrationNumber()
    {
        return $this->companyRegistrationNumber;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return AgentCompany
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
     * @return AgentCompany
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
     * Set phone
     *
     * @param \UtilBundle\Entity\Phone $phone
     * @return AgentCompany
     */
    public function setPhone(\UtilBundle\Entity\Phone $phone = null)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return \UtilBundle\Entity\Phone 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set agent
     *
     * @param \UtilBundle\Entity\Agent $agent
     * @return AgentCompany
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
     * Set address
     *
     * @param \UtilBundle\Entity\Address $address
     * @return AgentCompany
     */
    public function setAddress(\UtilBundle\Entity\Address $address = null)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return \UtilBundle\Entity\Address 
     */
    public function getAddress()
    {
        return $this->address;
    }
}
