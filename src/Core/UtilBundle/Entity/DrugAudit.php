<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DrugAudit
 *
 * @ORM\Table(name="drug_audit", indexes={@ORM\Index(name="drug_audit_fk_user", columns={"user_id"}), @ORM\Index(name="drug_audit_idx_type", columns={"price_type"}), @ORM\Index(name="drug_audit_idx_status", columns={"status"}), @ORM\Index(name="drug_audit_idx_percent", columns={"is_percent"}), @ORM\Index(name="drug_audit_idx_create", columns={"created_on"}), @ORM\Index(name="drug_audit_fk_pharmacy", columns={"pharmacy_id"}), @ORM\Index(name="FK_drug_audit_group", columns={"drug_group_id"}), @ORM\Index(name="FK_drug_audit_drug", columns={"drug_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\DrugAuditRepository")
 */
class DrugAudit
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
     * @ORM\Column(name="old_cost_price", type="decimal", precision=18, scale=2, nullable=false)
     */
    private $oldCostPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="new_cost_price", type="decimal", precision=18, scale=2, nullable=false)
     */
    private $newCostPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="price_type", type="string", length=25, nullable=false)
     */
    private $priceType;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_percent", type="boolean", nullable=false)
     */
    private $isPercent;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="take_effect_on", type="datetime", nullable=true)
     */
    private $takeEffectOn;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=false)
     */
    private $createdOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_on", type="datetime", nullable=true)
     */
    private $updatedOn;

    /**
     * @var \Drug
     *
     * @ORM\ManyToOne(targetEntity="Drug")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="drug_id", referencedColumnName="id")
     * })
     */
    private $drug;

    /**
     * @var \Pharmacy
     *
     * @ORM\ManyToOne(targetEntity="Pharmacy")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pharmacy_id", referencedColumnName="id")
     * })
     */
    private $pharmacy;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * @var \DrugGroup
     *
     * @ORM\ManyToOne(targetEntity="DrugGroup")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="drug_group_id", referencedColumnName="id")
     * })
     */
    private $drugGroup;



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
     * Set oldCostPrice
     *
     * @param string $oldCostPrice
     * @return DrugAudit
     */
    public function setOldCostPrice($oldCostPrice)
    {
        $this->oldCostPrice = $oldCostPrice;

        return $this;
    }

    /**
     * Get oldCostPrice
     *
     * @return string 
     */
    public function getOldCostPrice()
    {
        return $this->oldCostPrice;
    }

    /**
     * Set newCostPrice
     *
     * @param string $newCostPrice
     * @return DrugAudit
     */
    public function setNewCostPrice($newCostPrice)
    {
        $this->newCostPrice = $newCostPrice;

        return $this;
    }

    /**
     * Get newCostPrice
     *
     * @return string 
     */
    public function getNewCostPrice()
    {
        return $this->newCostPrice;
    }

    /**
     * Set priceType
     *
     * @param string $priceType
     * @return DrugAudit
     */
    public function setPriceType($priceType)
    {
        $this->priceType = $priceType;

        return $this;
    }

    /**
     * Get priceType
     *
     * @return string 
     */
    public function getPriceType()
    {
        return $this->priceType;
    }

    /**
     * Set isPercent
     *
     * @param boolean $isPercent
     * @return DrugAudit
     */
    public function setIsPercent($isPercent)
    {
        $this->isPercent = $isPercent;

        return $this;
    }

    /**
     * Get isPercent
     *
     * @return boolean 
     */
    public function getIsPercent()
    {
        return $this->isPercent;
    }

    /**
     * Set takeEffectOn
     *
     * @param \DateTime $takeEffectOn
     * @return DrugAudit
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
     * Set status
     *
     * @param integer $status
     * @return DrugAudit
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return DrugAudit
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
     * @return DrugAudit
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
     * Set drug
     *
     * @param \UtilBundle\Entity\Drug $drug
     * @return DrugAudit
     */
    public function setDrug(\UtilBundle\Entity\Drug $drug = null)
    {
        $this->drug = $drug;

        return $this;
    }

    /**
     * Get drug
     *
     * @return \UtilBundle\Entity\Drug 
     */
    public function getDrug()
    {
        return $this->drug;
    }

    /**
     * Set pharmacy
     *
     * @param \UtilBundle\Entity\Pharmacy $pharmacy
     * @return DrugAudit
     */
    public function setPharmacy(\UtilBundle\Entity\Pharmacy $pharmacy = null)
    {
        $this->pharmacy = $pharmacy;

        return $this;
    }

    /**
     * Get pharmacy
     *
     * @return \UtilBundle\Entity\Pharmacy 
     */
    public function getPharmacy()
    {
        return $this->pharmacy;
    }

    /**
     * Set user
     *
     * @param \UtilBundle\Entity\User $user
     * @return DrugAudit
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
     * Set drugGroup
     *
     * @param \UtilBundle\Entity\DrugGroup $drugGroup
     * @return DrugAudit
     */
    public function setDrugGroup(\UtilBundle\Entity\DrugGroup $drugGroup = null)
    {
        $this->drugGroup = $drugGroup;

        return $this;
    }

    /**
     * Get drugGroup
     *
     * @return \UtilBundle\Entity\DrugGroup 
     */
    public function getDrugGroup()
    {
        return $this->drugGroup;
    }
}
