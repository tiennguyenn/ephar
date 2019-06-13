<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Agent
 *
 * @ORM\Table(name="agent", indexes={@ORM\Index(name="personalInformation_id", columns={"personal_information_id"}), @ORM\Index(name="bank_account_id", columns={"bank_account_id"}), @ORM\Index(name="FK_agent_parent", columns={"parent_id"}), @ORM\Index(name="FK_site", columns={"site_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\AgentRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Agent
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
     * @ORM\Column(name="is_gst", type="boolean", nullable=true)
     */
    private $isGst;

    /**
     * @var string
     *
     * @ORM\Column(name="gst_no", type="string", length=50, nullable=true)
     */
    private $gstNo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="gst_effective_date", type="datetime", nullable=true)
     */
    private $gstEffectDate;


    /**
     * @var integer
     *
     * @ORM\Column(name="global_id", type="integer", nullable=false)
     */
    private $globalId;

    /**
     * @var string
     *
     * @ORM\Column(name="agent_code", type="string", length=50, nullable=true)
     */
    private $agentCode;

    /**
     * @var string
     *
     * @ORM\Column(name="business_name", type="string", length=300, nullable=true)
     */
    private $businessName;

    /**
     * @var string
     *
     * @ORM\Column(name="profile_photo_url", type="string", length=250, nullable=true)
     */
    private $profilePhotoUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="blocked_note", type="text", length=65535, nullable=true)
     */
    private $blockedNote;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=false)
     */
    private $isActive;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_3pa_agent", type="boolean", nullable=true)
     */
    private $is3paAgent;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_confirmed", type="boolean", nullable=true)
     */
    private $isConfirmed;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_use_company_address", type="boolean", nullable=true)
     */
    private $isUseCompanyAddress;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_minimun_fee_enabled", type="boolean", nullable=true)
     */
    private $isMinimunFeeEnabled;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=false)
     */
    private $createdOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_on", type="datetime", nullable=false)
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
     * @ORM\ManyToOne(targetEntity="Agent",inversedBy="child", cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     * })
     */
    private $parent;
    
    /**
     * @var \SequenceNumber
     *
     * @ORM\ManyToOne(targetEntity="User", cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="Agent", mappedBy="parent", cascade={"persist", "remove" })
     */
    private $child;


    /**
     * @var \PersonalInformation
     *
     * @ORM\ManyToOne(targetEntity="PersonalInformation", cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="personal_information_id", referencedColumnName="id",)
     * })
     */
    private $personalInformation;

    /**
     * @var \BankAccount
     *
     * @ORM\ManyToOne(targetEntity="BankAccount", cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="bank_account_id", referencedColumnName="id")
     * })
     */
    private $bankAccount;

     /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Doctor", mappedBy="agents")
     */
    private $doctors;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Identification", inversedBy="agents", cascade={"persist", "remove" })
     */
    private $identifications;
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Address", inversedBy="agents", cascade={"persist", "remove" })
     * @ORM\JoinTable(name="agent_address")
     */
    private $adresses;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Address", inversedBy="cagents", cascade={"persist", "remove" })
     * @ORM\JoinTable(name="agent_address")
     */
    private $companyAdresses;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Phone", inversedBy="agents", cascade={"persist", "remove" })
     */
    private $phones;
    
    /**
     * @ORM\OneToMany(targetEntity="AgentDoctor", mappedBy="agent", cascade={"persist", "remove" })
     */
    private $agentDoctors;
    
    /**
     * @ORM\OneToMany(targetEntity="AgentCompany", mappedBy="agent", cascade={"persist", "remove" })
     */
    private $companies;

    /**
     * @var \SequenceNumber
     *
     * @ORM\ManyToOne(targetEntity="SequenceNumber", cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sequence_number_id", referencedColumnName="id")
     * })
     */
    private $sequenceNumbers;

    /**
     * @var \Site
     *
     * @ORM\ManyToOne(targetEntity="Site", cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="site_id", referencedColumnName="id")
     * })
     */
    private $site;
    
    /**
     * @ORM\OneToMany(targetEntity="AgentFeeMedicine", mappedBy="agent", cascade={"persist", "remove"})
     */
    private $agentFeeMedicine;

    /**
     * @ORM\OneToMany(targetEntity="AgentMonthlyStatementLine", mappedBy="agent", cascade={"persist", "remove" })
     */
    private $lines;

    /**
     * @ORM\OneToMany(targetEntity="Agent3paFee", mappedBy="agent", cascade={"persist", "remove" })
     */
    private $thirdPartyFees;

    /**
     * @ORM\OneToMany(targetEntity="AgentPrimaryCustomFee", mappedBy="agent", cascade={"persist", "remove" })
     */
    private $primaryFees;

    /**
     * @ORM\OneToMany(targetEntity="AgentCustomMedicineFee", mappedBy="agent", cascade={"persist", "remove" })
     */
    private $minMedicineFees;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->doctors = new ArrayCollection();
        $this->child = new ArrayCollection();
        $this->identifications = new ArrayCollection();
        $this->adresses = new ArrayCollection();
        $this->phones = new ArrayCollection();
        $this->agentDoctors = new ArrayCollection();
        $this->companyAdresses = new ArrayCollection();
        $this->companies = new ArrayCollection();
        $this->agentFeeMedicine = new ArrayCollection();
        $this->lines = new ArrayCollection();
        $this->thirdPartyFees = new ArrayCollection();
        $this->primaryFees = new ArrayCollection();
        $this->minMedicineFees = new ArrayCollection();
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
     * Set isGst
     *
     * @param boolean $isGst
     * @return Agent
     */
    public function setIsGst($isGst)
    {
        $this->isGst = $isGst;

        return $this;
    }

    /**
     * Get isGst
     *
     * @return boolean
     */
    public function getIsGst()
    {
        return $this->isGst;
    }
    /**
     * Set gstNo
     *
     * @param string $gstNo
     * @return Doctor
     */
    public function setGstNo($gstNo)
    {
        $this->gstNo = $gstNo;

        return $this;
    }

    /**
     * Get gstNo
     *
     * @return string
     */
    public function getGstNo()
    {
        return $this->gstNo;
    }

    /**
     * Set gstEffectDate
     *
     * @param \DateTime $gstEffectDate
     * @return Doctor
     */
    public function setGstEffectDate($gstEffectDate)
    {
        $this->gstEffectDate = $gstEffectDate;

        return $this;
    }

    /**
     * Get gstEffectDate
     *
     * @return \DateTime
     */
    public function getGstEffectDate()
    {
        return $this->gstEffectDate;
    }

    /**
     * Set globalId
     *
     * @param integer $globalId
     * @return Agent
     */
    public function setGlobalId($globalId)
    {
        $this->globalId = $globalId;

        return $this;
    }

    /**
     * Get globalId
     *
     * @return integer 
     */
    public function getGlobalId()
    {
        return $this->globalId;
    }

    /**
     * Set agentCode
     *
     * @param string $agentCode
     * @return Agent
     */
    public function setAgentCode($agentCode)
    {
        $this->agentCode = $agentCode;

        return $this;
    }

    /**
     * Get agentCode
     *
     * @return string 
     */
    public function getAgentCode()
    {
        return $this->agentCode;
    }

    /**
     * Set businessName
     *
     * @param string $businessName
     * @return Agent
     */
    public function setBusinessName($businessName)
    {
        $this->businessName = $businessName;

        return $this;
    }

    /**
     * Get businessName
     *
     * @return string 
     */
    public function getBusinessName()
    {
        return $this->businessName;
    }

    /**
     * Set profilePhotoUrl
     *
     * @param string $profilePhotoUrl
     * @return Agent
     */
    public function setProfilePhotoUrl($profilePhotoUrl)
    {
        $this->profilePhotoUrl = $profilePhotoUrl;

        return $this;
    }

    /**
     * Get profilePhotoUrl
     *
     * @return string 
     */
    public function getProfilePhotoUrl()
    {
        return $this->profilePhotoUrl;
    }

    /**
     * Set blockedNote
     *
     * @param string $blockedNote
     * @return Agent
     */
    public function setBlockedNote($blockedNote)
    {
        $this->blockedNote = $blockedNote;

        return $this;
    }

    /**
     * Get blockedNote
     *
     * @return string 
     */
    public function getBlockedNote()
    {
        return $this->blockedNote;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return Agent
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
     * @return Agent
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
     * @return Agent
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
     * Set deletedOn
     *
     * @param \DateTime $deletedOn
     * @return Agent
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
     * @return Agent
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
     * Set personalIformation
     *
     * @param \UtilBundle\Entity\PersonalInformation $personalInformation
     * @return Agent
     */
    public function setPersonalinformation(\UtilBundle\Entity\PersonalInformation $personalInformation = null)
    {
        $this->personalInformation = $personalInformation;

        return $this;
    }

    /**
     * Get personalinformation
     *
     * @return \UtilBundle\Entity\PersonalInformation
     */
    public function getPersonalInformation()
    {
        return $this->personalInformation;
    }

    /**
     * Set bankAccount
     *
     * @param \UtilBundle\Entity\BankAccount $bankAccount
     * @return Agent
     */
    public function setBankAccount(\UtilBundle\Entity\BankAccount $bankAccount = null)
    {
        $this->bankAccount = $bankAccount;

        return $this;
    }

    /**
     * Get bankAccount
     *
     * @return \UtilBundle\Entity\BankAccount 
     */
    public function getBankAccount()
    {
        return $this->bankAccount;
    }

    /**
     * Add child
     *
     * @param \UtilBundle\Entity\Agent $child
     * @return Agent
     */
    public function addChild(\UtilBundle\Entity\Agent $child)
    {
        $this->child[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \UtilBundle\Entity\Agent $child
     */
    public function removeChild(\UtilBundle\Entity\Agent $child)
    {
        $this->child->removeElement($child);
    }

    /**
     * Get child
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChild()
    {
        return $this->child;
    }

    /**
     * Add doctors
     *
     * @param \UtilBundle\Entity\Doctor $doctors
     * @return Agent
     */
    public function addDoctor(\UtilBundle\Entity\Doctor $doctors)
    {
        $this->doctors[] = $doctors;

        return $this;
    }

    /**
     * Remove doctors
     *
     * @param \UtilBundle\Entity\Doctor $doctors
     */
    public function removeDoctor(\UtilBundle\Entity\Doctor $doctors)
    {
        $this->doctors->removeElement($doctors);
    }

    /**
     * Get doctors
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDoctors()
    {
        return $this->doctors;
    }
     /**
     * Gets triggered only on insert
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdOn = new \DateTime("now");
        $this->updatedOn = new \DateTime("now");
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
     * Add identifications
     *
     * @param \UtilBundle\Entity\Identification $identifications
     * @return Agent
     */
    public function addIdentification(\UtilBundle\Entity\Identification $identifications)
    {
        $this->identifications[] = $identifications;

        return $this;
    }

    /**
     * Remove identifications
     *
     * @param \UtilBundle\Entity\Identification $identifications
     */
    public function removeIdentification(\UtilBundle\Entity\Identification $identifications)
    {
        $this->identifications->removeElement($identifications);
    }

    /**
     * Get identifications
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIdentifications()
    {
        return $this->identifications;
    }

    /**
     * Add adresses
     *
     * @param \UtilBundle\Entity\Address $adresses
     * @return Agent
     */
    public function addAdress(\UtilBundle\Entity\Address $adresses)
    {        
        $this->adresses[] = $adresses;

        return $this;
    }

    /**
     * Remove adresses
     *
     * @param \UtilBundle\Entity\Address $adresses
     */
    public function removeAdress(\UtilBundle\Entity\Address $adresses)
    {
        $this->adresses->removeElement($adresses);
    }

    /**
     * Get adresses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAdresses()
    {
        return $this->adresses;
    }

    /**
     * Add phones
     *
     * @param \UtilBundle\Entity\Phone $phones
     * @return Agent
     */
    public function addPhone(\UtilBundle\Entity\Phone $phones)
    {
        $this->phones[] = $phones;

        return $this;
    }

    /**
     * Remove phones
     *
     * @param \UtilBundle\Entity\Phone $phones
     */
    public function removePhone(\UtilBundle\Entity\Phone $phones)
    {
        $this->phones->removeElement($phones);
    }

    /**
     * Get phones
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPhones()
    {
        return $this->phones;
    }


    /**
     * Add agentDoctors
     *
     * @param \UtilBundle\Entity\AgentDoctor $agentDoctors
     * @return Agent
     */
    public function addAgentDoctor(\UtilBundle\Entity\AgentDoctor $agentDoctors)
    {
        $this->agentDoctors[] = $agentDoctors;

        return $this;
    }

    /**
     * Remove agentDoctors
     *
     * @param \UtilBundle\Entity\AgentDoctor $agentDoctors
     */
    public function removeAgentDoctor(\UtilBundle\Entity\AgentDoctor $agentDoctors)
    {
        $this->agentDoctors->removeElement($agentDoctors);
    }

    /**
     * Get agentDoctors
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAgentDoctors()
    {
        return $this->agentDoctors;
    }
    
    /*
     * get undeleted child
     */
    
    public function getActiveChild(){
        $children = $this->getChild();
        $result = array();
        foreach ($children as $obj) {
            if(empty($obj->getDeletedOn())) {
                $result[] = $obj;
            }
        }
        return $result;
    }

    /**
     * Set user
     *
     * @param \UtilBundle\Entity\User $user
     *
     * @return Agent
     */
    public function setUser(\UtilBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \UtilBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set isConfirmed
     *
     * @param boolean $isConfirmed
     *
     * @return Agent
     */
    public function setIsConfirmed($isConfirmed)
    {
        $this->isConfirmed = $isConfirmed;

        return $this;
    }

    /**
     * Get isConfirmed
     *
     * @return boolean
     */
    public function getIsConfirmed()
    {
        return $this->isConfirmed;
    }

    /**
     * Add companyAdress
     *
     * @param \UtilBundle\Entity\Address $companyAdress
     *
     * @return Agent
     */
    public function addCompanyAdress(\UtilBundle\Entity\Address $companyAdress)
    {
        $this->companyAdresses[] = $companyAdress;

        return $this;
    }

    /**
     * Remove companyAdress
     *
     * @param \UtilBundle\Entity\Address $companyAdress
     */
    public function removeCompanyAdress(\UtilBundle\Entity\Address $companyAdress)
    {
        $this->companyAdresses->removeElement($companyAdress);
    }

    /**
     * Get companyAdresses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCompanyAdresses()
    {
        return $this->companyAdresses;
    }

    /**
     * Set isUseCompanyAddress
     *
     * @param boolean $isUseCompanyAddress
     *
     * @return Agent
     */
    public function setIsUseCompanyAddress($isUseCompanyAddress)
    {
        $this->isUseCompanyAddress = $isUseCompanyAddress;

        return $this;
    }

    /**
     * Get isUseCompanyAddress
     *
     * @return boolean
     */
    public function getIsUseCompanyAddress()
    {
        return $this->isUseCompanyAddress;
    }

    /**
     * Add company
     *
     * @param \UtilBundle\Entity\AgentCompany $company
     *
     * @return Agent
     */
    public function addCompany(\UtilBundle\Entity\AgentCompany $company)
    {
        $this->companies[] = $company;

        return $this;
    }

    /**
     * Remove company
     *
     * @param \UtilBundle\Entity\AgentCompany $company
     */
    public function removeCompany(\UtilBundle\Entity\AgentCompany $company)
    {
        $this->companies->removeElement($company);
    }

    /**
     * Get companies
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCompanies()
    {
        return $this->companies;
    }

    /**
     * Set sequenceNumbers
     *
     * @param \UtilBundle\Entity\SequenceNumber $sequenceNumbers
     * @return Doctor
     */
    public function setSequenceNumbers(\UtilBundle\Entity\SequenceNumber $sequenceNumbers = null)
    {
        $this->sequenceNumbers = $sequenceNumbers;
        return $this;
    }

    /**
     * Get sequenceNumbers
     *
     * @return \UtilBundle\Entity\SequenceNumber
     */
    public function getSequenceNumbers()
    {
        return $this->sequenceNumbers;
    }

    /**
     * Set site
     *
     * @param \UtilBundle\Entity\Site $site
     * @return Agent
     */
    public function setSite(\UtilBundle\Entity\Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get site
     *
     * @return \UtilBundle\Entity\Site
     */
    public function getSite()
    {
        return $this->site;
    }

    public function getAgentFeeMedicine()
    {
        return $this->agentFeeMedicine;
    }

    public function addAgentFeeMedicine($agentFeeMedicine)
    {
        $agentFeeMedicine->setAgent($this);
        $this->agentFeeMedicine->add($agentFeeMedicine);
    }

    /**
     * Remove agentFeeMedicine
     *
     * @param \UtilBundle\Entity\AgentFeeMedicine $agentFeeMedicine
     */
    public function removeAgentFeeMedicine(\UtilBundle\Entity\AgentFeeMedicine $agentFeeMedicine)
    {
        $this->agentFeeMedicine->removeElement($agentFeeMedicine);
    }

    /**
     * Add line
     *
     * @param \UtilBundle\Entity\AgentMonthlyStatementLine $line
     *
     * @return Agent
     */
    public function addLine(\UtilBundle\Entity\AgentMonthlyStatementLine $line)
    {
        $this->lines[] = $line;

        return $this;
    }

    /**
     * Remove line
     *
     * @param \UtilBundle\Entity\AgentMonthlyStatementLine $line
     */
    public function removeLine(\UtilBundle\Entity\AgentMonthlyStatementLine $line)
    {
        $this->lines->removeElement($line);
    }

    /**
     * Get lines
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLines()
    {
        return $this->lines;
    }

    /**
     * Set is3paAgent
     *
     * @param boolean $is3paAgent
     *
     * @return Agent
     */
    public function setIs3paAgent($is3paAgent)
    {
        $this->is3paAgent = $is3paAgent;

        return $this;
    }

    /**
     * Get is3paAgent
     *
     * @return boolean
     */
    public function getIs3paAgent()
    {
        return $this->is3paAgent;
    }

    /**
     * Add thirdPartyFee
     *
     * @param \UtilBundle\Entity\Agent3paFee $thirdPartyFee
     *
     * @return Agent
     */
    public function addThirdPartyFee(\UtilBundle\Entity\Agent3paFee $thirdPartyFee)
    {
        $thirdPartyFee->setAgent($this);
        $this->thirdPartyFees[] = $thirdPartyFee;

        return $this;
    }

    /**
     * Remove thirdPartyFee
     *
     * @param \UtilBundle\Entity\Agent3paFee $thirdPartyFee
     */
    public function removeThirdPartyFee(\UtilBundle\Entity\Agent3paFee $thirdPartyFee)
    {
        $this->thirdPartyFees->removeElement($thirdPartyFee);
    }

    /**
     * Get thirdPartyFees
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getThirdPartyFees()
    {
        return $this->thirdPartyFees;
    }

    /**
     * Add addPrimaryFee
     *
     * @param \UtilBundle\Entity\AgentPrimaryCustomFee $primaryFee
     *
     * @return Agent
     */
    public function addPrimaryFee(\UtilBundle\Entity\AgentPrimaryCustomFee $primaryFee)
    {
        $primaryFee->setAgent($this);
        $this->primaryFees[] = $primaryFee;

        return $this;
    }

    /**
     * Remove removePrimaryFee
     *
     * @param \UtilBundle\Entity\AgentPrimaryCustomFee $primaryFee
     */
    public function removePrimaryFee(\UtilBundle\Entity\AgentPrimaryCustomFee $primaryFee)
    {
        $this->primaryFees->removeElement($primaryFee);
    }

    /**
     * Get getPrimaryFees
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPrimaryFees()
    {
        return $this->primaryFees;
    }

    /**
     * Add addMinMedicineFee
     *
     * @param \UtilBundle\Entity\AgentCustomMedicineFee $minMedicineFee
     *
     * @return Agent
     */
    public function addMinMedicineFee(\UtilBundle\Entity\AgentCustomMedicineFee $minMedicineFee)
    {
        $minMedicineFee->setAgent($this);
        $this->minMedicineFees[] = $minMedicineFee;

        return $this;
    }

    /**
     * Remove removeMinMedicineFee
     *
     * @param \UtilBundle\Entity\AgentCustomMedicineFee $minMedicineFee
     */
    public function removeMinMedicineFee(\UtilBundle\Entity\AgentCustomMedicineFee $minMedicineFee)
    {
        $this->minMedicineFees->removeElement($minMedicineFee);
    }

    /**
     * Get getMinMedicineFees
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMinMedicineFees()
    {
        return $this->minMedicineFees;
    }

    /**
     * Set isMinimunFeeEnabled
     *
     * @param boolean $isMinimunFeeEnabled
     *
     * @return Agent
     */
    public function setIsMinimunFeeEnabled($isMinimunFeeEnabled)
    {
        $this->isMinimunFeeEnabled = $isMinimunFeeEnabled;

        return $this;
    }

    /**
     * Get isMinimunFeeEnabled
     *
     * @return boolean
     */
    public function getIsMinimunFeeEnabled()
    {
        return $this->isMinimunFeeEnabled;
    }

    public function isSecondAgent()
    {
        $agents = $this->getAgentDoctors();
        foreach ($agents as $value) {
            $agent = $value->getAgent();
            if ($agent->getId() != $this->getId()) {
                continue;
            }

            if ($value->getIsPrimary()) {
                continue;
            }

            if ($value->getIsActive() &&
                    empty($value->getDeletedOn()) && 
                    $agent->getIs3paAgent()) {
                return true;
            }
        }

        return false;
    }
}
