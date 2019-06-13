<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * XeroAccountCode
 *
 * @ORM\Table(name="xero_account_code", indexes={@ORM\Index(name="xero_code_type_id", columns={"xero_code_type_id"}), @ORM\Index(name="xero_coa_version_id", columns={"xero_coa_version_id"}), @ORM\Index(name="xero_tax_rate_id", columns={"xero_tax_rate_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\XeroAccountCodeRepository")
 * @ORM\HasLifecycleCallbacks
 */
class XeroAccountCode
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
     * @ORM\Column(name="code", type="string", length=14, nullable=false)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=150, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=180, nullable=false)
     */
    private $description;

    /**
     * @var boolean
     *
     * @ORM\Column(name="dashboard", type="integer", nullable=false)
     */
    private $dashboard;

    /**
     * @var boolean
     *
     * @ORM\Column(name="expense_claims", type="integer", nullable=false)
     */
    private $expenseClaims;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enable_payments", type="integer", nullable=false)
     */
    private $enablePayments;

    /**
     * @var \XeroCodeType
     *
     * @ORM\ManyToOne(targetEntity="XeroCodeType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="xero_code_type_id", referencedColumnName="id")
     * })
     */
    private $xeroCodeType;

    /**
     * @var \XeroCoaVersion
     *
     * @ORM\ManyToOne(targetEntity="XeroCoaVersion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="xero_coa_version_id", referencedColumnName="id")
     * })
     */
    private $xeroCoaVersion;

    /**
     * @var \XeroTaxRate
     *
     * @ORM\ManyToOne(targetEntity="XeroTaxRate")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="xero_tax_rate_id", referencedColumnName="id")
     * })
     */
    private $xeroTaxRate;

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
     * Set code
     *
     * @param string $code
     *
     * @return XeroAccountCode
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return XeroAccountCode
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return XeroAccountCode
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set dashboard
     *
     * @param boolean $dashboard
     *
     * @return XeroAccountCode
     */
    public function setDashboard($dashboard)
    {
        $this->dashboard = $dashboard;

        return $this;
    }

    /**
     * Get dashboard
     *
     * @return boolean
     */
    public function getDashboard()
    {
        return $this->dashboard;
    }

    /**
     * Set expenseClaims
     *
     * @param boolean $expenseClaims
     *
     * @return XeroAccountCode
     */
    public function setExpenseClaims($expenseClaims)
    {
        $this->expenseClaims = $expenseClaims;

        return $this;
    }

    /**
     * Get expenseClaims
     *
     * @return boolean
     */
    public function getExpenseClaims()
    {
        return $this->expenseClaims;
    }

    /**
     * Set enablePayments
     *
     * @param boolean $enablePayments
     *
     * @return XeroAccountCode
     */
    public function setEnablePayments($enablePayments)
    {
        $this->enablePayments = $enablePayments;

        return $this;
    }

    /**
     * Get enablePayments
     *
     * @return boolean
     */
    public function getEnablePayments()
    {
        return $this->enablePayments;
    }

    /**
     * Set xeroCodeType
     *
     * @param \UtilBundle\Entity\XeroCodeType $xeroCodeType
     *
     * @return XeroAccountCode
     */
    public function setXeroCodeType(\UtilBundle\Entity\XeroCodeType $xeroCodeType = null)
    {
        $this->xeroCodeType = $xeroCodeType;

        return $this;
    }

    /**
     * Get xeroCodeType
     *
     * @return \UtilBundle\Entity\XeroCodeType
     */
    public function getXeroCodeType()
    {
        return $this->xeroCodeType;
    }

    /**
     * Set xeroCoaVersion
     *
     * @param \UtilBundle\Entity\XeroCoaVersion $xeroCoaVersion
     *
     * @return XeroAccountCode
     */
    public function setXeroCoaVersion(\UtilBundle\Entity\XeroCoaVersion $xeroCoaVersion = null)
    {
        $this->xeroCoaVersion = $xeroCoaVersion;

        return $this;
    }

    /**
     * Get xeroCoaVersion
     *
     * @return \UtilBundle\Entity\XeroCoaVersion
     */
    public function getXeroCoaVersion()
    {
        return $this->xeroCoaVersion;
    }

    /**
     * Set xeroTaxRate
     *
     * @param \UtilBundle\Entity\XeroTaxRate $xeroTaxRate
     *
     * @return XeroAccountCode
     */
    public function setXeroTaxRate(\UtilBundle\Entity\XeroTaxRate $xeroTaxRate = null)
    {
        $this->xeroTaxRate = $xeroTaxRate;

        return $this;
    }

    /**
     * Get xeroTaxRate
     *
     * @return \UtilBundle\Entity\XeroTaxRate
     */
    public function getXeroTaxRate()
    {
        return $this->xeroTaxRate;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return XeroAccountCode
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
     * @return XeroAccountCode
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
